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
    $pos = fetch_or_get($_POST['cover_position'], 0);
    $dw  = 600;
    $dh  = 200;

    if (is_numeric($pos)) {
        try {
            require_once(cl_full_path("core/libs/PHPgumlet/ImageResize.php"));
            require_once(cl_full_path("core/libs/PHPgumlet/ImageResizeException.php"));

            $prof_cover     = new \Gumlet\ImageResize(cl_full_path($me['cover_orig']));
            $file_ext       = explode('.', $me['raw_cover']);
            $file_ext       = end($file_ext);
            $file_ext       = (empty($file_ext)) ? 'jpg' : $file_ext;
            $filename       =  cl_gen_path(array(
                'file_ext'  => $file_ext,
                'file_type' => 'image',
                'folder'    => 'covers',
                'slug'      => 'cover'
            ));

            $prof_cover->freecrop($dw, $dh, 0, $pos);
            $prof_cover->save(cl_full_path($filename));
            
            cl_delete_media($me['raw_cover']);

            cl_update_user_data($me['id'], array(
                'cover' => $filename
            ));

            $data['code']    = 200;
            $data['message'] = "Your changes have been successfully saved";
            $data['data']    = array(
                'cover_url'  => cl_get_media($filename)
            );
        } 

        catch (Exception $e) {
            $data['code']     = 400;
            $data['data']     = array();
            $data['message']  = $e->getMessage();
        }
    }
    else {
        $data['code']    = 400;
        $data['data']    = array();
        $data['message'] = "Cover position offset number is missing or invalid";
    }
}