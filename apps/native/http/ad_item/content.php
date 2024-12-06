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

$ad_id   = fetch_or_get($_GET["ad_id"], false);
$ad_item = cl_get_timeline_ads($ad_id);

if (empty($ad_item)) {
	cl_redirect("404");
}

else {
	$cl["page_title"] = cl_translate('Advertisement');
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "ad_item";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["ads_ls"]     = array($ad_item);
	$cl["http_res"]   = cl_template("ad_item/content");
}