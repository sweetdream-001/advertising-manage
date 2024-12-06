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

// $cl["app_statics"] = array(
// 	"scripts" => array(
// 		cl_static_file_path("statics/js/libs/jquery-plugins/jquery.form-v4.2.2.min.js")
// 	)
// );

require_once(cl_full_path("core/apps/cpanel/ip_restriction/app_ctrl.php"));

$cl['restricted_ips'] = cl_admin_get_restricted_ips(array('limit' => 10));
$cl['http_res'] = cl_template("cpanel/assets/ip_restrictions/content");