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

require_once(cl_full_path("core/apps/cpanel/banktrans_receipts/app_ctrl.php"));

$cl['requests_total'] = cl_admin_get_banktrans_requests_total();    
$cl['requests']       = cl_admin_get_banktrans_requests(array('limit' => 7));    
$cl['http_res']       = cl_template("cpanel/assets/banktrans_receipts/content");