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
else {
	if (not_empty($_FILES['cover']) && not_empty($_FILES['cover']['tmp_name'])) {
        $file_info      = array(
            'file'      => $_FILES['cover']['tmp_name'],
            'size'      => $_FILES['cover']['size'],
            'name'      => $_FILES['cover']['name'],
            'type'      => $_FILES['cover']['type'],
            'file_type' => 'image',
            'folder'    => 'covers',
            'slug'      => 'cover',
            'allowed'   => 'jpg,png,jpeg,gif'
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['filename'])) {
            try {
                require_once(cl_full_path("core/libs/PHPgumlet/ImageResize.php"));
                require_once(cl_full_path("core/libs/PHPgumlet/ImageResizeException.php"));

                $prof_cover = new \Gumlet\ImageResize(cl_full_path($file_upload['filename']));
                $sw         = $prof_cover->getSourceWidth();
                $sh         = $prof_cover->getSourceHeight();

                if ($sw != 600) {
                    $prof_cover->resize(600, (($sh * 600) / $sw), true);
                    $prof_cover->save(cl_full_path($file_upload['filename']));
                }

                $path_info     = explode(".", $file_upload['filename']);
                $filepath      = fetch_or_get($path_info[0], "");
                $file_ext      = fetch_or_get($path_info[1], "");
                $cropped_cover = cl_strf("%s_600x200.%s", $filepath, $file_ext);
                

                $prof_cover->crop(600, 200, true);
                $prof_cover->save(cl_full_path($cropped_cover));

                cl_delete_media($me['raw_cover']);
                cl_delete_media($me['cover_orig']);

                cl_update_user_data($me['id'], array(
                    'cover'      => $cropped_cover,
                    'cover_orig' => $file_upload['filename']
                ));

                $data['code']    = 200;
                $data['message'] = "Profile cover changed successfully";
                $data['data']    = array(
                	"cover_url" => cl_get_media($cropped_cover)
                );
            } 

            catch (Exception $e) {
                $data['err_message'] = $e->getMessage();
                $data['code']        = 400;
                $data['data']        = array();
            }
        } 

        else{
		    $data['code']    = 400;
		    $data['data']    = array();
		    $data['message'] = "Cover image is missing or invalid";
        }
    }

    else {
	    $data['code']    = 400;
	    $data['data']    = array();
	    $data['message'] = "Cover image is missing or invalid";
    }
}