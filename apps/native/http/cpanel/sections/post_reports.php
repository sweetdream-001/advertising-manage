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

require_once(cl_full_path("core/apps/cpanel/post_reports/app_ctrl.php"));

$cl['total_reports'] = cl_admin_get_total_post_reports();
$cl['post_reports']  = cl_admin_get_post_reports();
$cl['http_res']      = cl_template("cpanel/assets/post_reports/content");