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
	if (not_empty($_FILES['avatar']) && not_empty($_FILES['avatar']['tmp_name'])) {
	    $file_info      =  array(
	        'file'      => $_FILES['avatar']['tmp_name'],
	        'size'      => $_FILES['avatar']['size'],
	        'name'      => $_FILES['avatar']['name'],
	        'type'      => $_FILES['avatar']['type'],
	        'file_type' => 'thumbnail',
	        'folder'    => 'avatars',
	        'slug'      => 'avatar',
	        'crop'      => array('width' => 512, 'height' => 512),
	        'allowed'   => 'jpg,png,jpeg,gif'
	    );

	    $file_upload = cl_upload($file_info);

	    if (not_empty($file_upload['cropped'])) {
	        $data['code']    = 200;
	        $data['message'] = "Avatar changed successfully";
	        $data['data']    = array(
	        	'avatar_url' => cl_get_media($file_upload['cropped'])
	        );

	        cl_delete_media($file_upload['filename']);
	        cl_delete_media($me['raw_avatar']);

	        cl_update_user_data($me['id'], array(
	            'avatar' => $file_upload['cropped']
	        ));
	    } 

	    else{
	        $data['code']    = 400;
	        $data['data']    = array();
	        $data['message'] = "Avatar image is missing or invalid";
	    }
	}

	else {
	    $data['code']    = 400;
	    $data['data']    = array();
	    $data['message'] = "Avatar image is missing or invalid";
	}
}