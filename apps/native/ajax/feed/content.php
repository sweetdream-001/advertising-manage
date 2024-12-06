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

if ($action == 'load_more') {
    if (not_empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
    	$data['err_code'] = 0;
        $data['status']   = 400;
        $offset           = fetch_or_get($_GET['offset'], 0);
        $html_arr         = array();

        if (is_posnum($offset)) {

            require_once(cl_full_path("core/apps/feed/app_ctrl.php"));
        	
        	$feed_ls = cl_get_guest_feed($offset, 3000);

        	if (not_empty($feed_ls)) {
    			foreach ($feed_ls as $cl['li']) {
    				$html_arr[] = cl_template('timeline/post');
    			}

    			$data['status'] = 200;
    			$data['html']   = implode("", $html_arr);
    		}
        }
    }
}