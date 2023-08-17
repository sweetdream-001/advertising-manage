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

$custom_app_name = (isset($_GET["custom_app_name"])) ? $_GET["custom_app_name"] : "404";

if ($custom_app_name == "404") {
	cl_redirect("404");
}

else{
	if (file_exists(cl_strf("apps/native/http/%s/content.php", $custom_app_name))) {
		include_once(cl_strf("apps/native/http/%s/content.php", $custom_app_name));
	}
	else{
		cl_redirect("404");
	}
}