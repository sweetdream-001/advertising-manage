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

if (not_empty($cl['is_logged'])) {
	cl_redirect("home");
}

else {
	require_once(cl_full_path("core/apps/explore/app_ctrl.php"));
	require_once(cl_full_path("core/apps/feed/app_ctrl.php"));
	$cl["right_sidebar"]  = cl_template('main/guest_sidebar');
	$cl["page_title"] = cl_translate("Feed");
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "feed";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["feed"]       = cl_get_guest_feed(false, 30);
	$cl["search_query"] = fetch_or_get($_GET['q'], "");
$cl["page_tab"]     = fetch_or_get($_GET['tab'], "posts");
$cl["query_result"] = array();

if (not_empty($cl["search_query"])) {
	$cl["search_query"] = cl_text_secure($cl["search_query"]);
	$cl["page_title"]   = $cl["search_query"];
	$cl["search_query"] = cl_croptxt($cl["search_query"], 32);
}

if ($cl["page_tab"] == 'htags') {
	$cl["query_result"] = cl_search_hashtags($cl["search_query"], false, 30);
}

else if($cl["page_tab"] == 'people') {
	$cl["query_result"] = cl_search_people($cl["search_query"], false, 30);
}

else {
	$cl["query_result"] = cl_search_posts($cl["search_query"], false, 30);
}
	$cl["http_res"]   = cl_template("feed/content");
}