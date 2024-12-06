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

require_once(cl_full_path("core/apps/explore/app_ctrl.php"));

$cl["page_title"]   = cl_translate("Explore");
$cl["page_desc"]    = $cl["config"]["description"];
$cl["page_kw"]      = $cl["config"]["keywords"];
$cl["pn"]           = "explore";
$cl["sbr"]          = true;
$cl["sbl"]          = true;
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

else if($cl["page_tab"] == 'categories') {
    // echo json_encode(cl_search_people($cl["search_query"], false, 30));die;
    $data = $db->rawQuery("SELECT * FROM `cl_categories`  WHERE `parent_id` IS NULL order by sort asc");
    
    foreach($data as $k => $cat){
        $childs = $db->rawQuery("SELECT * FROM `cl_categories` WHERE `parent_id` = " . $cat['id']);   
        $data[$k]['childs'] = $childs;
    }
    $cl["query_result"]  = $data;
    // echo json_encode($data);die;
// 	$cl["query_result"] = cl_search_people($cl["search_query"], false, 30);
}

else {
    
	$cl["query_result"] = cl_search_posts($cl["search_query"], false, 30);

    //echo "<pre>";print_r($cl["query_result"]);exit("Okay");
}

$cl["http_res"] = cl_template("explore/content");