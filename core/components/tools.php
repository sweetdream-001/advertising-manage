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

function cl_full_path($path = "") {
	if (empty($path) != true && is_string($path)) {
		return (PROJ_RP . "/" . $path);
	}
	else {
		return PROJ_RP;
	}
}

function cl_ikon($icon_name = "") {
    global $config;

    $path1 = cl_strf("themes/%s/statics/ikons/%s.svg", $config['theme'], $icon_name);

    if (file_exists(cl_full_path($path1))) {
        return file_get_contents(cl_full_path($path1));
    }
    else{
        return "<b>?</b>";
    }
}

function cl_icon($icon_name = "") {
    global $config;

    $path1 = cl_strf("themes/%s/statics/md_icons/%s.svg", $config['theme'], $icon_name);

    if (file_exists(cl_full_path($path1))) {
        return file_get_contents(cl_full_path($path1));
    }
    else{
        return "<b>?</b>";
    }
}

function cl_banner($icon_name = "") {
	global $config;

	$path = cl_strf("themes/%s/statics/banners/%s.svg",$config['theme'],$icon_name);
    $path = cl_full_path($path);

	if (file_exists($path)) {
		return file_get_contents($path);
	}
	else{
		return "<b>?</b>";
	}
}
 
function cl_get_restricted_usernames() {
    global $config;

    $data = array();
    $usernames = explode(",", $config["username_restrictions"]);

    if (is_array($usernames)) {
        foreach($usernames as $uname) {
            array_push($data, trim($uname));
        }
    }

    return $data;
}
 
function cl_get_restricted_useremails() {
    global $config;

    $data = array();
    $useremails = explode(",", $config["useremail_restrictions"]);

    if (is_array($useremails)) {
        foreach($useremails as $email) {
            array_push($data, trim($email));
        }
    }

    return $data;
}

function cl_banner_url($icon_name = "") {
    global $config;

    return cl_get_media(cl_strf("themes/%s/statics/banners/%s.svg", $config['theme'], $icon_name));
}

function cl_slug($str, $delimiter = '_'){
    $slug = trim(preg_replace("#(\p{P}|\p{C}|\p{S}|\p{Z})+#u", mb_strtolower($delimiter,'UTF-8'), $str), $delimiter);
    return mb_strtolower($slug);
}

function cl_croptxt($text = "", $len = 100,$end = "") {
    if (empty($text) || is_string($text) != true || not_num($len) || $len < 1) {
        return "";
    }
    if (mb_strlen($text) > $len) {
        $text = mb_substr($text, 0, $len, 'utf-8') . $end;
    }
    return $text;
}

function cl_html_el($tag_name = "html", $cont = "", $attrs = array()) {

    $tag_attrs = "";

    if (not_empty($attrs)) {
        $tag_attrs = cl_html_attrs($attrs);
    }
    
    return cl_strf("<%s %s>%s</%s>",$tag_name,$tag_attrs,$cont,$tag_name);
}

function cl_html_attrs($attrs = array()) {
    $tag_attrs = array();

    if (not_empty($attrs) && is_array($attrs)) {

        foreach ($attrs as $attr => $value) {
            array_push($tag_attrs, cl_strf("%s=\"%s\"",$attr,$value));
        }
    }
    
    return implode(" ", $tag_attrs);
}

function cl_text_secure($text = "") {
  
 
    global $mysqli;
    $text = trim($text);
    $text = stripslashes($text);
    $text = strip_tags($text);
    $text = mysqli_real_escape_string($mysqli, $text);
    $text = htmlspecialchars($text, ENT_NOQUOTES);
    $text = str_replace('&amp;#', '&#', $text);
    $text = preg_replace('/\{\%(.*?)\%\}/', '', $text);
    $text = preg_replace('/\&#[0-9]{1,9}\;/', '', $text);

    return $text;
}

function cl_link($path = "") {
    global $url;
    return (($path == "/") ? $url : "$url/$path");
}

function cl_redirect($link = '') {
    global $spa_load;

    if ($spa_load == '1') {

        header('Content-Type: application/json');

        $data = array(
            'status' => 302,
            'redirect_url' => cl_link($link)
        );

        echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        exit();
    }
    else {
        header(cl_strf("Location: %s", cl_link($link)));
        exit();
    }
}

function cl_redirect_after($link = '', $seconds = 0) {
    header(cl_strf("Refresh: %d; url=%s", $seconds, cl_link($link)));
    exit();
}

