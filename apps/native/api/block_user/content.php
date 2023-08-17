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
    $user_id   = fetch_or_get($_POST['user_id'], false); 
    $user_data = cl_raw_user_data($user_id);

    if (empty($user_data) || $user_id == $me['id']) {
        $data['code']    = 400;
        $data['message'] = "User id is missing or invalid";
        $data['data']    = array();
    }

    else {
        if (cl_is_blocked($me['id'], $user_id)) {
            $data['message'] = "User profile unblocked successfully";

            cl_db_delete_item(T_BLOCKS, array(
                'user_id'    => $me['id'],
                'profile_id' => $user_id
            ));
        }

        else{
            $data['message'] = "User profile blocked successfully";
            $insert_id       = cl_db_insert(T_BLOCKS, array(
                'user_id'    => $me['id'],
                'profile_id' => $user_id,
                'time'       => time()
            ));

            if (cl_is_following($me['id'], $user_id)) {
                cl_unfollow($me['id'], $user_id);
                cl_follow_decrease($me['id'], $user_id);
            }

            if (cl_is_following($user_id, $me['id'])) {
                cl_unfollow($user_id, $me['id']);
                cl_follow_decrease($user_id, $me['id']);
            }
        }

        $data['code'] = 200;       
        $data['data'] = array();
    }
}