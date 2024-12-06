<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                           @
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                    @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

// RESTRICT USER CODE
$visitor_ip = $_SERVER['REMOTE_ADDR'];
cl_check_ip_restriction($visitor_ip);

if (empty($cl["is_logged"])) {
	cl_redirect("404");
}

else if($me["start_up"] == "done") {
	cl_redirect("home");
}

else {
	$rand_number = rand(1, 15);

	$user_update_data = array(
		"cover" => cl_strf("upload/default/cover-%d.png", $rand_number),
		"cover_orig" => cl_strf("upload/default/cover-%d.png", $rand_number)
	);

	if ($me["logged_in_with"] == "system") {
		$user_update_data["avatar"] = cl_strf("upload/default/avatar-%d.png", $rand_number);
		$me["avatar"] = cl_get_media(cl_strf("upload/default/avatar-%d.png", $rand_number));
	}

	cl_update_user_data($me["id"], $user_update_data);

	
	$cl["page_title"]  = cl_translate("Completion of registration");
	$cl["page_desc"]   = $cl["config"]["description"];
	$cl["page_kw"]     = $cl["config"]["keywords"];
	$cl["pn"]          = "start_up";
	$cl["sbr"]         = true;
	$cl["sbl"]         = true;
	$cl["suggestions"] = cl_get_follow_suggestions(20);;
	$cl["http_res"]    = cl_template("start_up/content");
}