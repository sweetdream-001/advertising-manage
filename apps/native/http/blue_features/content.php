<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                                    @
# @ Author_url 1: https://jooj.us                                           @
# @ Author_url 2: http://jooj.us/twitter-clone                              @
# @ Author E-mail: sales@jooj.us                                            @
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

else if($cl['config']['blue_account_system_status'] != 'on' || $me["is_blue"] != 1) {
	cl_redirect("404");
}

else{
	$cl["page_title"] = cl_translate("Blue features");
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "blue_features";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["http_res"]   = cl_template("blue_features/content");
}

