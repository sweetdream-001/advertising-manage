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

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

$profile_id = fetch_or_get($_GET["user_id"], false);

if (empty($profile_id) || is_posnum($profile_id) != true) {
	$data['code']    = 400;
    $data['message'] = "User ID is missing or invalid";
    $data['data']    = array();
}
else {
	$user_data = cl_raw_user_data($profile_id);

	if (not_empty($user_data)) {
		if (cl_can_view_profile($profile_id)) {

			$offset   = fetch_or_get($_GET['offset'], null);
		    $offset   = (is_posnum($offset)) ? $offset : null;
		    $limit    = fetch_or_get($_GET['page_size'], null);
		    $limit    = (is_posnum($limit)) ? $limit: null;
		    $users_ls = cl_get_followers($profile_id, $limit, $offset);

		    if (not_empty($users_ls)) {
		    	$data["code"]    = 200;
				$data["message"] = "Followers fetched successfully";
				$data["data"]    = $users_ls;
		    }

		    else {
		    	$data['code']    = 204;
		        $data['message'] = "No data found";
		        $data['data']    = array();
		    }
		}

		else {
			$data['code']    = 400;
		    $data['message'] = "This profile data is not available for viewing";
		    $data['data']    = array();
		}
	}

	else {
		$data['code']    = 404;
        $data['message'] = "User with this ID does not exist";
        $data['data']    = array();
	}
}