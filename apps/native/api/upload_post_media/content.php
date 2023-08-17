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
    $post_data  = $me['draft_post'];
    $media_type = fetch_or_get($_POST["type"], false);

    if (empty($media_type) || in_array($media_type, array("image", "video")) != true) {
    	$data['code']    = 400;
        $data['message'] = "Media file type is missing or invalid";
    	$data['data']    = array();
    }

    else {
    	if ($media_type == "image") {
		    if (not_empty($_FILES['file']) && not_empty($_FILES['file']['tmp_name'])) {
		        if (empty($post_data)) {
		            $post_id   = cl_create_orphan_post($me['id'], "image");
		            $post_data = cl_get_orphan_post($post_id);

		            cl_update_user_data($me['id'], array(
		                'last_post' => $post_id
		            ));
		        }
		        
		        if (not_empty($post_data) && $post_data["type"] == "image") {
		            if (empty($post_data['media']) || count($post_data['media']) < 10) {
		                $file_info      =  array(
		                    'file'      => $_FILES['file']['tmp_name'],
		                    'size'      => $_FILES['file']['size'],
		                    'name'      => $_FILES['file']['name'],
		                    'type'      => $_FILES['file']['type'],
		                    'file_type' => 'image',
		                    'folder'    => 'images',
		                    'slug'      => 'original',
		                    'crop'      => array('width' => 300, 'height' => 300),
		                    'allowed'   => 'jpg,png,jpeg,gif'
		                );

		                $file_upload = cl_upload($file_info);

		                if (not_empty($file_upload['filename'])) {
		  
		                    $img_id      = cl_db_insert(T_PUBMEDIA, array(
		                        "pub_id" => $post_data["id"],
		                        "type"   => "image",
		                        "src"    => $file_upload['filename'],
		                        "time"   => time(),
		                        "json_data" => json(array(
		                            "image_thumb" => $file_upload['cropped']
		                        ),true)
		                    ));

		                    if (is_posnum($img_id)) {
		                    	$data['message'] = 'Media file uploaded successfully';
		                    	$data['code']    = 200;
		                        $data['data']    = array(
		                        	"media_id"   => $img_id, 
		                        	"url"        => cl_get_media($file_upload['cropped']),
		                        	"type"       => "Image"
		                        );
		                    }
		                }
		                else {
		                	$data['code']    = 400;
					        $data['message'] = "Something went wrong while saving a uploaded media file. Please check your details and try again";
					    	$data['data']    = array();
		                }
		            }
		            else {
		                $data['code']    = 400;
				        $data['message'] = "You cannot attach more than 10 images to a post";
				    	$data['data']    = array();
		            }
		        }
		        else {
		            cl_delete_orphan_posts($me['id']);
		            cl_update_user_data($me['id'],array(
		                'last_post' => 0
		            ));

		            $data['code']    = 500;
			        $data['message'] = "An error occurred while processing your request. Please try again later.";
			    	$data['data']    = array();
		        }
		    }
		    else {
		    	$data['code']    = 500;
		        $data['message'] = "Media file is missing or invalid";
		    	$data['data']    = array();
		    }
    	}

    	else if($media_type == "video") {
	    	if (not_empty($_FILES['file']) && not_empty($_FILES['file']['tmp_name'])) {
	            if (empty($post_data)) {
	                $post_id   = cl_create_orphan_post($me['id'], "video");
	                $post_data = cl_get_orphan_post($post_id);

	                cl_update_user_data($me['id'],array(
	                    'last_post' => $post_id
	                ));
	            }

	            if (not_empty($post_data) && $post_data["type"] == "video") {
	                if (empty($post_data['media'])) {
	                    $file_info      =  array(
	                        'file'      => $_FILES['file']['tmp_name'],
	                        'size'      => $_FILES['file']['size'],
	                        'name'      => $_FILES['file']['name'],
	                        'type'      => $_FILES['file']['type'],
	                        'file_type' => 'video',
	                        'folder'    => 'videos',
	                        'slug'      => 'original',
	                        'allowed'   => 'mp4,mov,3gp,webm',
	                    );

	                    $file_upload = cl_upload($file_info);
	                    $upload_fail = false;
	                    $post_id     = $post_data['id'];

	                    if (not_empty($file_upload['filename'])) {
	                        try {
	                            require_once(cl_full_path("core/libs/ffmpeg-php/vendor/autoload.php"));

	                            $ffmpeg         = new FFmpeg(cl_full_path($config['ffmpeg_binary']));
	                            $thumb_path     = cl_gen_path(array(
	                                "folder"    => "images",
	                                "file_ext"  => "jpeg",
	                                "file_type" => "image",
	                                "slug"      => "poster",
	                            ));

	                            $ffmpeg->input($file_upload['filename']);
	                            $ffmpeg->set('-ss','3');
	                            $ffmpeg->set('-vframes','1');
	                            $ffmpeg->set('-f','mjpeg');
	                            $ffmpeg->output($thumb_path)->ready();
	                        } 

	                        catch (Exception $e) {
	                            $upload_fail = true;
	                        }

	                        if (empty($upload_fail)) {
	                            $vid_id      = cl_db_insert(T_PUBMEDIA, array(
	                                "pub_id" => $post_id,
	                                "type"   => "video",
	                                "src"    => $file_upload['filename'],
	                                "time"   => time(),
	                                "json_data" => json(array(
	                                    "poster_thumb" => $thumb_path
	                                ),true)
	                            ));

	                            if (is_posnum($vid_id)) {
	                                $data['message'] = 'Media file uploaded successfully';
			                    	$data['code']    = 200;
			                        $data['data']    = array(
			                        	"media_id"   => $vid_id, 
			                        	"type"       => "Video",
			                        	"source"     => cl_get_media($file_upload['filename']),
	                                    "poster"     => cl_get_media($thumb_path),
			                        );
	                            }
	                        }

	                        else {
			                	$data['code']    = 400;
						        $data['message'] = "Something went wrong while saving a uploaded media file. Please check your details and try again";
						    	$data['data']    = array();
			                }
	                    }
	                }
	                else {
		                $data['code']    = 400;
				        $data['message'] = "You cannot attach more than 1 video to a post";
				    	$data['data']    = array();
	                }
	            }
	            else {
	                cl_delete_orphan_posts($me['id']);
	                cl_update_user_data($me['id'], array(
	                    'last_post' => 0
	                ));
	            }
	        }

	        else {
		    	$data['code']    = 500;
		        $data['message'] = "Media file is missing or invalid";
		    	$data['data']    = array();
		    }
    	}
    }
}