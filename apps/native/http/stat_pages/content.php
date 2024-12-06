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

$page = fetch_or_get($_GET['page'], 'terms');

if (in_array($page, array('terms','privacy_policy','cookies_policy','about_us','faqs')) != true) {
	cl_redirect("404");
}

$page_titles         = array(
	'terms'          => cl_translate('Terms of Use'), 
	'privacy_policy' => cl_translate('Privacy policy'), 
	'cookies_policy' => cl_translate('Cookies policy'), 
	'about_us'       => cl_translate('About us'), 
	'faqs'           => "F.A.Qs"
);

$cl["page_title"] = $page_titles[$page];
$cl["page_desc"]  = $cl["config"]["description"];
$cl["page_kw"]    = $cl["config"]["keywords"];
$cl["pn"]         = "stat_pages";
$cl["sbr"]        = true;
$cl["sbl"]        = true;
$cl["http_res"]   = cl_template(cl_strf("%s/content",$page));


