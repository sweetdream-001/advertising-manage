<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)							@
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                   @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2021 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

$profile_id = fetch_or_get($_GET["user_id"], false);
$data_type  = fetch_or_get($_GET["type"], false);
$offset     = fetch_or_get($_GET['offset'], null);
$offset     = (is_posnum($offset)) ? $offset : null;
$limit      = fetch_or_get($_GET['page_size'], null);
$limit      = (is_posnum($limit)) ? $limit: null;

if (is_posnum($profile_id) != true) {
	$data['code']    = 400;
    $data['message'] = "User ID is missing or invalid";
    $data['data']    = array();
}
else {
	if (in_array($data_type, array("posts", "media", "likes")) != true) {
		$data_type = "posts";
	}

	if (cl_can_view_profile($profile_id)) { 	
    	if (in_array($data_type, array('posts', 'media'))) {

            $media_type = (($data_type == 'media') ? true : false);
            $posts_ls   = cl_get_profile_posts($profile_id, $limit, $media_type, $offset);

            if (not_empty($posts_ls)) {
                $data['code']    = 200;
		        $data['message'] = "Posts fetched successfully";
		        $data['data']    = array(
		        	'posts'      => $posts_ls
		        );
            }
            else {
            	$data['code']    = 204;
		        $data['message'] = "No data found";
		        $data['data']    = array();
            }
        }
        else {

            $posts_ls = cl_get_profile_likes($profile_id, $limit, $offset);

            if (not_empty($posts_ls)) {
                $data['code']    = 200;
		        $data['message'] = "Posts fetched successfully";
		        $data['data']    = array(
		        	'posts'      => $posts_ls
		        );
            }
            else {
            	$data['code']    = 204;
		        $data['message'] = "No data found";
		        $data['data']    = array();
            }
        }
    }
    else {
    	$data['code']    = 400;
	    $data['message'] = "This profile data is not available for viewing";
	    $data['data']    = array();
    }
}