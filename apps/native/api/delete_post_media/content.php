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

if (empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 401,
		'data'    => array(),
		'message' => 'Unauthorized Access'
	);
}

else {
	$media_id   = fetch_or_get($_POST['media_id'], 0);
    $post_data  = $me['draft_post'];
    $media_type = fetch_or_get($_POST['type'], false);

    if (empty($media_type) || in_array($media_type, array("image", "video")) != true) {
    	$data['code']    = 400;
        $data['message'] = "Media file type is missing or invalid";
    	$data['data']    = array();
    }
    else if(empty($post_data)) {
    	$data['code']    = 500;
        $data['message'] = "An error occurred while processing your request. Please try again later.";
    	$data['data']    = array();
    }
    else if(is_posnum($media_id) != true) {
    	$data['code']    = 400;
        $data['message'] = "Media file ID is missing or invalid";
    	$data['data']    = array();
    }
    else {
	    if ($media_type == "image") {
	    	$data['code']    = 200;
	    	$data['data']    = array();
	    	$data['message'] = "Media deleted successfully";
    		$post_id         = $post_data['id'];
	        $media_data      = cl_db_get_item(T_PUBMEDIA, array(
	        	"id"         => $media_id,
	        	"type"       => $media_type,
	        	"pub_id"     => $post_id
	        ));

	        if (not_empty($media_data)) {

	        	cl_delete_media($media_data['src']);

	        	cl_db_delete_item(T_PUBMEDIA, array(
		        	"id"     => $media_id,
		        	"type"   => $media_type,
		        	"pub_id" => $post_id
		        ));

	        	$media_data["json_data"] = json($media_data["json_data"]);

	        	if (not_empty($media_data['json_data']['image_thumb'])) {
                    cl_delete_media($media_data['json_data']['image_thumb']);
                }

                if (count($post_data['media']) < 2) {
	                cl_delete_orphan_posts($me['id']);
	                cl_update_user_data($me['id'], array(
	                    'last_post' => 0
	                ));
	            }
	        }
    	}
    	else if($media_type == "video") {
    		$data['code']    = 200;
	    	$data['data']    = array();
	    	$data['message'] = "Media deleted successfully";

	    	cl_delete_orphan_posts($me['id']);
	        cl_update_user_data($me['id'], array(
	            'last_post' => 0
	        ));
    	}
    }
}