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
	$cl["feed"]       = cl_get_guest_feed(false, 3000);
	$cl["tl_feed_total"] = 3000;
	$cl["search_query"] = fetch_or_get($_GET['q'], "");
// 	echo var_dump(fetch_or_get($_GET['tab'], "posts"));die;
$cl["page_tab"]     = fetch_or_get($_GET['tab'], "posts");
$cl["query_result"] = array();
// echo json_encode($cl);die;
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
    
	$cl["query_result"] = cl_search_posts($cl["search_query"], false, 3000);
	//echo "<pre>";print_r($cl["query_result"]);exit("OK");	

// 	echo json_encode($cl["query_result"]);die;
}
	$cl["http_res"]   = cl_template("feed/content");
}