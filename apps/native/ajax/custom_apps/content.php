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


$custom_app_name = (isset($_GET["action"])) ? $_GET["action"] : false;

$subaction = (isset($_GET["subaction"])) ? $_GET["subaction"] : false;

if (not_empty($custom_app_name) && not_empty($subaction) && file_exists(cl_strf("apps/native/ajax/%s/content.php", $custom_app_name))) {
	include_once(cl_strf("apps/native/ajax/%s/content.php", $custom_app_name));
}

else{
	$data =  array(
        "status"   => "400s",
        "err_code" => "invalid_endpoint",
        "message"  => "Invalid endpoint error on API call"
    );
}