function cl_location($link = '') {
    header(cl_strf("Location: %s", $link));
    exit();
}

function cl_static_file_path($path = "") {
    global $config;

    $full_path = cl_strf("%s/%s",$config['theme_url'],$path);

    return $full_path;
}

function cl_generate_csrf_token() {
    if (not_empty($_SESSION['csrf'])) {
        return $_SESSION['csrf'];
    }
    
    $hash = substr(sha1(rand(1111, 9999)), 0, 70);
    $slat = time();
    $hash = cl_strf('%d:%s',$slat,$hash);

    $_SESSION['csrf'] = $hash;

    return $hash;
}

function cl_verify_csrf_token($hash = '') {
    if (empty($_SESSION['csrf']) || empty($hash)) {
        return false;
    }

    return ($hash == $_SESSION['csrf']) ? true : false;
}

function cl_queryset($data = null, $type = 'array') {
    $query = false;
    if ($type == 'object') {
        $query = (is_object($data) && not_empty($data));
    } 

    else {
        $query = (is_array($data) && not_empty($data));
    }
    
    return $query;
}

function cl_get_media($media = '', $is_upload = false){
    global $config;
    if (empty($media)) {
        return '';
    }
    
    if ($config['as3_storage'] == 'on') {
        $as3_bucket = $config['as3_bucket_name'];
        $media_url  = cl_strf("https://%s.s3.amazonaws.com/%s", $as3_bucket, $media);
        return $media_url;
    }
    else {
        $media_url = cl_strf("%s/%s", $config['url'], $media);
        return $media_url;
    }
}

function cl_get_media_placeholder($media = ""){
    global $config;

    if (empty($media)) {
        return "";
    }
    
    if ($config['as3_storage'] == "on") {
        $as3_bucket = $config['as3_bucket_name'];
        $media_url  = cl_strf("https://%s.s3.amazonaws.com/upload/default/%s.png", $as3_bucket, $media);
        return $media_url;
    }
    else {
        $media_url = cl_strf("%s/upload/default/%s.png", $config['url'], $media);
        return $media_url;
    }
}

function cl_send_mail($data = array()) {
    global $cl, $db;

    try {
        require_once(cl_full_path('core/libs/configs/mailer.php'));

        $email_from      = $data['from_email'] = cl_text_secure($data['from_email']);
        $to_email        = $data['to_email']   = cl_text_secure($data['to_email']);
        $subject         = $data['subject'];
        $data['charSet'] = cl_text_secure($data['charSet']);
        $mail->SMTPDebug = false;

        if ($cl['config']['smtp_or_mail'] == 'mail') {
            $mail->IsMail();
        } else if ($cl['config']['smtp_or_mail'] == 'smtp') {
            $mail->isSMTP();
            $mail->Timeout     = 30;
            $mail->SMTPDebug   = false;
            $mail->Host        = $cl['config']['smtp_host'];
            $mail->SMTPAuth    = true;
            $mail->Username    = $cl['config']['smtp_username'];
            $mail->Password    = $cl['config']['smtp_password'];
            $mail->SMTPSecure  = $cl['config']['smtp_encryption'];
            $mail->Port        = $cl['config']['smtp_port'];
            $mail->SMTPOptions = array(
                'ssl'          => array(
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                )
            );  
        } 

        else {
            return false;
        }
        
        $mail->IsHTML($data['is_html']);
        $mail->setFrom($cl['config']['smtp_username'], $data['from_name']);
        $mail->addAddress($data['to_email'], $data['to_name']);
        $mail->Subject = $data['subject'];
        $mail->CharSet = $data['charSet'];
        $mail->MsgHTML($data['message_body']);

        if ($mail->send()) {
            $mail->ClearAddresses();

            return true;
        }
    }

    catch (Exception $e) {
       return false; 
    }
}

function cl_curl_httpreq($url = "", $payload = array()) {

    if (empty($url)) {
        return false;
    }

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
    
    if (not_empty($payload['post_data'])) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload['post_data']);
    }

    if (not_empty($payload['bearer'])) {

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            cl_strf("Authorization: Bearer %s", $config['bearer'])
        ));
    }

    $curl_response = curl_exec($curl);

    curl_close($curl);

    $curl_response = json($curl_response);

    if (is_array($curl_response)) {
        return $curl_response;
    }
    
    return false;
}

function http_referer() {
    global $cl;

    $ref = fetch_or_get($_SERVER['HTTP_REFERER'], $cl['config']['url']);

    return $ref;
}

