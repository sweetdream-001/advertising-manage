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
	require_once(cl_full_path("core/apps/chat/app_ctrl.php"));

	$send_to     = fetch_or_get($_GET["user_id"], false);
	$offset_up   = fetch_or_get($_GET["offset_up"], false);
	$offset_down = fetch_or_get($_GET["offset_down"], false);
	$user_data   = cl_raw_user_data($send_to);
	$limit       = fetch_or_get($_GET['page_size'], null);
    $limit       = (is_posnum($limit)) ? $limit: 20;

	if (empty($user_data) || $send_to == $me["id"] || cl_is_blocked($send_to, $me['id']) || cl_is_blocked($me['id'], $send_to)) {
		$data['code']    = 400;
        $data['message'] = "Interlocutor ID is missing or invalid";
    	$data['data']    = array();
	}

	else {
		if (not_empty($offset_up)) {
			$messages_ls    = cl_get_conversation(array(
				'user_one'  => $me['id'],
				'user_two'  => $send_to,
				'limit'     => $limit,
				'offset'    => $offset_up,
				'order'     => 'DESC',
				'offset_to' => 'lt'
			));

			if (not_empty($messages_ls)) {
				$data["code"] = 200;
				$data["data"] = array_reverse($messages_ls);
			}

			else {
				$data['code']    = 204;
		        $data['message'] = "No data found";
		        $data['data']    = array();
			}
		}

		else {
			$offset_down    = ((is_posnum($offset_down)) ? $offset_down : 0);
			$messages_ls    = cl_get_conversation(array(
				'user_one'  => $me['id'],
				'user_two'  => $send_to,
				'limit'     => $limit,
				'offset'    => $offset_down,
				'order'     => 'DESC',
				'offset_to' => 'gt'
			));

			if (not_empty($messages_ls)) {
				$data["code"] = 200;
				$data["data"] = array_reverse($messages_ls);
			}

			else {
				$data['code']    = 204;
		        $data['message'] = "No data found";
		        $data['data']    = array();
			}
		}
	}
}