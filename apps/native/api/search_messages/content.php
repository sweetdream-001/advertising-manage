<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)							@
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
	$page_size   = fetch_or_get($_GET["page_size"], false);
	$offset_up   = fetch_or_get($_GET["offset_up"], false);
	$offset_down = fetch_or_get($_GET["offset_down"], false);
	$user_data   = cl_raw_user_data($send_to);
	$query       = ((not_empty($_GET['query'])) ? cl_croptxt($_GET['query'], 32) : "");
	$query       = ((len_between($query, 1, 32)) ? cl_text_secure($query) : "");
	$offset_to   = false;
	$offset_id   = false;
	$page_size   = (is_posnum($page_size)) ? $page_size : 0;

	if (not_empty($offset_up) && is_posnum($offset_up)) {
		$offset_id = $offset_up;
		$offset_to = "lt";
	}
	else if(not_empty($offset_down) && is_posnum($offset_down)) {
		$offset_id = $offset_down;
		$offset_to = "gt";
	}

	if (empty($user_data) || $send_to == $me["id"] || cl_is_blocked($send_to, $me['id']) || cl_is_blocked($me['id'], $send_to)) {
		$data['code']    = 400;
        $data['message'] = "Interlocutor ID is missing or invalid";
    	$data['data']    = array();
	}

	else {
		$total_matches  = 0;
		$search_result  = array();
		$messages       = cl_search_conversation(array(
			'user_one'  => $me['id'],
			'user_two'  => $send_to,
			'order'     => "DESC",
			'limit'     => $page_size,
			'offset'    => $offset_id,
			'offset_to' => $offset_to
		));

		if (not_empty($messages)) {
			if (not_empty($query)) {
				foreach ($messages as $row) {

					$row['message'] = cl_linkify_urls($row['message']);
					$query_matches  = preg_match_all("/{$query}/i", $row['message']);

					if (is_posnum($query_matches)) {
						$total_matches += $query_matches;

						array_push($search_result, $row);
					}
				}

				if (not_empty($total_matches)) {
					$data["code"]  = 200;
					$data["total"] = $total_matches;
					$data["data"]  = $search_result;
				}
				else {
					$data["code"]    = 404;
					$data["data"]    = array();
					$data["message"] = "No data found";
				}
			}

			else {
				$data["code"]  = 200;
				$data["total"] = count($messages);
				$data["data"]  = $messages;
			}
		}

		else {
			$data["code"]    = 404;
			$data["data"]    = array();
			$data["message"] = "No data found";
		}
	}
}