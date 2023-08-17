<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                           @
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                   @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2021 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

if (empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 401,
		'data'    => array(),
		'message' => 'Unauthorized Access'
	);
}
else {
	$user_id = fetch_or_get($_POST['user_id'], 0);
    $udata   = cl_raw_user_data($user_id);

    if (not_empty($udata) && $me['id'] != $user_id) {
        if (cl_is_blocked($me['id'], $user_id) != true && cl_is_blocked($user_id, $me['id']) != true) {
            if (cl_is_following($me['id'], $user_id)) {
                
                cl_unfollow($me['id'], $user_id);

                cl_db_delete_item(T_NOTIFS, array(
                    'notifier_id'  => $me['id'],
                    'recipient_id' => $user_id,
                    'subject'      => 'subscribe',
                    'entry_id'     => $me['id']
                ));

                $data["code"]    = 200;
                $data["message"] = "Subscription canceled successfully";
                $data["data"]    = array(
                    "follow"     => false
                );

                cl_follow_decrease($me['id'], $user_id);
            }

            else{

                if ($udata["follow_privacy"] == "everyone") {

                    cl_follow($me['id'], $user_id);

                    cl_notify_user(array(
                        'subject'  => 'subscribe',
                        'user_id'  => $user_id,
                        'entry_id' => $me['id']
                    ));

                    cl_follow_increase($me['id'], $user_id);

                    $data["code"]    = 200;
                    $data["message"] = "Subscribed successfully";
                    $data["data"]    = array(
                        "follow"     => true
                    );
                }

                else {
                    if (cl_follow_requested($me['id'], $user_id)) {
                        if (cl_unfollow($me['id'], $user_id)) {
                            cl_db_delete_item(T_NOTIFS, array(
                                'notifier_id'  => $me['id'],
                                'recipient_id' => $user_id,
                                'subject'      => 'subscribe',
                                'entry_id'     => $user_id
                            ));

                            cl_db_delete_item(T_NOTIFS, array(
                                'notifier_id'  => $me['id'],
                                'recipient_id' => $user_id,
                                'subject'      => 'subscribe_request',
                                'entry_id'     => $user_id
                            ));

                            $data["code"]    = 200;
                            $data["message"] = "Subscription request canceled successfully";
                            $data["data"]    = array(
                                "follow"     => false
                            );
                        }
                    }
                    else{
                        if (cl_follow_request($me['id'], $user_id)) {
                            cl_notify_user(array(
                                'subject'  => 'subscribe_request',
                                'user_id'  => $user_id,
                                'entry_id' => $me["id"]
                            ));

                            $data["code"]    = 201;
                            $data["message"] = "Subscription request sent successfully";
                            $data["data"]    = array(
                                "follow"     => false
                            );
                        }
                    }
                }
            }
        }

        else {
            $data['code']    = 400;
            $data['message'] = "User ID is missing or invalid";
            $data['data']    = array();
        }
    }
    else {
        $data['code']    = 400;
        $data['message'] = "User ID is missing or invalid";
        $data['data']    = array();
    }
}