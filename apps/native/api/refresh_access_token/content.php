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

$token = fetch_or_get($_POST['refresh_token'], null);

if (empty($token) || len_between($token, 50, 250) != true) {
	$data['code']    = 400;
	$data['message'] = "Incorrect token value";
	$data['data']    = array();
}

else {
	$refresh_token = cl_text_secure($token);
	$user_data     = cl_db_get_item(T_USERS, array("refresh_token" => $refresh_token));

	if (empty($user_data) || $user_data["active"] != "1") {
		$data['code']    = 400;
		$data['message'] = "Invalid token value or user account disabled";
		$data['data']    = array();
	}
	else {

		cl_db_delete_item(T_SESSIONS, array(
			"user_id"  => $user_data["id"],
			"platform" => "mobile_android"
		));

		cl_db_delete_item(T_SESSIONS, array(
			"user_id"  => $user_data["id"],
			"platform" => "mobile_ios"
		));

		$data['code']     = 200;
		$data['message']  = "Token refreshed Out successful";
		$data_exp         = strtotime("+ 1 year");
		$session_id       = cl_create_user_session($user_data["id"], "mobile_android");
		$refresh_token    = md5(rand(11111, 99999)) . time() . md5(microtime() . $user_data["id"]);
		$data['data']     = array(
			"auth_token"        => $session_id,
            "refresh_token"     => $refresh_token,
        	"auth_token_expiry" => $data_exp,
		);

		cl_update_user_data($user_data["id"], array(
			"refresh_token" => $refresh_token
		));
	}
}