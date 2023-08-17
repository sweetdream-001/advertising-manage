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


if ($cl["config"]["swift_system_status"] == "on") {
    if ($action == 'upload_swift_image') {
        if (empty($cl["is_logged"])) {
            $data['status'] = 400;
            $data['error']  = 'Invalid access token';
        }
        else {
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
            $swift_data       = $me['swift'];

            if (not_empty($_FILES['image']) && not_empty($_FILES['image']['tmp_name'])) {
            
                if (cl_can_swift($swift_data)) {

                    if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
                        $swift_data = cl_delete_swift($me["last_swift"]);
                    }

                    $file_info      = array(
                        'file'      => $_FILES['image']['tmp_name'],
                        'size'      => $_FILES['image']['size'],
                        'name'      => $_FILES['image']['name'],
                        'type'      => $_FILES['image']['type'],
                        'file_type' => 'image',
                        'folder'    => 'images',
                        'slug'      => 'swift',
                        'allowed'   => 'jpg,png,jpeg,gif'
                    );

                    $file_upload = cl_upload($file_info);

                    if (not_empty($file_upload['filename'])) {
                        $swift_id              = cl_genkey(16, 16);
                        $swift_data[$swift_id] = array(
                            "views"   => array(),
                            "time"    => time(),
                            "type"    => "image",
                            "status"  => "inactive",
                            "media"   => array(
                                "src" => $file_upload['filename']
                            )
                        );

                        $data['status'] = 200;
                        $data['img']    = array(
                            "url"       => cl_get_media($file_upload['filename'])
                        );

                        cl_update_user_data($me["id"], array(
                            "swift"      => cl_minify_js(json($swift_data, true)),
                            "last_swift" => $swift_id
                        ));
                    }
                }
                else {
                    $data['err_code'] = "total_limit_exceeded";
                    $data['status']   = 400;
                }
            }
        }
    }
    if ($action == 'upload_swift_audio') {
        if (empty($cl["is_logged"])) {
            $data['status'] = 400;
            $data['error']  = 'Invalid access token';
        }
        else {
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
            $swift_data       = $me['swift'];

            if (not_empty($_FILES['audio']) && not_empty($_FILES['audio']['tmp_name'])) {
            
                if (cl_can_swift($swift_data)) {

                    if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
                        $swift_data = cl_delete_swift($me["last_swift"]);
                    }

                    $file_info      = array(
                        'file'      => $_FILES['audio']['tmp_name'],
                        'size'      => $_FILES['audio']['size'],
                        'name'      => $_FILES['audio']['name'],
                        'type'      => $_FILES['audio']['type'],
                        'file_type' => 'audio',
                        'folder'    => 'audios',
                        'slug'      => 'swift',
                        'allowed'   => 'mp3,wav,mp4,caf,m4p,aiff,m4a,flac'
                    );

                    $file_upload = cl_upload($file_info);

                    if (not_empty($file_upload['filename'])) {
                        $swift_id              = cl_genkey(16, 16);
                        $swift_data[$swift_id] = array(
                            "views"   => array(),
                            "time"    => time(),
                            "type"    => "audio",
                            "status"  => "inactive",
                            "media"   => array(
                                "src" => $file_upload['filename']
                            )
                        );

                        $data['status'] = 200;
                        $data['audio']    = array(
                            "url"       => cl_get_media($file_upload['filename'])
                        );

                        cl_update_user_data($me["id"], array(
                            "swift"      => cl_minify_js(json($swift_data, true)),
                            "last_swift" => $swift_id
                        ));
                    }
                }
                else {
                    $data['err_code'] = "total_limit_exceeded";
                    $data['status']   = 400;
                }
            }
        }
    }

    else if ($action == 'upload_swift_video') {
        if (empty($cl["is_logged"])) {
            $data['status'] = 400;
            $data['error']  = 'Invalid access token';
        }
        else {
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
            $swift_data       = $me['swift'];

            if (not_empty($_FILES['video']) && not_empty($_FILES['video']['tmp_name'])) {
            
                if (cl_can_swift($swift_data)) {

                    if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
                        $swift_data = cl_delete_swift($me["last_swift"]);
                    }

                    $video_duration      = 10;
                    $max_swift_dur       = 10;
                    $file_info           = array(
                        'file'           => $_FILES['video']['tmp_name'],
                        'size'           => $_FILES['video']['size'],
                        'name'           => $_FILES['video']['name'],
                        'type'           => $_FILES['video']['type'],
                        'file_type'      => 'video',
                        'folder'         => 'videos',
                        'slug'           => 'swift',
                        'allowed'        => 'mp4,mov,3gp,webm',
                        'aws_uploadfile' => 'N'
                    );

                    $file_upload = cl_upload($file_info);

                    if (not_empty($file_upload['filename'])) {

                        try {
                            require_once(cl_full_path("core/libs/ffmpeg-php/vendor/autoload.php"));
                            require_once(cl_full_path("core/libs/getID3/getid3/getid3.php"));

                            $ffmpeg_binary = ($config['ffmpeg_binary'] == "/core/libs/ffmpeg/ffmpeg") ? cl_full_path($config['ffmpeg_binary']) : $config['ffmpeg_binary']; 
                            $ffmpeg        = new FFmpeg($ffmpeg_binary);
                            $getID3        = new getID3;
                            $video_file    = $getID3->analyze($file_upload['filename']);

                            if (isset($video_file["playtime_seconds"])) {

                                $ffmpeg           = new FFmpeg($ffmpeg_binary);
                                $video_duration   = intval($video_file["playtime_seconds"]);
                                $video_format     = $video_file["fileformat"];
                                $swift_video_path = cl_gen_path(array(
                                    "folder"      => "videos",
                                    "file_ext"    => "mp4",
                                    "file_type"   => "video",
                                    "slug"        => "swift"
                                ));

                                if (is_posnum($video_duration) && $video_duration > $max_swift_dur) {

                                    $ffmpeg->input($file_upload['filename']);
                                    $ffmpeg->set('-ss', '0');
                                    $ffmpeg->set('-to', '10');
                                    $ffmpeg->set('-c:v', 'copy');
                                    $ffmpeg->set('-c:a', 'copy');

                                    if ($video_format != 'mp4') {
                                        $ffmpeg->set('-vcodec', 'libx264');
                                        $ffmpeg->forceFormat('mp4');
                                    }
                                    
                                    $ffmpeg->output($swift_video_path)->ready();

                                    cl_delete_loc_media($file_upload['filename']);
                                    $file_upload['filename'] = $swift_video_path;

                                    $video_duration = $max_swift_dur;
                                }
                            }
                        } 

                        catch (Exception $e) {
                            $data["error"] = $e->getMessage();
                            $upload_fail   = true;
                        }

                        if (empty($upload_fail)) {
                            $swift_id              = cl_genkey(16, 16);
                            $swift_data[$swift_id] = array(
                                "views"            => array(),
                                "time"             => time(),
                                "type"             => "video",
                                "status"           => "inactive",
                                "media"            => array(
                                    "source"       => $file_upload['filename'],
                                    "duration"     => $video_duration
                                )
                            );

                            $data['status'] = 200;
                            $data['video']  = array(
                                "source"    => cl_get_media($file_upload['filename'])
                            );

                            cl_update_user_data($me["id"], array(
                                "swift"      => cl_minify_js(json($swift_data, true)),
                                "last_swift" => $swift_id
                            ));

                            if ($cl['config']['as3_storage'] == 'on') {
                                cl_upload2s3($file_upload['filename']);
                            }
                        }
                    }
                }
                else {
                    $data['err_code'] = "total_limit_exceeded";
                    $data['status']   = 400;
                }
            }
        }
    }

    else if ($action == 'delete_swift_image') {
        if (empty($cl["is_logged"])) {
            $data['status'] = 400;
            $data['error']  = 'Invalid access token';
        }
        else {
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
            $swift_data       = $me['swift'];

            if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
                $swift_data     = cl_delete_swift($me["last_swift"]);
                $data['status'] = 200;

                cl_update_user_data($me["id"], array(
                    "swift"      => cl_minify_js(json($swift_data, true)),
                    "last_swift" => ""
                ));
            } 
        }
    }
    else if ($action == 'delete_swift_audio') {
        if (empty($cl["is_logged"])) {
            $data['status'] = 400;
            $data['error']  = 'Invalid access token';
        }
        else {
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
            $swift_data       = $me['swift'];

            if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
                $swift_data     = cl_delete_swift($me["last_swift"]);
                $data['status'] = 200;

                cl_update_user_data($me["id"], array(
                    "swift"      => cl_minify_js(json($swift_data, true)),
                    "last_swift" => ""
                ));
            } 
        }
    }

    else if ($action == 'delete_swift_video') {
        if (empty($cl["is_logged"])) {
            $data['status'] = 400;
            $data['error']  = 'Invalid access token';
        }
        else {
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
            $swift_data       = $me['swift'];

            if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
                $swift_data     = cl_delete_swift($me["last_swift"]);
                $data['status'] = 200;

                cl_update_user_data($me["id"], array(
                    "swift"      => cl_minify_js(json($swift_data, true)),
                    "last_swift" => ""
                ));
            } 
        }
    }

    else if ($action == 'publish_new_swift') {
        if (empty($cl["is_logged"])) {
            $data['status'] = 400;
            $data['error']  = 'Invalid access token';
        }
        else {
            $data['err_code'] = 0;
            $data['status']   = 400;
            $swift_data       = $me['swift'];
            $swift_text       = fetch_or_get($_POST['swift_text'], "");
            $swift_text       = cl_croptxt($swift_text, 200);

            if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
                $swift_data[$me["last_swift"]]["status"]   = "active";
                $swift_data[$me["last_swift"]]["time"]     = time();
                $swift_data[$me["last_swift"]]["exp_time"] = (time() + (24 * 3600));
                $swift_data[$me["last_swift"]]["text"]     = "";

                if (not_empty($swift_text)) {
                    $swift_data[$me["last_swift"]]["text"] = cl_text_secure($swift_text);
                }

                cl_update_user_data($me["id"], array(
                    "swift"        => cl_minify_js(json($swift_data, true)),
                    "last_swift"   => "",
                    "swift_update" => (time() + (24 * 3600))
                ));

                $data['status'] = 200;
            }
        }
    }

    else if($action == 'delete_swift') {
        $data['err_code'] = "invalid_req_data";
        $data['status']   = 400;
        $swift_data       = $me['swift'];
        $swift_id         = fetch_or_get($_POST["swid"], false);

        if (is_array($swift_data) && isset($swift_data[$swift_id])) {
            $swift_data     = cl_delete_swift($swift_id);
            $data['status'] = 200;
            $junked_swifts  = array();

            foreach ($swift_data as $i => $row) {
                if (cl_is_junked_swift($row)) {
                    array_push($junked_swifts, $i);
                }
            }

            if (not_empty($junked_swifts)) {
                foreach ($junked_swifts as $junked_swid) {
                    $swift_data = cl_delete_swift($junked_swid);
                }
            }
            
            cl_update_user_data($me["id"], array(
                "swift" => cl_minify_js(json($swift_data, true))
            ));
        }
    }

    else if($action == 'swift_view') {
        $data['err_code'] = "invalid_req_data";
        $data['status']   = 400;
        $swift_id         = fetch_or_get($_POST["swid"], false);
        $swift_user_id    = fetch_or_get($_POST["user_id"], false);

        if (not_empty($swift_id) && is_posnum($swift_user_id) && $swift_user_id != $me["id"]) {
            $swift_udata = cl_raw_user_data($swift_user_id);

            if (not_empty($swift_udata)) {
                $swift_data = cl_init_swift($swift_udata["swift"]);

                if (is_array($swift_data) && isset($swift_data[$swift_id])) {

                    if (in_array($me["id"], $swift_data[$swift_id]["views"]) != true) {
                        $swift_data[$swift_id]["views"][$me["id"]] = time();

                        cl_update_user_data($swift_user_id, array(
                            "swift" => cl_minify_js(json($swift_data, true))
                        ));

                        $data['status'] = 200;
                    }
                }
            }
        }
    }
}