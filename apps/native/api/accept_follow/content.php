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

	$req_id = fetch_or_get($_POST['req_id'], 0);
    $req_id = (is_posnum($req_id)) ? $req_id : 0;

    $req_data = cl_db_get_item(T_CONNECTIONS, array(
        "id" => $req_id
    ));

    if (not_empty($req_data)) {
        $udata = cl_raw_user_data($req_data["follower_id"]);

        if (not_empty($udata)) {
            cl_update_user_data($req_data["follower_id"], array(
                'following' => ($udata['following'] += 1)
            ));

            cl_update_user_data($me['id'], array(
                'followers' => ($me['followers'] += 1)
            ));

            cl_notify_user(array(
                'subject'  => 'subscribe_accept',
                'user_id'  => $req_data["follower_id"],
                'entry_id' => $me["id"]
            ));

            cl_db_update(T_CONNECTIONS, array(
                "id" => $req_id
            ), array(
                "status" => "active"
            ));

            $data['message'] = "Subscription request successfully accepted";
            $data['code']    = 200;
            $data['data']    = array(
                "total" => cl_get_follow_requests_total()
            );
        }
        else{
            $data['code']    = 400;
            $data['message'] = "Follow request ID is missing or invalid";
            $data['data']    = array();
        }
    }
    else{
        $data['code']    = 400;
        $data['message'] = "Follow request ID is missing or invalid";
        $data['data']    = array();
    }
}