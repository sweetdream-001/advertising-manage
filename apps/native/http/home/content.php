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

if (empty($cl["is_logged"])) {
	if ($cl["config"]["guest_page_status"] == "on") {
		cl_redirect("guest");
	}
	else{
		cl_redirect("feed");
	}
}
else {
	require_once(cl_full_path("core/apps/home/app_ctrl.php"));
	require_once(cl_full_path("core/apps/feed/app_ctrl.php"));
	$cl["app_statics"] = array(
		"scripts" => array(
			cl_js_template("statics/js/libs/SwiperJS/swiper-bundle.min")
		)
	);

	$cl["page_title"]    = cl_translate("Homepage");
	$cl["page_desc"]     = $cl["config"]["description"];
	$cl["page_kw"]       = $cl["config"]["keywords"];
	$cl["pn"]            = "home";
	$cl["sbr"]           = true;
	$cl["sbl"]           = true;
	$cl["tl_feed"]       = cl_get_timeline_feed(30);
	$cl["feed"]       = cl_get_guest_feed(false, 30);
	$cl["tl_feed_total"] = 30;
	$cl["tl_swifts"]     = cl_timeline_swifts();
	$cl["http_res"]      = cl_template("home/content");
}