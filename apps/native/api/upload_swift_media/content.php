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
    $swift_data = $me['swift'];
    $media_type = fetch_or_get($_POST["type"], false);

    if (empty($media_type) || in_array($media_type, array("image", "video")) != true) {
    	$data['code']    = 400;
        $data['message'] = "Media file type is missing or invalid";
    	$data['data']    = array();
    }

    if (cl_can_swift($swift_data) != true) {
    	$data['code']    = 400;
        $data['message'] = "Daily swift replication limit has been reached";
    	$data['data']    = array();
    }

    if (empty($_FILES['file']) || empty($_FILES['file']['tmp_name'])) {
    	$data['code']    = 500;
        $data['message'] = "Media file is missing or invalid";
    	$data['data']    = array();
    }

    else {
    	if ($media_type == "image") {

	        if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
                $swift_data = cl_delete_swift($me["last_swift"]);
            }

            $file_info      = array(
                'file'      => $_FILES['file']['tmp_name'],
                'size'      => $_FILES['file']['size'],
                'name'      => $_FILES['file']['name'],
                'type'      => $_FILES['file']['type'],
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

                cl_update_user_data($me["id"], array(
                    "swift"      => cl_minify_js(json($swift_data, true)),
                    "last_swift" => $swift_id
                ));

                $data['code']    = 200;
                $data['message'] = "Media file uploaded successfully";
                $data['data']    = array(
                    "url"        => cl_get_media($file_upload['filename']),
                    "type"       => "image"
                );
            }
    	}

    	else if($media_type == "video") {

            if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
                $swift_data = cl_delete_swift($me["last_swift"]);
            }

            $video_duration = 10;
            $max_swift_dur  = 10;
            $file_info      = array(
                'file'      => $_FILES['file']['tmp_name'],
                'size'      => $_FILES['file']['size'],
                'name'      => $_FILES['file']['name'],
                'type'      => $_FILES['file']['type'],
                'file_type' => 'video',
                'folder'    => 'videos',
                'slug'      => 'swift',
                'allowed'   => 'mp4,mov,3gp,webm',
            );

            $file_upload = cl_upload($file_info);

            if (not_empty($file_upload['filename'])) {

                try {
                    require_once(cl_full_path("core/libs/ffmpeg-php/vendor/autoload.php"));
                    require_once(cl_full_path("core/libs/getID3/getid3/getid3.php"));

                    $ffmpeg     = new FFmpeg(cl_full_path($config['ffmpeg_binary']));
                    $getID3     = new getID3;
                    $video_file = $getID3->analyze($file_upload['filename']);

                    if (isset($video_file["playtime_seconds"])) {

                        $ffmpeg           = new FFmpeg(cl_full_path($config['ffmpeg_binary']));
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
                            $ffmpeg->set('-ss','0');
                            $ffmpeg->set('-to','10');
                            $ffmpeg->set('-c:v','copy');
                            $ffmpeg->set('-c:a','copy');

                            if ($video_format != 'mp4') {
                                $ffmpeg->forceFormat('mp4');
                            }
                            
                            $ffmpeg->output($swift_video_path)->ready();

                            cl_delete_media($file_upload['filename']);
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

                    cl_update_user_data($me["id"], array(
                        "swift"      => cl_minify_js(json($swift_data, true)),
                        "last_swift" => $swift_id
                    ));

                    $data['code']    = 200;
                    $data['message'] = "Media file uploaded successfully";
                    $data['data']    = array(
                        "url"        => cl_get_media($file_upload['filename']),
                        "type"       => "video"
                    );

                    if ($cl['config']['as3_storage'] == 'on') {
                        cl_upload2s3($file_upload['filename']);
                    }
                }
            }
    	}
    }
}