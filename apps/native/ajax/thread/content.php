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

require_once(cl_full_path("core/apps/thread/app_ctrl.php"));
// 28 april
require_once(cl_full_path("core/components/user.php"));
require_once(cl_full_path("core/components/post.php"));

if ($action == 'load_thread_replys') {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $thread_id        = fetch_or_get($_GET['thread_id'], 0);
    $replys_list      = array();
    $html_arr         = array();

    if (is_posnum($offset) && is_posnum($thread_id)) {
    	
    	$replys_list = cl_get_thread_child_posts($thread_id, 30, $offset, 'lt');

    	if (not_empty($replys_list)) {
			foreach ($replys_list as $cl['li']) {
				$html_arr[] = cl_template('timeline/post');
			}

			$data['status'] = 200;
			$data['html']   = implode("", $html_arr);
		}
    }
}
// 28 april
elseif($action == 'view_count_update') {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $PostId           = fetch_or_get($_POST['post_id'], 0);
	
    if (is_posnum($PostId)) {
		
		$postData	=	cl_raw_post_data($PostId);
		
		if (not_empty($postData)) {
			
			$data['status'] = 200;
			
			// $views   = cl_session('post_view');
			
			// if (is_array($views) != true) {
			// 	$views = array();
			// }
			// if (in_array($PostId, $views) != true) {
			// 	array_push($views, $PostId);
			
			// 	cl_session('post_view', $views);
			
			// }
			if(cl_update_post_data($PostId, array('views' => ($postData['views'] += 1)))){
				$postData = cl_raw_post_data($PostId);
			}
		}
		$data['data'] = $postData['views'];
    }
}