function cl_cropimg($max_width, $max_height, $source_file, $dst_dir, $quality = 80) {
    $imgsize = @getimagesize($source_file);
    $width   = $imgsize[0];
    $height  = $imgsize[1];
    $mime    = $imgsize['mime'];
    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image        = "imagegif";
            break;
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image        = "imagepng";
            break;
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image        = "imagejpeg";
            break;
        default:
            return false;
            break;
    }

    $dst_img    = @imagecreatetruecolor($max_width, $max_height);
    $src_img    = $image_create($source_file);
    $width_new  = ($height * $max_width / $max_height);
    $height_new = ($width * $max_height / $max_width);

    if ($width_new > $width) {
        $h_point = (($height - $height_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } 

    else {
        $w_point = (($width - $width_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }

    @imagejpeg($dst_img, $dst_dir, $quality);
    if ($dst_img) {
        @imagedestroy($dst_img);
    }     
    if ($src_img) {
        @imagedestroy($src_img);
    }      
}

function cl_compress_img($source_url, $destination_url, $quality) {
    $info = getimagesize($source_url);
    if ($info['mime'] == 'image/jpeg') {
        $image = @imagecreatefromjpeg($source_url);
        $image = cl_imagerotate($image, $source_url);

        @imagejpeg($image, $destination_url, $quality);
    } 

    elseif ($info['mime'] == 'image/gif') {
        $image = @imagecreatefromgif($source_url);
        $image = cl_imagerotate($image, $source_url);

        @imagegif($image, $destination_url, $quality);
    } 

    elseif ($info['mime'] == 'image/png') {
        $image = @imagecreatefrompng($source_url);

        @imagepng($image, $destination_url);
    }
}

function cl_imagerotate($img_source = false, $source_url = false) {

    if (function_exists("exif_read_data")) {
        try {
            $file_exif = @exif_read_data($source_url);

            if (not_empty($file_exif["Orientation"])) {
                if ($file_exif["Orientation"] == 3) {
                    return imagerotate($img_source, 180, 0);
                }

                else if($file_exif["Orientation"] == 6) {
                    return imagerotate($img_source, -90, 0);
                }

                else if($file_exif["Orientation"] == 8) {
                    return imagerotate($img_source, 90, 0);
                }
            }
        } 

        catch (Exception $e) {
            return $img_source;
        }
    }

    return $img_source;
}

function cl_genkey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false) {
    $charset = '';
    if ($uselower) {
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    }
    if ($useupper) {
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if ($usenumbers) {
        $charset .= "123456789";
    }
    if ($usespecial) {
        $charset .= "~@#$%^*()_+-={}|][";
    }
    if ($minlength > $maxlength) {
        $length = mt_rand($maxlength, $minlength);
    } else {
        $length = mt_rand($minlength, $maxlength);
    }
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $charset[(mt_rand(0, mb_strlen($charset) - 1))];
    }
    return $key;
}

function cl_gen_path($data = array()) {
    $upload_dirs = array(
        cl_full_path(cl_strf("upload/avatars/%s/%s",date('Y'), date('m'))),
        cl_full_path(cl_strf("upload/covers/%s/%s",date('Y'), date('m'))),
        cl_full_path(cl_strf("upload/receipts/%s/%s",date('Y'), date('m'))),
        cl_full_path(cl_strf("upload/images/%s/%s",date('Y'), date('m'))),
        cl_full_path(cl_strf("upload/videos/%s/%s",date('Y'), date('m'))),
        cl_full_path(cl_strf("upload/audios/%s/%s",date('Y'), date('m'))),
        cl_full_path(cl_strf("upload/documents/%s/%s",date('Y'), date('m')))
    );

    foreach ($upload_dirs as $upload_dir) {
        if (file_exists($upload_dir) !== true) {
            @mkdir($upload_dir, 0777, true);
        }
    }

    $folder    = $data['folder'];
    $file_ext  = fetch_or_get($data['file_ext'], 'jpg');
    $file_type = $data['file_type'];
    $slug      = $data['slug'];
    $file_type = (not_empty($slug)) ? cl_strf("%s_%s",$file_type,$slug) : $file_type;
    $dir       = cl_strf("upload/%s/%s/%s",$folder,date('Y'),date('m'));
    $filename  = cl_strf("%s/%s_%s_%s_%s.%s", $dir, cl_genkey(), date('d'), md5(time()),$file_type,$file_ext);

    return $filename;
}

function cl_upload($data = array()) {
    global $cl;

    if (empty($data)) {
        return false;
    }

    $allowed = 'jpg,png,jpeg,gif,webp';

    if (not_empty($data['allowed'])) {
        $allowed = $data['allowed'];
    }

    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($data['name'], PATHINFO_EXTENSION);
    $file_extension    = strtolower($file_extension);
    $folder            = $data['folder'];
    $file_type         = $data['file_type'];
    $slug              = $data['slug'];

    if (in_array($data['type'], $cl["media_mime_types"]) != true) {
        // return array(
        //     'error' => 'File format not supported'
        // );
    }
    else if(in_array($file_extension, $extension_allowed) != true) {
        return array(
            'error' => 'File extension not supported'
        );
    }

    else if(intval($data["size"]) > intval($cl["config"]["max_upload_size"])) {
        return array(
            'error' => 'File is too large'
        );
    }

    $file_ext       = $file_extension;
    $result         = array();
    $filename       = cl_gen_path(array(
        "folder"    => $folder,
        "file_ext"  => $file_ext,
        "file_type" => $file_type,
        "slug"      => $slug,
    ));

    if (move_uploaded_file($data['file'], $filename)) {
        if (in_array($file_ext, array('gif','png','jpeg','jpg')) == true) {
            try {
                if ($file_ext != 'gif') {
                    cl_compress_img($filename, $filename, 90);
                }
            } catch (Exception $e) { /* pass */ }

            if (not_empty($data['crop'])) {
                $crop_size         = cl_strf("%sx%s",$data['crop']['width'],$data['crop']['height']);
                $cropped_img       = cl_gen_path(array(
                    "folder"       => $folder,
                    "file_ext"     => $file_ext,
                    "file_type"    => $file_type,
                    "slug"         => $crop_size,
                ));
                $crop_image        = cl_cropimg($data['crop']['width'], $data['crop']['height'], $filename, $cropped_img, 60);
                $result['cropped'] = $cropped_img;
            }
        }

        if ($cl['config']['as3_storage'] == 'on') {

            $aws_uploadfile = fetch_or_get($data["aws_uploadfile"], "Y");

            if ($aws_uploadfile == "Y") {
                try {
                    cl_upload2s3($filename);

                    if (not_empty($result['cropped'])) {
                        cl_upload2s3($result['cropped']);
                    }
                } catch (Exception $e) { /* pass */ }
            }
        }

        $result['filename'] = $filename;
        $result['name']     = $data['name'];

        return $result;
    }
}

function cl_upload2s3($filename = null, $del_localfile = "Y") {
    global $cl;
    
    if ($cl['config']['as3_storage'] == 'off') {
        return false;
    }
    else {
        if (empty($cl['config']['as3_api_key'])) {
            return false;
        }

        else if(empty($cl['config']['as3_api_secret_key'])) {
            return false;
        }

        else if(empty($cl['config']['as3_bucket_region'])) {
            return false;
        }

        else if(empty($cl['config']['as3_bucket_name'])) {
            return false;
        }

        else {
            try {

                include_once(cl_full_path("core/libs/s3/vendor/autoload.php"));

                $amazon_s3        = new \Aws\S3\S3Client(array(
                    'version'     => 'latest',
                    'region'      => $cl['config']['as3_bucket_region'],
                    'credentials' => array(
                        'key'     => $cl['config']['as3_api_key'],
                        'secret'  => $cl['config']['as3_api_secret_key']
                    )
                ));

                $up_aws_object     = $amazon_s3->putObject(array(
                    'Bucket'       => $cl['config']['as3_bucket_name'],
                    'Key'          => $filename,
                    'Body'         => fopen($filename, 'r+'),
                    'ACL'          => 'public-read',
                    'CacheControl' => 'max-age=3153600'
                ));

                if ($del_localfile == "Y") {
                    if ($amazon_s3->doesObjectExist($cl['config']['as3_bucket_name'], $filename)) {
                        cl_delete_loc_media($filename);
                    }
                }

                return true;
            } 
            catch (Exception $e) {
                return false;
            }
        }
    } 
}

function cl_delete_from_s3($filename = null) {
    global $cl;

    if ($cl['config']['as3_storage'] == 'off') {
        return false;
    }
    else {
        if (empty($cl['config']['as3_api_key'])) {
            return false;
        }

        else if(empty($cl['config']['as3_api_secret_key'])) {
            return false;
        }

        else if(empty($cl['config']['as3_bucket_region'])) {
            return false;
        }

        else if(empty($cl['config']['as3_bucket_name'])) {
            return false;
        }

        try {
            include_once(cl_full_path("core/libs/s3/vendor/autoload.php"));

            $amazon_s3        = new \Aws\S3\S3Client(array(
                'version'     => 'latest',
                'region'      => $cl['config']['as3_bucket_region'],
                'credentials' => array(
                    'key'     => $cl['config']['as3_api_key'],
                    'secret'  => $cl['config']['as3_api_secret_key']
                )
            ));

            $rm_aws_object = $amazon_s3->deleteObject(array(
                'Bucket'   => $cl['config']['as3_bucket_name'],
                'Key'      => $filename
            ));

            if ($amazon_s3->doesObjectExist($cl['config']['as3_bucket_name'], $filename) != true) {
                return true;
            }

            else {
                return false;
            }
        } 
        catch (Exception $e) {
            return false;
        }
    }
}

function cl_import_image($data = array()) {
    global $cl;

    if (empty($data['url']) || is_url($data['url']) != true) {
        return false;
    }

    try {
        $url       = $data['url'];
        $file_ext  = explode('.', $url);
        $file_ext  = end($file_ext);
        $file_ext  = (in_array($file_ext, array('png', 'jpg', 'jpeg', 'gif', 'webp'))) ? $file_ext : 'jpg';
        $get_media = file_get_contents($url);
        $file_name = cl_gen_path(array(
            "folder" => $data["folder"],
            "file_type" => $data["file_type"],
            "slug"      => $data["slug"],
        ));

        if (not_empty($get_media)) {
            $import_image = file_put_contents(cl_full_path($file_name), $get_media);
        }

        $file_name = ((file_exists(cl_full_path($file_name))) ? $file_name : false); 

        if (not_empty($file_name)) {
            if ($cl['config']['as3_storage'] == 'on') {
                try {
                    cl_upload2s3($file_name);
                } catch (Exception $e) { /* pass */ }
            }
        }

        return $file_name;
    } 

    catch (Exception $e) {
        return false;
    }
}

function cl_import_aws_media($file_name = false) {
    global $cl;

    if (empty($file_name)) {
        return false;
    }

    try {

        $get_media = file_get_contents(cl_get_media($file_name));

        if (not_empty($get_media)) {
            file_put_contents(cl_full_path($file_name), $get_media);
        }

        $file_name = ((file_exists(cl_full_path($file_name))) ? $file_name : false); 

        return $file_name;
    } 

    catch (Exception $e) {
        return false;
    }
}

function cl_delete_loc_media($path = null) {
    global $cl;

    if (not_empty($path) && file_exists(cl_full_path($path))) {
        try {

            $placeholders = array(
                'upload/default/avatar.png',
                'upload/default/cover.png',
                'upload/default/as3-do-not-delete.png',
                'upload/default/video.png',
                'upload/default/gif.png',
                'upload/default/image.png' 
            );
            
            if (in_array($path, $placeholders) != true) {
                @unlink(cl_full_path($path));
            }
            
        } catch (Exception $e) {/*pass*/ }
    }
}

function cl_delete_media($path = null) {
    global $cl;

    if (not_empty($path) && file_exists(cl_full_path($path))) {
        try {

            $placeholders = array(
                'upload/default/video.png',
                'upload/default/avatar-1.png',
                'upload/default/avatar-2.png',
                'upload/default/avatar-3.png',
                'upload/default/avatar-4.png',
                'upload/default/avatar-5.png',
                'upload/default/avatar-6.png',
                'upload/default/avatar-7.png',
                'upload/default/avatar-8.png',
                'upload/default/avatar-9.png',
                'upload/default/avatar-10.png',
                'upload/default/avatar-11.png',
                'upload/default/avatar-12.png',
                'upload/default/avatar-13.png',
                'upload/default/avatar-14.png',
                'upload/default/avatar-15.png',
                'upload/default/cover-1.png',
                'upload/default/cover-2.png',
                'upload/default/cover-3.png',
                'upload/default/cover-4.png',
                'upload/default/cover-5.png',
                'upload/default/cover-6.png',
                'upload/default/cover-7.png',
                'upload/default/cover-8.png',
                'upload/default/cover-9.png',
                'upload/default/cover-10.png',
                'upload/default/cover-11.png',
                'upload/default/cover-12.png',
                'upload/default/cover-13.png',
                'upload/default/cover-14.png',
                'upload/default/cover-15.png',
                'upload/default/as3-do-not-delete.png'
            );
            
            if (in_array($path, $placeholders) != true) {
                @unlink(cl_full_path($path));
            }
            
        } catch (Exception $e) {/*pass*/ }
    }
    else{
        if ($cl['config']['as3_storage'] == 'on') {
            cl_delete_from_s3($path);
        }
    }
}

function cl_is_ajax() {
    if (not_empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        if (mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
    }

    return false;
}

function cl_number($number = null) {
    if (is_numeric($number)) {
        return number_format($number, 0, '',', ');
    }

    return 0;
}

function cl_remove_emoji($text = ""){
    return preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);
}

function cl_add_http_scheme($url = "") {
    if ((substr($url, 0, 7) == "http://") || (substr($url, 0, 8) == "https://") ) {
        return $url;
    }

    else {
        return sprintf("http://%s",$url);
    }
}

function cl_linkify_urls($text = "") {
    if (empty($text)) {
        return $text;
    }

    else {
        try {
            $text = preg_replace_callback('/(?P<url>https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/is', function($m) {
                if (isset($m['url'])) {

                    $url = cl_add_http_scheme($m['url']);

                    return cl_html_el('a', cl_croptxt($m['url'], 26, "..."), array(
                        'href' => $url,
                        'target' => '_blank',
                        'class' => 'inline-link'
                    ));
                }
            }, $text);

            return $text;
        } catch (Exception $e) { /*pass*/ }

        return $text;
    }
}

function cl_session($key = null, $val = null) { 
    if (not_empty($key) && is_string($key)) {
        if ($key && $val) {
            $_SESSION[$key] = $val;
            return true;
        } 
        else {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
        }
    }

    return false;
}

function cl_session_unset($key = null) { 
    if (not_empty($key) && isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

function cl_rn2br($text = "") {
    $text = str_ireplace("\r\n", "<br>", $text);
    $text = str_ireplace("\n\r", "<br>", $text);
    $text = str_ireplace("\r", "<br>", $text);
    $text = str_ireplace("\n", "<br>", $text);
    $text = str_ireplace('\r\n', "<br>", $text);
    $text = str_ireplace('\n\r', "<br>", $text);
    $text = str_ireplace('\r', "<br>", $text);
    $text = str_ireplace('\n', "<br>", $text);

    return $text;
}

function cl_strip_brs($content = "") {
    return preg_replace('/(<br\s{0,}\/{0,}>\s{0,}){3,}/i', '<br/><br/>', $content);
}

function cl_rn_strip($text = "") {
    $text = str_ireplace("\r\n", " ", $text);
    $text = str_ireplace("\n\r", " ", $text);
    $text = str_ireplace("\r", " ", $text);
    $text = str_ireplace("\n", " ", $text);
    $text = str_ireplace('\r\n', " ", $text);
    $text = str_ireplace('\n\r', " ", $text);
    $text = str_ireplace('\r', " ", $text);
    $text = str_ireplace('\n', " ", $text);

    return $text;
}

function cl_get_configurations() {
    global $db;

    $data    = array();
    $configs = $db->get(T_CONFIGS);

    foreach ($configs as $config) {
        $data[$config['name']] = $config['value'];
    }

    return $data;
}

function cl_json_server500_err($errno = false, $errstr = "", $errfile = "", $errline = "") {
    if ($errno) {
        $errors = array(
            'status'  => 500,
            'errno'   => $errno,
            'message' => "$errstr in [$errfile] at line $errline"
        );
    }
    else {
        $errors = array(
            'status'  => 500,
            'message' => $errstr
        );
    }
    
    echo json_encode($errors,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit();
}

function cl_minify_js($code = ''){
    $code = preg_replace('/(\r\n|\n|\t|\s{2,})/is', '', $code);
    return $code;
}

function cl_minify_css($code = ''){
    $code = preg_replace('/(\r\n|\n|\t|\s{2,})/is', '', $code);
    return $code;
}

function cl_is_decimal($value = 0) {
    if(strpos($value, ".") !== false){
        return true;
    }

    else{
        return false;
    }
}

function cl_money($money = '0.00', $digits = 2) {
    global $cl;

    $digits = (cl_is_decimal($money)) ? $digits : 0;
    $curr   = $cl['site_currencies'][$cl['config']['site_currency']];


    if (is_numeric($money) != true) {
        if ($cl["config"]["currency_symbol_pos"] == "after") {
            return cl_strf("0.00%s", $curr['symbol']);
        }
        else{
            return cl_strf("%s0.00", $curr['symbol']);
        }
    }

    else {
        if ($cl["config"]["currency_symbol_pos"] == "after") {
            return cl_strf("%s%s", number_format($money, $digits, '.',  '.'), $curr['symbol']);
        }
        else{
            return cl_strf("%s%s", $curr['symbol'], number_format($money, $digits, '.',  '.'));
        }
    }
}

function cl_text($text = "") {
    $text = stripcslashes($text);
    $text = htmlspecialchars_decode($text, ENT_QUOTES);
    $text = cl_rn_strip($text);

    return $text;
}

function cl_encode_og_text($text = "") {
    $text  = stripcslashes($text);
    $text  = htmlspecialchars($text, ENT_QUOTES);
    $text  = cl_rn_strip($text);
    $text  = cl_croptxt($text, 180);
    $htags = cl_listify_htags($text);
    $text  = cl_tagify_htags($text, $htags);

    return $text;
}

function cl_get_host($url = "") { 

    $parse_url = parse_url(trim($url));

    if(isset($parse_url['host'])) {
        $host = $parse_url['host'];
    }

    else {
        $path = explode('/', $parse_url['path']);
        $host = $path[0];
    }
    
    return trim($host); 
}

function cl_db_insert($table = false, $data = array()) {
    global $db;

    $id = $db->insert($table, $data);

    return $id;
}

function cl_db_update($table = false, $data = array(), $fields = array()) {
    global $db;

    if (empty($data)) {
        return false;
    }

    foreach ($data as $k => $v) {
        $db = $db->where($k, $v);
    }

    return $db->update($table, $fields);
}

function cl_db_get_item($table = false, $data = array(), $fields = null) {
    global $db;

    if (empty($data)) {
        return false;
    }

    foreach ($data as $k => $v) {
        $db = $db->where($k, $v);
    }

    $item = $db->getOne($table, $fields);

    if (cl_queryset($item)) {
        return $item;
    }

    return false;
}

function cl_db_get_total($table = false, $data = array(), $fields = "COUNT(*)") {
    global $db;

    if (empty($data)) {
        return 0;
    }

    foreach ($data as $k => $v) {
        $db = $db->where($k, $v);
    }

    $total = $db->getValue($table, $fields);

    if (is_posnum($total)) {
        return $total;
    }

    return 0;
}

function cl_db_get_items($table = false, $data = array(), $limit = null, $fields = null) {
    global $db;

    if (empty($data)) {
        return false;
    }

    foreach ($data as $k => $v) {
        $db = $db->where($k, $v);
    }

    $item = $db->get($table, $limit, $fields);

    if (cl_queryset($item)) {
        return $item;
    }

    return false;
}

function cl_db_delete_item($table = false, $data = array()) {
    global $db;

    if (empty($data)) {
        return false;
    }

    foreach ($data as $k => $v) {
        $db = $db->where($k, $v);
    }

    $qr = $db->delete($table);

    return $qr;
}

function cl_is_valid_poll($poll = array()) {

    if (empty($poll) || is_array($poll) != true) {
        return false;
    }
    else if(count($poll) > 4) {
        return false;
    }

    else {
        foreach ($poll as $row) {
            if (empty($row["value"]) || is_string($row["value"]) != true || len_between($row["value"], 1, 25) != true) {
                return false;
            }
        }

        return true;
    }
}

function cl_get_youtube_video_id($video_url = "") {
    if (preg_match('#(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})#i', $video_url, $matches)) {
        if (not_empty($matches)) {
            return $matches["1"];
        }
    }

    return false;
}

function cl_get_vimeo_video_id($video_url = "") {
    if (preg_match("#https?://vimeo.com/([0-9]+)#i", $video_url, $matches)) {
        if (not_empty($matches)) {
            return $matches["1"];
        }
    }

    return false;
}

function cl_is_google_mapurl($map_url = "") {
    if (preg_match("/@(-?\d+\.\d+),(-?\d+\.\d+),(\d+\.?\d?)+z/i", $map_url)) {
        return true;
    }

    return false;
}

function cl_show_feed_gad() {
    try {
        $gad_counter = @file_get_contents(cl_full_path("core/components/vars/gad_counter.num"));
        $gad_counter = intval($gad_counter);
        $show_ad     = false;

        if ($gad_counter >= GADS_TIMELINE_FREQ) {
            $gad_counter = 0;
            $show_ad     = true;
        }

        else {
            $gad_counter = ($gad_counter + 1);
        }

        @file_put_contents(cl_full_path("core/components/vars/gad_counter.num"), $gad_counter);

        return $show_ad;
    } 

    catch (Exception $e) {
        return false;
    }
}

function cl_time2str($ptime = 0) {
    $etime = (time() - $ptime);

    if ($etime < 1) {
        return cl_translate("Just now");
    }

    $a = array(
        (365 * 24 * 60 * 60) => "year",
        (30 * 24 * 60 * 60) => "month",
        (24 * 60 * 60) => "day",
        (60 * 60) => "hour",
        60 => "minute",
        1 => "second"
    );

    $a_plural = array(
        "year"   => "years",
        "month"  => "months",
        "day"    => "days",
        "hour"   => "hours",
        "minute" => "minutes",
        "second" => "seconds"
    );

    foreach ($a as $secs => $str) {
        $diff = ($etime / $secs);

        if ($diff >= 1) {
            $diff = round($diff);
            return cl_strf("%s %s %s",$diff, (($diff > 1) ? cl_translate($a_plural[$str]) : cl_translate($str)), cl_translate("ago"));
        }
    }
}

function cl_date($format = "", $date = 0) {
    global $cl;

    $months = array(
        "January" => cl_translate("January"),
        "February" => cl_translate("February"),
        "March" => cl_translate("March"),
        "April" => cl_translate("April"),
        "May" => cl_translate("May"),
        "June" => cl_translate("June"),
        "July" => cl_translate("July"),
        "August" => cl_translate("August"),
        "September" => cl_translate("September"),
        "October" => cl_translate("October"),
        "November" => cl_translate("November"),
        "December" => cl_translate("December"),
        "Jan" => cl_translate("Jan"),
        "Feb" => cl_translate("Feb"),
        "Mar" => cl_translate("Mar"),
        "Apr" => cl_translate("Apr"),
        "May" => cl_translate("May"),
        "June" => cl_translate("June"),
        "July" => cl_translate("July"),
        "Aug" => cl_translate("Aug"),
        "Sept" => cl_translate("Sept"),
        "Oct" => cl_translate("Oct"),
        "Nov" => cl_translate("Nov"),
        "Dec" => cl_translate("Dec")
    );

    $date = date($format, $date);

    foreach($months as $k => $v) {
        if (preg_match("/{$k}/i", $date)) {
            $date = str_ireplace($k, $months[$k], $date); break;
        }
    }

    return $date;
}

function cl_is_valid_og($og = array()) {
    global $cl;

    if (is_array($og)) {
        if (isset($og["title"]) && isset($og["description"]) && isset($og["image"]) && isset($og["type"]) && isset($og["url"])) {
            return true;
        }
    }

    return false;
}

function cl_decode_array($arr = array()) {
    if (is_array($arr)) {
        return $arr;
    }

    else if(is_string($arr)) {
        $arr = json($arr);

        if (is_array($arr) != true) {
            $arr = array();
        }

        return $arr;
    }
    else{
        return array();
    }
}

function cl_get_uicol() {
    global $cl;

    return $cl["ui_rand_colors"][array_rand($cl["ui_rand_colors"])];
}

function cl_get_custom_code($type = false) {
    global $cl;

    if ($type == "headerjs") {
        $code = file_get_contents(cl_full_path(cl_strf("themes/%s/statics/custom_code/header.js", $cl["config"]["theme"])));

        return $code;
    }

    elseif ($type == "footerjs") {
        $code = file_get_contents(cl_full_path(cl_strf("themes/%s/statics/custom_code/footer.js", $cl["config"]["theme"])));

        return $code;
    }

    elseif ($type == "headercss") {
        $code = file_get_contents(cl_full_path(cl_strf("themes/%s/statics/custom_code/header.css", $cl["config"]["theme"])));

        return $code;
    }
}

// RESTRICT USER FUNCTION
function cl_check_ip_restriction($visitor_ip) {
    // echo '<pre>';print_r($visitor_ip);'</pre>';die;
    global $db;

    $query = "SELECT ip_address FROM " . T_RESTRICTED_IPS;
	$results = $db->query($query);

    $blocked_ips = [];
    foreach ($results as $result) {
        $blocked_ips[] = $result['ip_address'];
    }

    if (in_array($visitor_ip, $blocked_ips)) {
        header('HTTP/1.0 403 Forbidden');
        echo 'You are not allowed to access this website.';
        exit;
    }
}