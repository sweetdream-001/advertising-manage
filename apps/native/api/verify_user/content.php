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

if (empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 401,
		'data'    => array(),
		'message' => 'Unauthorized Access'
	);
}

else if($me['verified'] == '1') {
	$data         = array(
		'code'    => 400,
		'data'    => array(),
		'message' => 'Your account has already been verified'
	);
}

else {
    if ($me['verified'] == '2') {
    	$data['code']    = 400;
        $data['message'] = "Error Duplicate request. Please wait until the end of the review of the previous request";
    	$data['data']    = array();
    }

    else if (empty($_POST['full_name']) || len_between($_POST['full_name'], 3, 60) != true) {
        $data['code']    = 400;
        $data['message'] = "Invalid user full name. Please check your details";
    	$data['data']    = array();
    }

    else if (empty($_POST['text_message']) || len_between($_POST['text_message'], 1, 1200) != true) {
        $data['code']    = 400;
        $data['message'] = "Text message to the reviewer is incorrect or missing";
    	$data['data']    = array();
    }

    else if(empty($_FILES['video_message']) || empty($_FILES['video_message']['tmp_name'])) {
        $data['code']    = 400;
        $data['message'] = "Video message to the reviewer is incorrect or missing";
    	$data['data']    = array();
    }

    else {
        $file_info      = array(
            'file'      => $_FILES['video_message']['tmp_name'],
            'size'      => $_FILES['video_message']['size'],
            'name'      => $_FILES['video_message']['name'],
            'type'      => $_FILES['video_message']['type'],
            'file_type' => 'video',
            'folder'    => 'videos',
            'slug'      => 'video_message',
            'allowed'   => 'mp4,mov,3gp,webm',
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['filename'])) {
            $full_name          = cl_text_secure($_POST['full_name']);
            $text_message       = cl_text_secure($_POST['text_message']);
            $insert_data        = array(
                'user_id'       => $me['id'],
                'full_name'     => $full_name,
                'text_message'  => $text_message,
                'video_message' => $file_upload['filename'],
                'time'          => time()
            );

            $req_id = cl_db_insert(T_VERIFICATIONS, $insert_data);

            if (is_posnum($req_id)) {
                $data['code']    = 200;
                $data['message'] = "Verification request sent successfully";

                cl_update_user_data($me['id'], array(
                    'verified' => '2'
                ));
            }
            else {
            	$data['code']    = 500;
		        $data['message'] = "An error occurred while processing your request. Please try again later.";
		    	$data['data']    = array();
            }
        }
    }
}