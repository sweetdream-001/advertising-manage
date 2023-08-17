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

require_once(cl_full_path("core/apps/search/app_ctrl.php"));

if ($action == 'load_more') {
	$data['err_code'] = "0";
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $type             = fetch_or_get($_GET['type'], null);
    $search_query     = fetch_or_get($_GET['q'], null);
    $query_result     = array();
    $html_arr         = array();

    if (is_posnum($offset)) {  	
    	if ($type == "htags") {
            if (not_empty($search_query)) {
                $search_query = cl_text_secure($search_query);
                $search_query = cl_croptxt($search_query, 32);
            }

            $query_result = cl_search_hashtags($search_query, $offset, 15);
            
            if (not_empty($query_result)) {
                foreach ($query_result as $cl['li']) {
                    $html_arr[] = cl_template('search/includes/li/htag_li');
                }

                $data['status'] = 200;
                $data['html']   = implode("", $html_arr);
            }  
        }

        else if($type == "people") {
            if (not_empty($search_query)) {
                $search_query = cl_text_secure($search_query);
                $search_query = cl_croptxt($search_query, 32);
            }

            $query_result = cl_search_people($search_query, $offset, 15);

            if (not_empty($query_result)) {
                foreach ($query_result as $cl['li']) {
                    $html_arr[] = cl_template('search/includes/li/people_li');
                }

                $data['status'] = 200;
                $data['html']   = implode("", $html_arr);
            } 
        }

        else if($type == "posts") {
            if (not_empty($search_query)) {
                $search_query = cl_text_secure($search_query);
                $search_query = cl_croptxt($search_query, 32);
            }

            $query_result = cl_search_posts($search_query, $offset, 15);

            if (not_empty($query_result)) {
                foreach ($query_result as $cl['li']) {
                    $html_arr[] = cl_template('timeline/post');
                }

                $data['status'] = 200;
                $data['html']   = implode("", $html_arr);
            } 
        }
    }
}

else if($action == 'search') {
    $data['err_code'] = "0";
    $data['status']   = 400;
    $type             = fetch_or_get($_GET['type'], null);
    $search_query     = fetch_or_get($_GET['q'], null);
    $query_result     = array();
    $html_arr         = array();

    if (not_empty($search_query) && len($search_query) >= 2) {
        if ($type == "htags") {
            $search_query = cl_text_secure($search_query);
            $search_query = cl_croptxt($search_query, 32);
            $query_result = cl_search_hashtags($search_query, false, 15);
            
            if (not_empty($query_result)) {
                foreach ($query_result as $cl['li']) {
                    $html_arr[] = cl_template('search/includes/li/htag_li');
                }

                $data['status'] = 200;
                $data['html']   = implode("", $html_arr);
            }  
        }

        else if($type == "people") {
            $search_query = cl_text_secure($search_query);
            $search_query = cl_croptxt($search_query, 32);
            $query_result = cl_search_people($search_query, false, 15);

            if (not_empty($query_result)) {
                foreach ($query_result as $cl['li']) {
                    $html_arr[] = cl_template('search/includes/li/people_li');
                }

                $data['status'] = 200;
                $data['html']   = implode("", $html_arr);
            } 
        }

        else if($type == "posts") {
            $search_query = cl_text_secure($search_query);
            $search_query = cl_croptxt($search_query, 32);
            $query_result = cl_search_posts($search_query, false, 15);

            if (not_empty($query_result)) {
                foreach ($query_result as $cl['li']) {
                    $html_arr[] = cl_template('timeline/post');
                }

                $data['status'] = 200;
                $data['html']   = implode("", $html_arr);
            } 
        }
    }
}