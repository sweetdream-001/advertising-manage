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


require_once(cl_full_path("core/apps/cpanel/dashboard/app_ctrl.php"));
require_once(cl_full_path("core/apps/ads/app_ctrl.php"));
require_once(cl_full_path("core/apps/cpanel/affiliate_payouts/app_ctrl.php"));
require_once(cl_full_path("core/apps/cpanel/account_verification/app_ctrl.php"));

$cl["app_statics"] = array(
	"scripts" => array(
		cl_static_file_path("apps/cpanel/statics/plugins/jquery-countto/jquery.countTo.js"),
		cl_static_file_path("apps/cpanel/statics/plugins/chartjs/Chart.bundle.js")
	)
);
require_once(cl_full_path("core/apps/cpanel/dashboard/app_ctrl.php"));
require_once(cl_full_path("core/apps/cpanel/users/app_ctrl.php"));
$cl['total_users']  = cl_admin_total_users();
$cl['total_posts']  = cl_admin_total_posts();
$cl['total_images'] = cl_admin_total_posts('image');
$cl['total_videos'] = cl_admin_total_posts('video');
$cl['statistics']   = cl_admin_annual_main_stats();
$cl['site_users']  = cl_admin_get_users(array('limit' => 3));  
$cl["active_ads"]   = cl_get_total_ads('active'); 
$cl["inactive_ads"] = cl_get_total_ads('inactive');  
$cl['total_requests'] = cl_get_affiliate_payouts_total(); 
$cl['requests_total'] = cl_admin_get_verification_requests_total();
$cl['http_res']     = cl_template("cpanel/assets/dashboard/content");