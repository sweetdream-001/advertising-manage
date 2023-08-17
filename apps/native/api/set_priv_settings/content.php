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
	$profile_privacy = fetch_or_get($_POST['profile_visibility'], null);
    $contact_privacy = fetch_or_get($_POST['contact_privacy'], null);
    $index_privacy   = fetch_or_get($_POST['search_visibility'], null);
    $follow_privacy  = fetch_or_get($_POST['follow_privacy'], null);

    if (in_array($profile_privacy, array('everyone', 'followers')) != true) {
        $data["code"]    = 400;
        $data["data"]    = array();
        $data["message"] = "Invalid request data";
    }

    else if (in_array($contact_privacy, array('everyone', 'followed')) != true) {
        $data["code"]    = 400;
        $data["data"]    = array();
        $data["message"] = "Invalid request data";
    }

    else if (in_array($index_privacy, array('Y', 'N')) != true) {
        $data["code"]    = 400;
        $data["data"]    = array();
        $data["message"] = "Invalid request data";
    }

    else if (in_array($follow_privacy, array('everyone', 'approved')) != true) {
        $data["code"]    = 400;
        $data["data"]    = array();
        $data["message"] = "Invalid request data";
    }

    else {
        cl_update_user_data($me["id"], array(
            'profile_privacy' => $profile_privacy,
            'contact_privacy' => $contact_privacy,
            'follow_privacy'  => $follow_privacy,
            'index_privacy'   => $index_privacy
        ));

        $data["code"]    = 200;
		$data["valid"]   = true;
		$data["message"] = "User privacy settings updated";
		$data["data"]    = array();

        cl_db_update(T_PUBS, array(
            "user_id" => $me["id"],
            "status"  => "active"
        ), array(
            "priv_wcs" => $profile_privacy
        ));
    }
}