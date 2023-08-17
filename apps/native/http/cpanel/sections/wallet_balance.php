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


$user_id = fetch_or_get($_GET["id"], false);
$user_id = (is_posnum($user_id)) ? $user_id : 0;
$cl["user_data"] = cl_raw_user_data($user_id);

if (not_empty($cl["user_data"])) {
	$cl["app_statics"] = array(
		"scripts" => array(
			cl_static_file_path("statics/js/libs/jquery-plugins/jquery.form-v4.2.2.min.js")
		)
	);

	$cl['http_res'] = cl_template("cpanel/assets/wallet_balance/content");
}
else {
	cl_redirect("404");
}