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

// RESTRICT USER CODE
$visitor_ip = $_SERVER['REMOTE_ADDR'];
cl_check_ip_restriction($visitor_ip);

if (empty($cl['is_logged'])) {
	cl_redirect("guest");
}

else {
	require_once(cl_full_path("core/apps/bookmarks/app_ctrl.php"));

	$cl["page_title"] = cl_translate("Bookmarks");
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "bookmarks";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["bookmarks"]  = cl_get_bookmarks($me['id'], 30);
	$cl["http_res"]   = cl_template("bookmarks/content");
}