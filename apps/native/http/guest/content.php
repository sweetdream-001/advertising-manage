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

if (not_empty($cl["is_logged"])) {
	cl_redirect("home");
}

require_once(cl_full_path("core/apps/guest/app_ctrl.php"));

$cl["invite_code"] = fetch_or_get($_GET["invite_code"], false);
$cl["auth_type"]   = fetch_or_get($_GET["auth"], "login");
$cl["auth_type"]   = (in_array($cl["auth_type"], array("login", "signup", "forgot_pass", "reset_pass"))) ? $cl["auth_type"] : "login";
$cl["page_title"]  = cl_translate("welcome_back");
$cl["page_desc"]   = $cl["config"]["description"];
$cl["page_kw"]     = $cl["config"]["keywords"];
$cl["pn"]          = "guest";
$cl['em_code']     = ((not_empty($_GET['em_code']) && cl_verify_emcode($_GET['em_code'])) ? cl_text_secure($_GET['em_code']) : null);
$cl["sbr"]         = false;
$cl["sbl"]         = false;
$cl["slider_imgs"] = cl_get_slider_images();
$cl["invite_code_status"] = (not_empty($cl["invite_code"])) ? cl_verify_invite_code($cl["invite_code"]) : false;
$cl["http_res"] = cl_template("guest/content");
