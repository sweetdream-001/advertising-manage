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

	require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

	$offset   = fetch_or_get($_GET['offset'], null);
    $offset   = (is_posnum($offset)) ? $offset : null;
    $limit    = fetch_or_get($_GET['page_size'], null);
    $limit    = (is_posnum($limit)) ? $limit: null;
    $users_ls = cl_get_follow_requests($limit, $offset);

    if (not_empty($users_ls)) {
    	$data["code"]    = 200;
		$data["valid"]   = true;
		$data["message"] = "Follow requiests fetched successfully";
		$data["data"]    = $users_ls;
    }

    else {
    	$data['code']    = 204;
        $data['message'] = "No data found";
        $data['data']    = array();
    }
}