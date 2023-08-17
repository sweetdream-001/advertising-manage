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

if (empty($cl["is_logged"])) {
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else if ($action == 'load_more') {

    require_once(cl_full_path("core/apps/home/app_ctrl.php"));

    $data['err_code'] = 0;
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $html_arr         = array();

    if (is_posnum($offset)) {    

        $posts_ls = cl_get_timeline_feed(30, $offset);

        if (not_empty($posts_ls)) {
            foreach ($posts_ls as $cl['li']) {
                $html_arr[] = cl_template('timeline/post');
            }

            $data['status'] = 200;
            $data['html']   = implode("", $html_arr);
        }
    }
    
}

// else if ($action == 'update_timeline') {

//     require_once(cl_full_path("core/apps/home/app_ctrl.php"));

//     $data['err_code'] = 0;
//     $data['status']   = 400;
//     $onset            = fetch_or_get($_GET['onset'], 0);
//     $html_arr         = array();

//     if (is_posnum($onset)) {    

//         $posts_ls = cl_get_timeline_feed(false, false, $onset);

//         if (not_empty($posts_ls)) {
//             foreach ($posts_ls as $cl['li']) {
//                 $html_arr[] = cl_template('timeline/post');
//             }

//             $data['status'] = 200;
//             $data['html']   = implode("", $html_arr);
//         }
//     }
    
// }