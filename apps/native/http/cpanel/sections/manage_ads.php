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

require_once(cl_full_path("core/apps/cpanel/ads/app_ctrl.php"));

// Pagination setup
$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0) ? intval($_GET['limit']) : 10;
$page  = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
// Fetch total ad count
$total_ads = $db->where('status', 'active')->getValue(T_ADS, 'COUNT(*)');

$cl['user_ads']     = cl_admin_get_user_ads(array('limit' => $limit, 'offset' => $offset));
$cl['current_page'] = $page;
$cl['total_pages']  = ceil($total_ads / $limit);
$cl['http_res']     = cl_template("cpanel/assets/manage_ads/content");