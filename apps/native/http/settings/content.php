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

$cl["page_title"]    = cl_translate("Account settings");
$cl["page_desc"]     = $cl["config"]["description"];
$cl["page_kw"]       = $cl["config"]["keywords"];
$cl["pn"]            = "settings";
$cl["sbr"]           = true;
$cl["sbl"]           = true;
$cl["blocked_users"] = cl_get_blocked_users();
$cl["settings_app"]  = fetch_or_get($_GET["sapp"], false);
$cl["settings_app"]  = (not_empty($cl["settings_app"])) ? cl_text_secure($cl["settings_app"]) : 0;
$cl["settings_apps"] = array("name", "email", "siteurl", "bio", "gender", "password", "language", "country", "city", "verification", "privacy", "notifications", "blocked", "delete", "information", "email_notifs");

if (not_empty($cl["settings_app"]) && in_array($cl["settings_app"], $cl["settings_apps"])) {

	if ($cl["settings_app"] == "email_notifs" && $cl["config"]["email_notifications"] == "off") {
		cl_redirect("404");
	}

	else if ($cl["settings_app"] == "premium" && $cl["config"]["prem_account_system_status"] == "off") {
		cl_redirect("404");
	}
	else if ($cl["settings_app"] == "blue" && $cl["config"]["blue_account_system_status"] == "off") {
		cl_redirect("404");
	}
	else{
		$cl["http_res"] = cl_template(cl_strf("settings/includes/%s", $cl["settings_app"]));
	}
	
}

else{
	$cl["http_res"] = cl_template("settings/content");
}


