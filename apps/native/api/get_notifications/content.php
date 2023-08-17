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

	require_once(cl_full_path("core/apps/notifications/app_ctrl.php"));

	$type   = fetch_or_get($_GET['type'], null);
	$offset = fetch_or_get($_GET['offset'], null);
	$offset = (is_posnum($offset)) ? $offset : null;
	$limit  = fetch_or_get($_GET['page_size'], 15);
	$limit  = (is_posnum($limit)) ? $limit: 15;

	if (in_array($type, array('notifs', 'mentions'))) {
		$notifs_list = cl_get_notifications(array(
            "type"   => $type,
            "offset" => $offset,
            "limit"  => $limit
        ));

		if(not_empty($notifs_list)) {
			$data["code"]    = 200;
			$data["message"] = "Fetched successfully";
			$data["data"]    = $notifs_list;
		}

		else {
			$data['code']    = 404;
		    $data['message'] = "No data found";
			$data['data']    = array();
		}
	}
	else {
		$data['code']    = 400;
	    $data['message'] = "The type of notification is missing or invalid. Please check your details";
	    $data['data']    = array();
	}
}