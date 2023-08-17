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
	$data["code"]    = 200;
	$data["valid"]   = true;
	$data["message"] = "";
	$data["data"]    = array(
		"profile_visibility" => $me["profile_privacy"],
		"contact_privacy"    => $me["contact_privacy"],
		"search_visibility"  => (($me["index_privacy"] == "Y") ? true : false)
	);
}