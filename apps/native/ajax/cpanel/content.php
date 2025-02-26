<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                                    @
# @ Author_url 1: https://jooj.us                                           @
# @ Author_url 2: http://jooj.us/twitter-clone                              @
# @ Author E-mail: sales@jooj.us                                            @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

//echo $action;exit;
//echo $action;exit("Here123");

if (empty($cl['is_admin'])) {
	$data['status'] = 400;
    $data['error']  = 'Invalid access token';
}


else if ($action == 'save_settings') {	
	$data['status']    = 400;
	$data['err_field'] = null;
	$raw_configs       = $db->get(T_CONFIGS);
	$raw_configs       = ((cl_queryset($raw_configs)) ? $raw_configs : array());
	//echo "<pre>";print_r($raw_configs);exit;

	if ($raw_configs) {
		require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

		foreach ($raw_configs as $config_data) {
			if (isset($_POST[$config_data['name']])) {

				if (in_array($config_data['name'], array("google_ad_horiz", "google_ad_vert", "google_analytics"))) {
					$conf_new_val = htmlspecialchars($_POST[$config_data['name']]);
				}

				else {
					$conf_new_val = cl_text_secure($_POST[$config_data['name']]);
				}

				if ($config_data['regex']) {
					if (preg_match($config_data['regex'], $conf_new_val)) {
						cl_admin_save_config($config_data['name'], $conf_new_val);
					}

					else {
						$field_label       = $config_data['title'];
						$data['message']   = cl_strf('Invalid value of field: %s', $field_label);
						$data['err_field'] = $config_data['name']; break;
					}
				}
				else {
					cl_admin_save_config($config_data['name'], $conf_new_val);
				}
			}
		}

		if (empty($data['err_field'])) {
			$data['status'] = 200;
		}
	}
}
else if ($action == 'save_settings_faq') {	
	$data['status']    = 400;
	$data['err_field'] = null;
	$raw_configs       = $db->get(T_CONFIGS);
	$raw_configs       = ((cl_queryset($raw_configs)) ? $raw_configs : array());

	if ($raw_configs) {

		require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

		foreach ($raw_configs as $config_data) {
			if (isset($_POST[$config_data['name']])) {

			
                 if ($config_data['name'] == 'faq_page') {
					$conf_new_val = htmlspecialchars_decode($_POST[$config_data['name']]);
				}
                
				else {
					$conf_new_val = cl_text_secure($_POST[$config_data['name']]);
				}

				if ($config_data['regex']) {
					if (preg_match($config_data['regex'], $conf_new_val)) {
						cl_admin_save_config($config_data['name'], $conf_new_val);
					}

					else {
						$field_label       = $config_data['title'];
						$data['message']   = cl_translate('Invalid value of field: {@field_name@}', array('field_name' => $field_label));
						$data['err_field'] = $config_data['name']; break;
					}
				}
				else {
					cl_admin_save_config($config_data['name'],$conf_new_val);
				}
			}
		}

		if (empty($data['err_field'])) {
			$data['status'] = 200;
		}
	}
    
}
// Terms Page
else if ($action == 'save_settings_terms') {	
	$data['status']    = 400;
	$data['err_field'] = null;
	$raw_configs       = $db->get(T_CONFIGS);
	$raw_configs       = ((cl_queryset($raw_configs)) ? $raw_configs : array());

	if ($raw_configs) {

		require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

		foreach ($raw_configs as $config_data) {
			if (isset($_POST[$config_data['name']])) {

			
                 if ($config_data['name'] == 'terms_page') {
					$conf_new_val = htmlspecialchars_decode($_POST[$config_data['name']]);
				}
                
				else {
					$conf_new_val = cl_text_secure($_POST[$config_data['name']]);
				}

				if ($config_data['regex']) {
					if (preg_match($config_data['regex'], $conf_new_val)) {
						cl_admin_save_config($config_data['name'], $conf_new_val);
					}

					else {
						$field_label       = $config_data['title'];
						$data['message']   = cl_translate('Invalid value of field: {@field_name@}', array('field_name' => $field_label));
						$data['err_field'] = $config_data['name']; break;
					}
				}
				else {
					cl_admin_save_config($config_data['name'],$conf_new_val);
				}
			}
		}

		if (empty($data['err_field'])) {
			$data['status'] = 200;
		}
	}
    
}
// Privacy Page
else if ($action == 'save_settings_privacy') {	
	$data['status']    = 400;
	$data['err_field'] = null;
	$raw_configs       = $db->get(T_CONFIGS);
	$raw_configs       = ((cl_queryset($raw_configs)) ? $raw_configs : array());

	if ($raw_configs) {

		require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

		foreach ($raw_configs as $config_data) {
			if (isset($_POST[$config_data['name']])) {

			
                 if ($config_data['name'] == 'privacy_page') {
					$conf_new_val = htmlspecialchars_decode($_POST[$config_data['name']]);
				}
                
				else {
					$conf_new_val = cl_text_secure($_POST[$config_data['name']]);
				}

				if ($config_data['regex']) {
					if (preg_match($config_data['regex'], $conf_new_val)) {
						cl_admin_save_config($config_data['name'], $conf_new_val);
					}

					else {
						$field_label       = $config_data['title'];
						$data['message']   = cl_translate('Invalid value of field: {@field_name@}', array('field_name' => $field_label));
						$data['err_field'] = $config_data['name']; break;
					}
				}
				else {
					cl_admin_save_config($config_data['name'],$conf_new_val);
				}
			}
		}

		if (empty($data['err_field'])) {
			$data['status'] = 200;
		}
	}
    
}
else if ($action == 'save_settings_cookies') {	
	$data['status']    = 400;
	$data['err_field'] = null;
	$raw_configs       = $db->get(T_CONFIGS);
	$raw_configs       = ((cl_queryset($raw_configs)) ? $raw_configs : array());

	if ($raw_configs) {

		require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

		foreach ($raw_configs as $config_data) {
			if (isset($_POST[$config_data['name']])) {

			
                 if ($config_data['name'] == 'cookies_page') {
					$conf_new_val = htmlspecialchars_decode($_POST[$config_data['name']]);
				}
                
				else {
					$conf_new_val = cl_text_secure($_POST[$config_data['name']]);
				}

				if ($config_data['regex']) {
					if (preg_match($config_data['regex'], $conf_new_val)) {
						cl_admin_save_config($config_data['name'], $conf_new_val);
					}

					else {
						$field_label       = $config_data['title'];
						$data['message']   = cl_translate('Invalid value of field: {@field_name@}', array('field_name' => $field_label));
						$data['err_field'] = $config_data['name']; break;
					}
				}
				else {
					cl_admin_save_config($config_data['name'],$conf_new_val);
				}
			}
		}

		if (empty($data['err_field'])) {
			$data['status'] = 200;
		}
	}
    
}
else if ($action == 'save_settings_about') {	
	$data['status']    = 400;
	$data['err_field'] = null;
	$raw_configs       = $db->get(T_CONFIGS);
	$raw_configs       = ((cl_queryset($raw_configs)) ? $raw_configs : array());

	if ($raw_configs) {

		require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

		foreach ($raw_configs as $config_data) {
			if (isset($_POST[$config_data['name']])) {

			
                 if ($config_data['name'] == 'about_page') {
					$conf_new_val = htmlspecialchars_decode($_POST[$config_data['name']]);
				}
                
				else {
					$conf_new_val = cl_text_secure($_POST[$config_data['name']]);
				}

				if ($config_data['regex']) {
					if (preg_match($config_data['regex'], $conf_new_val)) {
						cl_admin_save_config($config_data['name'], $conf_new_val);
					}

					else {
						$field_label       = $config_data['title'];
						$data['message']   = cl_translate('Invalid value of field: {@field_name@}', array('field_name' => $field_label));
						$data['err_field'] = $config_data['name']; break;
					}
				}
				else {
					cl_admin_save_config($config_data['name'],$conf_new_val);
				}
			}
		}

		if (empty($data['err_field'])) {
			$data['status'] = 200;
		}
	}
    
}
else if ($action == 'get_users') {
	require_once(cl_full_path("core/apps/cpanel/users/app_ctrl.php"));

	$filter_data      = fetch_or_get($_POST['filter'], array());
	$offset_to        = fetch_or_get($_POST['dir'], 'none');
	$offset_lt        = ((is_posnum($_POST['offset_lt'])) ? intval($_POST['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_POST['offset_gt'])) ? intval($_POST['offset_gt']) : 0);
	$users            = array();
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$users          = cl_admin_get_users(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC',
			'filter'    => $filter_data
		));

		$users = array_reverse($users);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$users          = cl_admin_get_users(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC',
			'filter'    => $filter_data
		));
	}

	if (not_empty($users)) {
		foreach ($users as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/users/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if ($action == 'search_users') {

	require_once(cl_full_path("core/apps/cpanel/users/app_ctrl.php"));

	$filter_data      = fetch_or_get($_POST['filter'], array());
	$data['err_code'] = 0;
	$html_arr         = array();
	$users            = cl_admin_get_users(array(
		'limit'       => 7,
		'filter'      => $filter_data
	));

	if (not_empty($users)) {
		foreach ($users as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/users/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
	else{
		$data['status'] = 404;
		$data['html']   = cl_template('cpanel/assets/users/includes/filter404');
	}
}

else if ($action == 'get_posts') {

	require_once(cl_full_path("core/apps/cpanel/posts/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$posts            = array();
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$posts          = cl_admin_get_posts(array(
			'limit'     => 10,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$posts = array_reverse($posts);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$posts          = cl_admin_get_posts(array(
			'limit'     => 10,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($posts)) {
		foreach ($posts as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/publications/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if($action == 'delete_user') {
	$data['status']   = 404;
	$data['err_code'] = 0;
	$user_id          = fetch_or_get($_POST['id'], 0);

	if (is_posnum($user_id)) {
		$data['status']      = 200;
		$data['status_code'] = (cl_delete_user_data($user_id) == true) ? 1 : 0;
	}
}

else if($action == 'toggle_user_status') {
	$data['status']   = 404;
	$data['err_code'] = 0;
	$user_id          = fetch_or_get($_POST['id'], 0);

	if (is_posnum($user_id)) {
		$udata = cl_raw_user_data($user_id);

		if (not_empty($udata)) {
			$data['status']  = 200;
			$data['message'] = "Your changes has been successfully saved!";
			$status          = (($udata['active'] == '1') ? '2' : '1' );

			cl_update_user_data($user_id, array(
				'active' => $status
			));

			if ($status == '2') {
				cl_signout_user_by_id($user_id);
			}
		}
	}
}

else if($action == 'toggle_user_type') {
	$data['status']   = 404;
	$data['err_code'] = 0;
	$user_id          = fetch_or_get($_POST['id'], 0);

	if (is_posnum($user_id)) {
		$udata = cl_raw_user_data($user_id);

		if (not_empty($udata)) {
			$data['status']  = 200;
			$data['message'] = "Your changes has been successfully saved!";
			$user_type       = (($udata['admin'] == '1') ? '0' : '1');

			cl_update_user_data($user_id, array(
				'admin' => $user_type
			));
		}
	}
}

else if($action == 'verify_user') {
	$data['status']   = 404;
	$data['err_code'] = 0;
	$user_id          = fetch_or_get($_POST['id'], 0);

	if (is_posnum($user_id)) {
		$udata = cl_raw_user_data($user_id);

		if (not_empty($udata)) {
			$data['status']  = 200;
			$data['message'] = "Your changes has been successfully saved!";
			$is_verified     = (($udata['verified'] == '1') ? '0' : '1');

			cl_update_user_data($user_id, array(
				'verified' => $is_verified
			));
		}
	}
}

else if($action == 'delete_post') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $post_id          = fetch_or_get($_POST['id'], 0);

    if (is_posnum($post_id)) {
        $post_data = cl_raw_post_data($post_id);

        if (not_empty($post_data)) {

            $post_owner = cl_raw_user_data($post_data['user_id']);

            if (not_empty($post_owner)) {
                if ($post_data['target'] == 'publication') {

                    $posts_total = ($post_owner['posts'] -= 1);
                    $posts_total = ((is_posnum($posts_total)) ? $posts_total : 0);

                    cl_update_user_data($post_data['user_id'], array(
                        'posts' => $posts_total
                    ));

                    $db = $db->where('publication_id', $post_id);
                    $qr = $db->delete(T_POSTS);
                }

                else {
                    cl_update_thread_replys($post_data['thread_id'], 'minus');
                }
                
                cl_recursive_delete_post($post_id);
                
                $data['status'] = 200;
            }
        }
    }
}

else if($action =='create_backup') {

	require_once(cl_full_path("core/apps/cpanel/backups/app_ctrl.php"));
	
	require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

	$data['err_code'] = 'failed_to_create_backup';
	$data['status']   = 500;
	$new_backup       = cl_admin_create_backup();

	if ($new_backup) {
		$time                = time();
		$data['status']      = 200;
		$data['err_code']    = 0;
		$data['last_backup'] = date('d F, Y - h:m', $time);

		cl_admin_save_config('last_backup', $time);
	}
}

else if ($action == 'get_account_verifications') {

	require_once(cl_full_path("core/apps/cpanel/account_verification/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$requests       = cl_admin_get_verification_requests(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$requests = array_reverse($requests);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$requests       = cl_admin_get_verification_requests(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($requests)) {
		foreach ($requests as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/account_verification/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if ($action == 'get_verifreq_data') {

	require_once(cl_full_path("core/apps/cpanel/account_verification/app_ctrl.php"));

	$request_id       = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$cl['req_data']   = cl_admin_get_verification_request_data($request_id);

	if (not_empty($cl['req_data'])) {
		$data['status'] = 200;
		$data['html']   = cl_template('cpanel/assets/account_verification/modals/popup_ticket');
	}
}

else if ($action == 'delete_verifreq_data') {
	$request_id       = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$db               = $db->where('id', $request_id);
	$req_data         = $db->getOne(T_VERIFICATIONS);

	if (cl_queryset($req_data)) {
		$data['status'] = 200;
		$db             = $db->where('id', $request_id);
		$qr             = $db->delete(T_VERIFICATIONS);

		cl_delete_media($req_data['video_message']);

		cl_update_user_data($req_data['user_id'], array(
			'verified' => '0'
		));
	}
	else {
		$data['status']  = 400;
		$data['message'] = "An error occurred while processing your request. Please try again later.";
	}
}

else if ($action == 'verify_user_account') {
	$request_id       = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$db               = $db->where('id', $request_id);
	$req_data         = $db->getOne(T_VERIFICATIONS);

	if (cl_queryset($req_data)) {
		$data['status']  = 200;
		$data['message'] = "Account has been verified successfully!";
		$db              = $db->where('id', $request_id);
		$qr              = $db->delete(T_VERIFICATIONS);

		cl_delete_media($req_data['video_message']);

		cl_update_user_data($req_data['user_id'], array(
			'verified' => '1'
		));

		cl_notify_user(array(
            'subject'  => 'verified',
            'user_id'  => $req_data['user_id'],
            'entry_id' => 0
        ));
	}
	else {
		$data['status']  = 400;
		$data['message'] = "An error occurred while processing your request. Please try again later.";
	}
}

else if($action == "update_sitemap") {
	$data['status']   = 404;
	$data['err_code'] = 0;
	$data['errors']   = array();

	if (is_writable('sitemap') != true) {
		$data['err_code'] = "permission_denied";
		$data['message']  = "The sitemaps storage folder does not exists or not writable!";
	}

	else if(is_writable('sitemap/sitemap-index.xml') != true) {
		$data['err_code'] = "permission_denied";
		$data['message']  = "The sitemap-index.xml does not exists or not writable!";
	}

	else if(is_writable('sitemap/maps') != true) {
		$data['err_code'] = "permission_denied";
		$data['message']  = "The sitemap/maps forder does not exists or not writable!";
	}

	else {

		require_once(cl_full_path("core/apps/cpanel/sitemap/app_ctrl.php"));

		$old_maps = glob('sitemap/maps/*.xml');
		$old_maps = ((is_array($old_maps) && not_empty($old_maps)) ? $old_maps : array());
		$maps     = 1;
		$posts    = cl_admin_get_publication_indexes();
		$users    = cl_admin_get_user_indexes();

		

		if (not_empty($old_maps)) {
			foreach($old_maps as $old_site_map){
			    try {
			    	@unlink($old_site_map);
			    } catch (Exception $e) { /* pass */ }
			}
		}

		if (not_empty($posts)) {
			$posts = array_chunk($posts, 1000);

			foreach ($posts as $cl['sitemap_entries']) {
				$map_url  = cl_strf("sitemap/maps/sitemap-%d.xml", $maps);
				$map_code = cl_sitemap('temps/sitemap');
				$map_code = trim($map_code);
				$map_code = str_replace("{%xml_version%}", '<?xml version="1.0" encoding="UTF-8"?>', $map_code);
				$exe_code = file_put_contents($map_url, $map_code);

				if ($exe_code) {
					$maps += 1;
				}

				else {
					$data['errors'][] = array(
						'file_index' => $maps,
						'file_path' => $map_url,
						'message' => "Failed to save sitemap file."
					);
				}
			}
		}

		if (not_empty($users)) {
			$users = array_chunk($users, 1000);

			foreach ($users as $cl['sitemap_entries']) {
				$map_url  = cl_strf("sitemap/maps/sitemap-%d.xml", $maps);
				$map_code = cl_sitemap('temps/sitemap');
				$map_code = trim($map_code);
				$map_code = str_replace("{%xml_version%}", '<?xml version="1.0" encoding="UTF-8"?>', $map_code);
				$exe_code = file_put_contents($map_url, $map_code);

				if ($exe_code) {
					$maps += 1;
				}

				else {
					$data['errors'][] = array(
						'file_index' => $maps,
						'file_path' => $map_url,
						'message' => "Failed to save sitemap file."
					);
				}
			}
		}

		if($maps > 1) {
			$cl['map_indexes'] = $maps;
			$sitemap_index     = cl_sitemap('temps/index');
			$sitemap_index     = trim($sitemap_index);
			$sitemap_index     = str_replace("{%xml_version%}", '<?xml version="1.0" encoding="UTF-8"?>', $sitemap_index);
			$exe_code          = file_put_contents('sitemap/sitemap-index.xml', trim($sitemap_index));

			if ($exe_code) {
				$data['status']       = 200;
				$data['last_sitemap'] = date('d F, Y - h:m');

				$db = $db->where('name', 'sitemap_update');
		        $qr = $db->update(T_CONFIGS, array(
		        	'value' => time()
		        ));
			}

			else {
				$data['errors'][] = array(
					'file_index' => $maps,
					'file_path' => $map_url,
					'message' => "Failed to save sitemap file."
				);
			}
		}
	}
}

else if ($action == 'get_account_reports') {

	require_once(cl_full_path("core/apps/cpanel/account_reports/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$reports        = cl_admin_get_profile_reports(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$reports = array_reverse($reports);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$reports        = cl_admin_get_profile_reports(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($reports)) {
		foreach ($reports as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/account_reports/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if ($action == 'get_account_report_data') {

	require_once(cl_full_path("core/apps/cpanel/account_reports/app_ctrl.php"));

	$report_id        = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$cl['rep_data']   = cl_admin_get_account_report_data($report_id);

	if (not_empty($cl['rep_data'])) {
		$data['status']  = 200;
		$data['is_seen'] = $cl['rep_data']['seen'];
		$data['html']    = cl_template('cpanel/assets/account_reports/modals/popup_ticket');
	}
}

else if($action == 'delete_account_report_data') {
	$report_id        = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;

	if (is_posnum($report_id)) {

		require_once(cl_full_path("core/apps/cpanel/account_reports/app_ctrl.php"));

		$db             = $db->where('id', $report_id);
		$qr             = $db->delete(T_PROF_REPORTS);
		$data['status'] = 200;
		$data['total']  = cl_admin_get_total_profile_reports();;
	}
}

else if ($action == 'get_affiliate_payouts') {

	require_once(cl_full_path("core/apps/cpanel/affiliate_payouts/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$requests       = cl_get_affiliate_payouts(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$requests = array_reverse($requests);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$requests       = cl_get_affiliate_payouts(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($requests)) {
		foreach ($requests as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/affiliate_payouts/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if ($action == 'delete_affiliate_payout') {
	$request_id       = fetch_or_get($_POST['id'], 'none');
	$data['status']   = 400;
	$data['err_code'] = 0;

	if (is_posnum($request_id)) {
		$data['status'] = 200;
		$db             = $db->where('id', $request_id);
		$qr             = $db->delete(T_AFF_PAYOUTS);
	}
}

else if ($action == 'update_affiliate_payout_status') {
	$request_id       = fetch_or_get($_POST['id'], 'none');
	$data['status']   = 400;
	$data['err_code'] = 0;

	if (is_posnum($request_id)) {
		$payout_req = cl_db_get_item(T_AFF_PAYOUTS, array("id" => $request_id));

		if (not_empty($payout_req)) {
			$data['status'] = 200;

			cl_update_user_data($payout_req["user_id"], array(
				"aff_bonuses" => 0
			));

			cl_db_update(T_AFF_PAYOUTS, array(
				'id' => $request_id
			), array(
				'status' => 'paid'
			));
		}
	}
}

else if ($action == 'get_user_ads') {

	require_once(cl_full_path("core/apps/cpanel/ads/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();
	if ($offset_to == 'up' && $offset_lt) {
		$user_ads       = cl_admin_get_user_ads(array(
			'limit'     => 10,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$user_ads = array_reverse($user_ads);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$user_ads       = cl_admin_get_user_ads(array(
			'limit'     => 10,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($user_ads)) {
		foreach ($user_ads as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/manage_ads/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}
else if ($action == 'show_user_ad') {
	$data['err_code'] = 0;
    $data['status']   = 200;
    $ad_id            = fetch_or_get($_POST['id'], false);
    $ad_data          = cl_raw_ad_data($ad_id);
	
    $data['data'] = $ad_data;
}
else if($action == 'delete_user_ad') {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $ad_id            = fetch_or_get($_POST['id'], false);
    $ad_data          = cl_raw_ad_data($ad_id);

    if (not_empty($ad_data)) {
        cl_delete_media($ad_data['cover']);
        cl_db_delete_item(T_ADS, array("id" => $ad_id));

        $data['status'] = 200;
    }
}

else if($action == 'approve_user_ad') {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $ad_id            = fetch_or_get($_POST['id'], false);
    $ad_data          = cl_raw_ad_data($ad_id);

    if (not_empty($ad_data)) {
        $data['status'] = 200;

		if ($ad_data['approved'] == "N") {
			cl_update_ad_data($ad_id, array(
				"approved" => "Y"
			));
			$data['approved'] = "Y";
		} else {
			cl_update_ad_data($ad_id, array(
				"approved" => "N"
			));
			$data['approved'] = "N";
		}

        cl_notify_user(array(
	        'subject'  => 'ad_approval',
	        'user_id'  => $ad_data["user_id"],
	        'entry_id' => $ad_data["id"]
	    ));
    }
}

else if($action == 'delete_old_swifts') {
	require_once(cl_full_path("core/apps/cpanel/swifts/app_ctrl.php"));

	cl_admin_delete_user_old_swifts();

	$data['status'] = 200;
}

else if($action == 'activate_theme') {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $theme_name       = fetch_or_get($_POST['theme_name'], false);

    if (not_empty($theme_name) && is_string($theme_name)) {
    	require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

    	cl_admin_save_config("theme", $theme_name);

    	$data['status'] = 200;
    }
}

else if ($action == 'get_post_reports') {

	require_once(cl_full_path("core/apps/cpanel/post_reports/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$reports        = cl_admin_get_post_reports(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$reports = array_reverse($reports);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$reports        = cl_admin_get_post_reports(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($reports)) {
		foreach ($reports as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/post_reports/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if ($action == 'get_post_report_data') {

	require_once(cl_full_path("core/apps/cpanel/post_reports/app_ctrl.php"));

	$report_id        = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$cl['rep_data']   = cl_admin_get_post_report_data($report_id);

	if (not_empty($cl['rep_data'])) {
		$data['status']  = 200;
		$data['is_seen'] = $cl['rep_data']['seen'];
		$data['html']    = cl_template('cpanel/assets/post_reports/modals/popup_ticket');
	}
}

else if($action == 'delete_post_report_data') {
	$report_id        = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;

	if (is_posnum($report_id)) {

		require_once(cl_full_path("core/apps/cpanel/post_reports/app_ctrl.php"));

		cl_db_delete_item(T_PUB_REPORTS, array(
			'id' => $report_id
		));

		$data['status'] = 200;
		$data['total']  = cl_admin_get_total_post_reports();;
	}
}

else if($action == 'as3_api_contest') {
	try {
	    $cl['config']['as3_storage']     = 'on';
	    $cl['config']['as3_onup_delete'] = 'no';       
        $test_aws3_upload                = cl_upload2s3(cl_full_path("upload/default/as3-do-not-delete.png"));

        if ($test_aws3_upload == true) {
        	$data['status']  = 200;
        	$data['message'] = 'Connection test was successful!';
        }

	    else {
	        $data['status']  = 500;
	        $data['message'] = "Error found while processing your request. Please try again later!";
	    }
	}
	catch (Exception $e) {
	    $data['status']  = 400;
	    $data['message'] = $e->getMessage();
	}
}

else if($action == "delete_spam_accounts") {

	$data['status']   = 200;
	$data['err_code'] = 0;

	$db = $db->where("time", (time() - 604800), "<");
	$qr = $db->delete(T_ACC_VALIDS);
}

else if($action == "create_invite_link") {
	$data['status']   = 400;
	$data['err_code'] = 0;
	$expires_at       = fetch_or_get($_POST["expires_at"], false);
	$user_role        = fetch_or_get($_POST["role"], false);
	$link_mnu         = fetch_or_get($_POST["mnu"], false);

	if (is_posnum($expires_at) != true || in_array($expires_at, array("1", "3", "7", "15", "30")) != true) {
		$data['err_code'] = "invalid_expiration_date";
	}

	else if(empty($user_role) || in_array($user_role, array("admin", "user")) != true) {
		$data['err_code'] = "invalid_user_role";
	}

	else if(is_posnum($link_mnu) != true) {
		$data['err_code'] = "invalid_link_mnu";
	}

	else{
		$insert_data = array(
			"code"       => sha1(rand(11111, 99999)) . time() . md5(microtime() . rand(11111, 99999)),
			"role"       => $user_role,
			"mnu"        => $link_mnu,
			"expires_at" => strtotime("+$expires_at day"),
			"time" => time()
		);

		$insert_id = cl_db_insert(T_USER_INVITES, $insert_data);

		if (is_posnum($insert_id)) {
			$data["status"] = 200;
		}
	}
}

else if($action == "update_invite_links_list") {
	$data['status']   = 200;
	$data['err_code'] = 0;

	require_once(cl_full_path("core/apps/cpanel/invite_users/app_ctrl.php"));

	$invite_links  = cl_admin_get_user_invitations();
	$data["links"] = $invite_links;
}

else if($action == "delete_invite_link") {
	$data['status']   = 400;
	$data['err_code'] = 0;
	$link_id          = fetch_or_get($_POST["id"], false);

	if (is_posnum($link_id)) {
		$data['status'] = 200;

		cl_db_delete_item(T_USER_INVITES, array(
			"id" => $link_id
		));
	}
}

else if($action == "toggle_lang_status") {
	$data['status']   = 400;
	$data['err_code'] = 0;
	$lang_stat        = fetch_or_get($_POST["stat"], false);
	$lang_id          = fetch_or_get($_POST["id"], false);

	if (in_array($lang_stat, array("active", "inactive")) && is_posnum($lang_id)) {
		$data['status'] = 200;

		cl_db_update(T_UI_LANGS, array(
			"id" => $lang_id
		), array(
			"status" => ($lang_stat == "active") ? "1" : "0"
		));
	}
}

else if($action == "set_default_lang") {
	$data['status']   = 400;
	$data['err_code'] = 0;
	$lang_id          = fetch_or_get($_POST["id"], false);

	if (is_posnum($lang_id)) {
		$lang_data = cl_db_get_item(T_UI_LANGS, array("id" => $lang_id));

		if (not_empty($lang_data)) {
			$data['status'] = 200;

			cl_db_update(T_CONFIGS, array(
				"name" => "language"
			), array(
				"value" => $lang_data["slug"]
			));
		}
	}
}

else if($action == "delete_lang") {
	$data['status']   = 400;
	$data['err_code'] = 0;
	$lang_id          = fetch_or_get($_POST["id"], false);

	if (is_posnum($lang_id)) {
		$lang_data = cl_db_get_item(T_UI_LANGS, array(
			"id" => $lang_id
		));

		if (not_empty($lang_data) && $lang_data["is_native"] != "1") {
			
			cl_db_delete_item(T_UI_LANGS, array(
				"id" => $lang_id
			));

			$data['status'] = 200;

			try {
				@unlink(cl_full_path(cl_strf("core/langs/%s.json", $lang_data["slug"])));
				@unlink(cl_full_path(cl_strf("core/langs/custom/%s.json", $lang_data["slug"])));
			} 

			catch (Exception $e) {
				/*PASS*/
			}
		}
	}
}

else if($action == "add_new_lanuage") {
	$data['status']   = 400;
	$data['err_code'] = 0;
	$lang_name   = fetch_or_get($_POST["language"], false);
	$lang_status = fetch_or_get($_POST["status"], false);
	$lang_dir    = fetch_or_get($_POST["direction"], false);

	if (empty($lang_name) || in_array($lang_name, array_keys($cl['language_codes'])) != true) {
		$data['err_code'] = "invalid_lang_name";
	}

	else if(empty($lang_status) || in_array($lang_status, array("active", "inactive")) != true) {
		$data['err_code'] = "invalid_lang_status";
	}

	else if(empty($lang_dir) || in_array($lang_dir, array("rtl", "ltr")) != true) {
		$data['err_code'] = "invalid_lang_direction";
	}

	else{
		$data["status"] = 200;

		$lang_file1 = cl_full_path(cl_strf("core/langs/%s.json", $lang_name));
		$lang_file2 = cl_full_path(cl_strf("core/langs/custom/%s.json", $lang_name));

		$v1 = file_put_contents($lang_file1, file_get_contents(cl_full_path("core/langs/default.json")));
		$v2 = file_put_contents($lang_file2, json(array(), true));

		if ($v1 && $v2) {
			cl_db_insert(T_UI_LANGS, array(
				"name" => $cl['language_codes'][$lang_name]["name"],
				"slug" => $lang_name,
				"status" => (($lang_status == "active") ? "1" : "0"),
				"is_rtl" => (($lang_dir == "rtl") ? "Y" : "N")
			));
		}
	}
}

else if($action == 'edit_wallet_balance') {
	$data['status']   = 404;
	$data['err_code'] = 0;
	$user_id          = fetch_or_get($_POST['user_id'], 0);
	$wallet           = fetch_or_get($_POST['wallet'], "0.00");

	if (is_posnum($user_id) && is_numeric($wallet)) {
		$data['status']  = 200;
		$data['message'] = "Your changes has been successfully saved!";

		cl_update_user_data($user_id, array(
			'wallet' => $wallet
		));
	}
	else{
		$data['err_field'] = "wallet";
		$data['message']   = "Something went wrong while trying to save user balance. Please check your details or try again later";
	}
}

else if($action == "check_smtp_server") {
	$data['status']   = 400;
	$data['err_code'] = 0;
	$test_email       = fetch_or_get($_POST['test_email'], false);

	if (empty($test_email) || empty(filter_var(trim($test_email), FILTER_VALIDATE_EMAIL))) {
		$data['err_code'] = "invalid_email_address";
		$data['message']  = "The email address you entered is not valid. Please check your details";
	}
	else{
		$send_email_data   = array(
            'from_email'   => $cl['config']['email'],
            'from_name'    => $cl['config']['name'],
            'to_email'     => $test_email,
            'to_name'      => explode("@", $test_email)[0],
            'subject'      => "DO NOT REPLY. Checking SMTP server",
            'charSet'      => 'UTF-8',
            'is_html'      => false,
            'message_body' => "This is a test message that was sent to make sure that the SMTP server is working correctly. If you see this message, then your JOOJ Talk script is working and everything is OK"
        ); 

        if (cl_send_mail($send_email_data)) {
        	$data['status'] = 200;
        }
	}
}

else if ($action == 'get_banktrans_receipts') {

	require_once(cl_full_path("core/apps/cpanel/banktrans_receipts/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$requests       = cl_admin_get_banktrans_requests(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$requests = array_reverse($requests);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$requests       = cl_admin_get_banktrans_requests(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($requests)) {
		foreach ($requests as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/banktrans_receipts/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode("", $html_arr);
	}
}

else if ($action == 'get_banktrans_receipt_data') {

	require_once(cl_full_path("core/apps/cpanel/banktrans_receipts/app_ctrl.php"));

	$request_id       = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$cl['req_data']   = cl_admin_get_banktrans_request_data($request_id);

	if (not_empty($cl['req_data'])) {
		$data['status'] = 200;
		$data['html']   = cl_template('cpanel/assets/banktrans_receipts/modals/popup_ticket');
	}
}

else if ($action == 'delete_banktrans_receipt') {
	$request_id       = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$db               = $db->where('id', $request_id);
	$req_data         = $db->getOne(T_BANKTRANS_REQS);

	if (cl_queryset($req_data)) {
		$data['status'] = 200;
		$db             = $db->where('id', $request_id);
		$qr             = $db->delete(T_BANKTRANS_REQS);

		cl_delete_media($req_data['receipt_img']);

		cl_db_update(T_WALLET_HISTORY, array(
			"trans_id" => $req_data["trans_id"]
		), array(
			"status" => "declined"
		));
	}
	else {
		$data['status']  = 400;
		$data['message'] = "An error occurred while processing your request. Please try again later.";
	}
}

else if ($action == 'accept_banktrans_receipt') {
	$request_id       = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$db               = $db->where('id', $request_id);
	$req_data         = $db->getOne(T_BANKTRANS_REQS);

	if (cl_queryset($req_data)) {
		$data['status'] = 200;
		$db             = $db->where('id', $request_id);
		$qr             = $db->delete(T_BANKTRANS_REQS);

		cl_delete_media($req_data['receipt_img']);

		cl_db_update(T_WALLET_HISTORY, array(
			"trans_id" => $req_data["trans_id"]
		), array(
			"status" => "success"
		));

		$user_data = cl_raw_user_data($req_data["user_id"]);

		cl_update_user_data($req_data["user_id"], array(
			"wallet" => ($user_data["wallet"] += $req_data["amount"])
		));
	}
	else {
		$data['status']  = 400;
		$data['message'] = "An error occurred while processing your request. Please try again later.";
	}
}

else if ($action == 'save_customcode') {	
	$data['status']    = 400;
	$data['err_field'] = null;

	$custom_hjs = fetch_or_get($_POST["header_customjs"], "");
	$custom_fjs = fetch_or_get($_POST["footer_customjs"], "");
	$custom_hcss = fetch_or_get($_POST["header_customcss"], "");

	$ccf1 = cl_full_path(cl_strf("themes/%s/statics/custom_code/header.js", $cl["config"]["theme"]));
	$ccf2 = cl_full_path(cl_strf("themes/%s/statics/custom_code/header.css", $cl["config"]["theme"]));
	$ccf3 = cl_full_path(cl_strf("themes/%s/statics/custom_code/footer.js", $cl["config"]["theme"]));

	if (is_writable($ccf1) != true) {
		$data['err_code'] = "permission_denied";
		$data['err_field'] = "header_customjs";
		$data['message']  = "The costom header JS file ($ccf1) does not exists or not writable!";
	}
	
	elseif (is_writable($ccf2) != true) {
		$data['err_code'] = "permission_denied";
		$data['err_field'] = "header_customcss";
		$data['message']  = "The costom header CSS file ($ccf2) does not exists or not writable!";
	}

	elseif (is_writable($ccf3) != true) {
		$data['err_code'] = "permission_denied";
		$data['err_field'] = "footer_customjs";
		$data['message']  = "The costom footer JS file ($ccf3) does not exists or not writable!";
	}

	else{
		file_put_contents($ccf1, $custom_hjs);
		file_put_contents($ccf2, $custom_hcss);
		file_put_contents($ccf3, $custom_fjs);

		$data['status'] = 200;
	}
}
else if ($action == 'banned_words') {
    $data['status']    = 200;
    $words = $db->rawQuery("SELECT * FROM cl_banned_words;");

    $data['data'] = $words;  
}
else if ($action == 'get_all_categories') {	
    $data['status']    = 200;
    $categories = $db->rawQuery("SELECT * FROM cl_categories WHERE parent_id IS NULL  order by sort ASC ;");

    $flattenedData = [];
    
    foreach ($categories as $k => $cat) {
        $flattenedData[] = $cat; // Add main category
        $subcategories = $db->rawQuery("SELECT * FROM `cl_categories` WHERE parent_id = " . $cat['id'] . " order by sort ASC");
        foreach ($subcategories as $subcategory) {
            $flattenedData[] = $subcategory; // Add subcategory
        }
    }
    
    $data['data'] = $flattenedData;
    
}
else if ($action == 'delete_posts_data') {
    $id = $_GET['id'];
    $data['id'] = $id;
    $q = "delete from cl_publications where id = " . $id;
    $db->rawQuery($q);
    $data['id'] = $id;
}
// else if ($action == 'update_post') {
//     $data = $_POST;
// 	$pattern = '/(https?:\/\/[^\s]+)/';
// 	$urls = []; // Initialize an empty array to store URLs

// 	if (preg_match_all($pattern, $data['text'], $matches)) {
// 		// Check if any URLs were matched
// 		if (!empty($matches[0])) {
// 			// $matches[0] contains an array of matched URLs
// 			$urls = $matches[0];
// 		}
// 	}
// 	// Check if any URLs were found
// 	if (!empty($urls)) {

// 		$youtub_url = $urls[0]; // Get the first matched URL
// 		$update['type']= "text";

// 	}

// 	if(isset($data['text']) && !empty($data['text'])){

// 		$update['text']				=  $data['text'];

// 	}
// 	if(isset($data['category_id']) && !empty($data['category_id'])){

// 		$update['category_id']				=  $data['category_id'];

// 	}
	
	
// 	if(isset($data['id']) && !empty($data['id'])){

// 		$db->where ('id', $data['id']);
// 		$db->update ('cl_publications', $update);
// 	}
// 	if(empty($youtub_url)){

// 		if (isset($_FILES['image'])  && !empty($_FILES['image']['name']) ) {
// 			$image = $_FILES['image'];
// 			$fileType = explode('/', $image['type'])[0] ?? 'unknown';
// 			if ($fileType == 'image') {
				
// 				// Process image-related code
// 				$file_info = array(
// 					'file'      => $image['tmp_name'],
// 					'size'      => $image['size'],
// 					'name'      => $image['name'],
// 					'type'      => $image['type'],
// 					'file_type' => 'image',
// 					'folder'    => 'images',
// 					'slug'      => 'original',
// 					'allowed'   => 'jpg,png,jpeg,gif'
// 				);
// 				$file_upload = cl_upload($file_info);
				
// 				$jsonData = array(
// 					"image_thumb" => $file_upload['filename']
// 				);
// 			} else if ($fileType === 'video') {
// 				 $file_info      = array(
// 					'file'      => $image['tmp_name'],
// 					'size'      => $image['size'],
// 					'name'      => $image['name'],
// 					'type'      => $image['type'],
// 					'file_type' => 'video',
// 					'folder'    => 'videos',
// 					'slug'      => 'video_message',
// 					'allowed'   => 'mp4,mov,3gp,webm',
// 				);
		
// 				$file_upload = cl_upload($file_info);   
// 				$jsonData = array(
// 					"video_thumb" => $file_upload['filename']
// 				);
// 			}
	
// 			$q = "UPDATE `cl_publications` SET `type` = '"  . $fileType ."' WHERE `cl_publications`.`id` = ". $data['id'];
// 			$resp1 = $db->rawQuery($q);
		   
	
// 			$isExist = $db->rawQuery("SELECT * FROM `cl_pubmedia` where pub_id= ". $data['id']);
	
// 			if(isset($isExist) && !empty($isExist)){
	
// 				$q2 = "UPDATE `cl_pubmedia` SET 
// 					`type` = '"  . $fileType . "', 
// 					`src` = '"  . $file_upload['filename'] . "' ,
// 					`json_data` = '"  . json_encode($jsonData) . "' 
// 					WHERE `cl_pubmedia`.`pub_id` = ". $data['id'];
// 					// print_r($q2);die;
				
// 				// Execute the queries
// 				$resp2 = $db->rawQuery($q2);
// 			}else{
	
	
// 				$db->rawQuery("INSERT INTO `cl_pubmedia` (`pub_id`, `type`, `src`, `json_data`, `time`) VALUES ('".$data['id']."', '".$fileType."', '".$file_upload['filename']."', '".json_encode($jsonData) ."','".time()."')");
// 			}
// 			 // Update the cl_pubmedia table
			
			
// 		}
		
// 		if(isset($data['yt']) && !empty($data['yt'])) {
			
// 			 function getYoutubeVideoId($url) {
// 				$pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
// 				preg_match($pattern, $url, $matches);
			
// 				if (isset($matches[1])) {
// 					return $matches[1];
// 				} else {
// 					return 'notFound';
// 				}
// 			}
// 			// new code paramjeet singh
	
// 			$isExist = $db->rawQuery("SELECT * FROM `cl_pubmedia` where pub_id= ". $data['id']);
	
// 			if(isset($isExist) && !empty($isExist)){
	
// 				$q2 = "UPDATE `cl_pubmedia` SET 
// 				`src` = '"  . getYoutubeVideoId($data['yt']) . "' , `type`='yt' , `json_data`='[]'
// 				 WHERE `cl_pubmedia`.`pub_id` = ". $data['id'];
	
// 				 $resp1 = $db->rawQuery('UPDATE `cl_publications` SET `type` = "yt" WHERE `id`="'.$data['id'].'" ');
			 
// 			 // Execute the queries
// 			 $resp2 = $db->rawQuery($q2);
// 			}else{
	
// 				$db->rawQuery("INSERT INTO `cl_pubmedia` (`pub_id`, `type`, `src`, `time`) VALUES ('".$data['id']."', 'yt', '".getYoutubeVideoId($data['yt'])."','".time()."')");
// 				$resp1 = $db->rawQuery('UPDATE `cl_publications` SET `type` = "yt" WHERE `id`="'.$data['id'].'" ');
	
// 			}

// 			$q = "UPDATE `cl_publications` SET `type` = 'yt' WHERE `cl_publications`.`id` = ". $data['id'];
// 			$resp1 = $db->rawQuery($q);
				
// 		}
		
// 	}else{

//         $og_url    	= 	fetch_or_get($youtub_url, "");

//             try {
//                 require_once(cl_full_path("core/libs/htmlParser/simple_html_dom.php"));

//                 $og_data_object = file_get_html($og_url);

//                 if ($og_data_object) {
//                     $og_data_values = array(
//                         "title" => "",
//                         "description" => "",
//                         "image" => "",
//                         "type" => ""
//                     );

//                     foreach(array_keys($og_data_values) as $og_val) {
//                         if ($og_val == "title") {
//                             if ($og_data_object->find('title', 0)) {
//                                 $og_data_values["title"] = $og_data_object->find('title', 0)->plaintext;
//                             }

//                             else if ($og_data_object->find("meta[name='og:title']", 0)) {
//                                 $og_data_values["title"] = $og_data_object->find("meta[name='og:title']", 0)->content;
//                             }
//                         }

//                         else if($og_val == "description") {
//                             if ($og_data_object->find("meta[name='description']", 0)) {
//                                 $og_data_values["description"] = $og_data_object->find("meta[name='description']", 0)->content;
//                             }

//                             else if($og_data_object->find("meta[property='og:description']", 0)) {
//                                 $og_data_values["description"] = $og_data_object->find("meta[property='og:description']", 0)->content;
//                             }
//                         }

//                         else if($og_val == "image") {
//                             if($og_data_object->find("meta[name='image']", 0)) {
//                                 $og_data_values["image"] = $og_data_object->find("meta[name='image']", 0)->content;
//                             }

//                             else if($og_data_object->find("meta[property='og:image']", 0)) {
//                                 $og_data_values["image"] = $og_data_object->find("meta[property='og:image']", 0)->content;
//                             }
//                         }

//                         else if($og_val == "type") {
//                             if($og_data_object->find("meta[property='og:type']", 0)) {
//                                 $og_data_values["type"] = $og_data_object->find("meta[property='og:type']", 0)->content;
//                             }

//                             else if($og_data_object->find("meta[name='type']", 0)) {
//                                 $og_data_values["type"] = $og_data_object->find("meta[name='type']", 0)->content;
//                             }
//                         } 
//                     }

//                     $og_data_values = array(
//                         'title'       => cl_croptxt($og_data_values["title"], 160, '..'),
//                         'description' => cl_croptxt($og_data_values["description"], 300, '..'),
//                         'image'       => $og_data_values["image"],
//                         'type'        => $og_data_values["type"],
//                         'url'         => $og_url
//                     );

// 					if (not_empty($og_data_values['title'])) {
	
// 						$db->where ('id', $data['id']);
// 						$db->update ('cl_publications', ['og_data'=>json_encode($og_data_values)]);

// 					}
//                 }
//             } 
//             catch (Exception $e) {
//                 /*pass*/ 
//             }
        
// 		$resp1 = $db->rawQuery('DELETE FROM `cl_pubmedia` WHERE `pub_id`="'.$data['id'].'"');

// 	}

//     // $q = "UPDATE `cl_publications` SET `text` = '". $text . "', `category_id` = '"  . $category_id. "' WHERE `id` = '". $id."'";
	
//     // $resp1 = $db->rawQuery($e);

	
//       // Check if 'image' is present in $_FILES
   

//     if(isset($_POST['deleteVideo']) && !empty($_POST['deleteVideo'])) {
//         $resp1 = $db->rawQuery('DELETE FROM `cl_pubmedia` WHERE `pub_id`="'.$_POST['deleteVideo'].'"');
        
//         $resp1 = $db->rawQuery('UPDATE `cl_publications` SET `type` = "text" WHERE `id`="'.$_POST['deleteVideo'].'" ');
        
//     }
    
//     $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/talk/admin_panel/posts_pages'; // Set a default URL if the referer is not available
//     header("Location: $referer");
//     exit(); // Make sure to exit after sending the header to prevent further execution

// }
// new code 8 april
else if ($action == 'update_post') {
	// OLD CODE 7 MARCH 
    // $data = $_POST;
	// $pattern = '/(https?:\/\/[^\s]+)/';
	// $urls = []; // Initialize an empty array to store URLs

	// if(isset($data['yt']) && !empty($data['yt'])){

	// 	$data['text']		=	concatUrlWithText1($data['text'],$data['yt']);
	// 	$data['type']		=	'text';	

	// }else{
	// 	if (preg_match_all($pattern, $data['text'], $matches)) {
	// 		// Check if any URLs were matched
	// 		if (!empty($matches[0])) {
	// 			// $matches[0] contains an array of matched URLs
	// 			$urls = $matches[0];
	// 		}
	// 	}
	// 	// Check if any URLs were found
	// 	if (!empty($urls)) {
	
	// 		$youtub_url = $urls[0]; // Get the first matched URL
	// 		$update['type']= "text";
	// 	}
	// }
	// if(isset($data['text']) && !empty($data['text'])){
	// 	$update['text']				=  $data['text'];
	// }
	// if(isset($data['category_id']) && !empty($data['category_id'])){
	// 	$update['category_id']				=  $data['category_id'];
	// }
	// if(isset($data['id']) && !empty($data['id'])){

	// 	$db->where ('id', $data['id']);
	// 	$db->update ('cl_publications', $update);
	// }
	// if(empty($youtub_url)){

	// 	if (isset($_FILES['image'])  && !empty($_FILES['image']['name']) ) {
	// 		$image = $_FILES['image'];
	// 		$fileType = explode('/', $image['type'])[0] ?? 'unknown';
	// 		if ($fileType == 'image') {
				
	// 			// Process image-related code
	// 			$file_info = array(
	// 				'file'      => $image['tmp_name'],
	// 				'size'      => $image['size'],
	// 				'name'      => $image['name'],
	// 				'type'      => $image['type'],
	// 				'file_type' => 'image',
	// 				'folder'    => 'images',
	// 				'slug'      => 'original',
	// 				'allowed'   => 'jpg,png,jpeg,gif'
	// 			);
	// 			$file_upload = cl_upload($file_info);
				
	// 			$jsonData = array(
	// 				"image_thumb" => $file_upload['filename']
	// 			);
	// 		} else if ($fileType === 'video') {
	// 			 $file_info      = array(
	// 				'file'      => $image['tmp_name'],
	// 				'size'      => $image['size'],
	// 				'name'      => $image['name'],
	// 				'type'      => $image['type'],
	// 				'file_type' => 'video',
	// 				'folder'    => 'videos',
	// 				'slug'      => 'video_message',
	// 				'allowed'   => 'mp4,mov,3gp,webm',
	// 			);
		
	// 			$file_upload = cl_upload($file_info);   
	// 			$jsonData = array(
	// 				"video_thumb" => $file_upload['filename']
	// 			);
	// 		}
	
	// 		$q = "UPDATE `cl_publications` SET `type` = '"  . $fileType ."' WHERE `cl_publications`.`id` = ". $data['id'];
	// 		$resp1 = $db->rawQuery($q);
		   
	
	// 		$isExist = $db->rawQuery("SELECT * FROM `cl_pubmedia` where pub_id= ". $data['id']);
	
	// 		if(isset($isExist) && !empty($isExist)){
	
	// 			$q2 = "UPDATE `cl_pubmedia` SET 
	// 				`type` = '"  . $fileType . "', 
	// 				`src` = '"  . $file_upload['filename'] . "' ,
	// 				`json_data` = '"  . json_encode($jsonData) . "' 
	// 				WHERE `cl_pubmedia`.`pub_id` = ". $data['id'];
	// 				// print_r($q2);die;
				
	// 			// Execute the queries
	// 			$resp2 = $db->rawQuery($q2);
	// 		}else{
	
	
	// 			$db->rawQuery("INSERT INTO `cl_pubmedia` (`pub_id`, `type`, `src`, `json_data`, `time`) VALUES ('".$data['id']."', '".$fileType."', '".$file_upload['filename']."', '".json_encode($jsonData) ."','".time()."')");
	// 		}
	// 		 // Update the cl_pubmedia table
			
			
	// 	}
		
	// 	if(isset($data['yt']) && !empty($data['yt'])) {
			
	// 		function getYoutubeVideoId($url) {
	// 			$pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
	// 			preg_match($pattern, $url, $matches);
			
	// 			if (isset($matches[1])) {
	// 				return $matches[1];
	// 			} else {
	// 				return 'notFound';
	// 			}
	// 		}
	// 		// new code paramjeet singh
	
	// 		$isExist = $db->rawQuery("SELECT * FROM `cl_pubmedia` where pub_id= ". $data['id']);
	
	// 		if(isset($isExist) && !empty($isExist)){
	
	// 			$q2 = "UPDATE `cl_pubmedia` SET 
	// 			`src` = '"  . getYoutubeVideoId($data['yt']) . "' , `type`='yt' , `json_data`='[]'
	// 			 WHERE `cl_pubmedia`.`pub_id` = ". $data['id'];
	
	// 			 $resp1 = $db->rawQuery('UPDATE `cl_publications` SET `type` = "yt" WHERE `id`="'.$data['id'].'" ');
			 
	// 		 // Execute the queries
	// 		 $resp2 = $db->rawQuery($q2);
	// 		}else{
	
	// 			$db->rawQuery("INSERT INTO `cl_pubmedia` (`pub_id`, `type`, `src`, `time`) VALUES ('".$data['id']."', 'yt', '".getYoutubeVideoId($data['yt'])."','".time()."')");
	// 			$resp1 = $db->rawQuery('UPDATE `cl_publications` SET `type` = "yt" WHERE `id`="'.$data['id'].'" ');
	
	// 		}

	// 		$q = "UPDATE `cl_publications` SET `type` = 'yt' WHERE `cl_publications`.`id` = ". $data['id'];
	// 		$resp1 = $db->rawQuery($q);
				
	// 	}
		
	// }else{

    //     $og_url    	= 	fetch_or_get($youtub_url, "");

    //         try {
    //             require_once(cl_full_path("core/libs/htmlParser/simple_html_dom.php"));

    //             $og_data_object = file_get_html($og_url);

    //             if ($og_data_object) {
    //                 $og_data_values = array(
    //                     "title" => "",
    //                     "description" => "",
    //                     "image" => "",
    //                     "type" => ""
    //                 );

    //                 foreach(array_keys($og_data_values) as $og_val) {
    //                     if ($og_val == "title") {
    //                         if ($og_data_object->find('title', 0)) {
    //                             $og_data_values["title"] = $og_data_object->find('title', 0)->plaintext;
    //                         }

    //                         else if ($og_data_object->find("meta[name='og:title']", 0)) {
    //                             $og_data_values["title"] = $og_data_object->find("meta[name='og:title']", 0)->content;
    //                         }
    //                     }

    //                     else if($og_val == "description") {
    //                         if ($og_data_object->find("meta[name='description']", 0)) {
    //                             $og_data_values["description"] = $og_data_object->find("meta[name='description']", 0)->content;
    //                         }

    //                         else if($og_data_object->find("meta[property='og:description']", 0)) {
    //                             $og_data_values["description"] = $og_data_object->find("meta[property='og:description']", 0)->content;
    //                         }
    //                     }

    //                     else if($og_val == "image") {
    //                         if($og_data_object->find("meta[name='image']", 0)) {
    //                             $og_data_values["image"] = $og_data_object->find("meta[name='image']", 0)->content;
    //                         }

    //                         else if($og_data_object->find("meta[property='og:image']", 0)) {
    //                             $og_data_values["image"] = $og_data_object->find("meta[property='og:image']", 0)->content;
    //                         }
    //                     }

    //                     else if($og_val == "type") {
    //                         if($og_data_object->find("meta[property='og:type']", 0)) {
    //                             $og_data_values["type"] = $og_data_object->find("meta[property='og:type']", 0)->content;
    //                         }

    //                         else if($og_data_object->find("meta[name='type']", 0)) {
    //                             $og_data_values["type"] = $og_data_object->find("meta[name='type']", 0)->content;
    //                         }
    //                     } 
    //                 }

    //                 $og_data_values = array(
    //                     'title'       => cl_croptxt($og_data_values["title"], 160, '..'),
    //                     'description' => cl_croptxt($og_data_values["description"], 300, '..'),
    //                     'image'       => $og_data_values["image"],
    //                     'type'        => $og_data_values["type"],
    //                     'url'         => $og_url
    //                 );

	// 				if (not_empty($og_data_values['title'])) {
	
	// 					$db->where ('id', $data['id']);
	// 					$db->update ('cl_publications', ['og_data'=>json_encode($og_data_values)]);

	// 				}
    //             }
    //         } 
    //         catch (Exception $e) {
    //             /*pass*/ 
    //         }
        
	// 	$resp1 = $db->rawQuery('DELETE FROM `cl_pubmedia` WHERE `pub_id`="'.$data['id'].'"');

	// }

	// OLD CODE 7 MARCH
	// NEW CODE 7 MARCH
	$data = $_POST;

	// Get Post's Old Data
	if(isset($data['id']) && !empty($data['id'])){
		$db->where ('id', $data['id']);
		$oldData = $db->get(T_PUBS);
	}

	$pattern = '/(https?:\/\/[^\s]+)/';
	$urls = [];                         // Initialize an empty array to store URLs
	$text="";
	$type="";
	$youtub_url="";
	$link_src="";
	if(isset($data['yt']) && !empty($data['yt'])){


		$text				=	removeUrls($data['text'],$data['yt']);
		$type				=	'text';
        $youtub_url         =   $data['yt']; // Get the first matched URL
        $link_src   =   getYoutubeVideoIdS($data['yt']);

	}else{

        // if text contain link
		if (preg_match_all($pattern, $data['text'], $matches)) {
			// Check if any URLs were matched
			if (!empty($matches[0])) {
				// $matches[0] contains an array of matched URLs
				$urls = $matches[0];
			}
		}
		// Check if any URLs were found
		if (!empty($urls)) {
	
			$youtub_url         =   $urls[0]; // Get the first matched URL
			$type     			=   "text";
            $link_src   		=   getYoutubeVideoIdS($youtub_url);

		} else if ($oldData[0]['type'] == 'image') {
			$type = $oldData[0]['type'];
		} else{
			$type     			=   "text";
		}
	}

	if(isset($text) && !empty($text)){

		$update['text']				=  $text;

	}else{

		$update['text']				=  $data['text'];
	}

	if(isset($type) && !empty($type)){
		$update['type']	=  $type;
		
	}else{

		$update['type']	=  $data['type'];
	}

	if(isset($link_src) && !empty($link_src)){
		$update['link_src']				=  $link_src;
		
	}else{

		$update['link_src']				=  "";
	}

	if(isset($data['category_id']) && !empty($data['category_id'])){
		$update['category_id']				=  $data['category_id'];
	}

	if(isset($data['id']) && !empty($data['id'])){

		$db->where('id', $data['id']);
		$db->update(T_PUBS, $update);
	}

	if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
		$image = $_FILES['image'];
		$fileType = explode('/', $image['type'])[0] ?? 'unknown';
		if ($fileType == 'image') {
			
			// Process image-related code
			$file_info = array(
				'file'      => $image['tmp_name'],
				'size'      => $image['size'],
				'name'      => $image['name'],
				'type'      => $image['type'],
				'file_type' => 'image',
				'folder'    => 'images',
				'slug'      => 'original',
				'allowed'   => 'jpg,png,jpeg,gif'
			);
			$file_upload = cl_upload($file_info);
			
			$jsonData = array(
				"image_thumb" => $file_upload['filename']
			);
		} else if ($fileType === 'video') {
				$file_info      = array(
				'file'      => $image['tmp_name'],
				'size'      => $image['size'],
				'name'      => $image['name'],
				'type'      => $image['type'],
				'file_type' => 'video',
				'folder'    => 'videos',
				'slug'      => 'video_message',
				'allowed'   => 'mp4,mov,3gp,webm',
			);

			$file_upload = cl_upload($file_info);   
			$jsonData = array(
				"video_thumb" => $file_upload['filename']
			);
		}

		$q = "UPDATE `cl_publications` SET `type` = '"  . $fileType ."',link_src='',`og_data`='',`text` ='".filterText($data['text'])."'  WHERE `cl_publications`.`id` = '". $data['id']."'";
		$resp1 = $db->rawQuery($q);
		$isExist = $db->rawQuery("SELECT * FROM `cl_pubmedia` where pub_id= ". $data['id']);

		if(isset($isExist) && !empty($isExist)){
			$q2 = "UPDATE `cl_pubmedia` SET 
				`type` = '"  . $fileType . "', 
				`src` = '"  . $file_upload['filename'] . "' ,
				`json_data` = '"  . json_encode($jsonData) . "' 
				WHERE `cl_pubmedia`.`pub_id` = ". $data['id'];
			// Execute the querie
			$resp2 = $db->rawQuery($q2);
		}else{

			$db->rawQuery("INSERT INTO `cl_pubmedia` (`pub_id`, `type`, `src`, `json_data`, `time`) VALUES ('".$data['id']."', '".$fileType."', '".$file_upload['filename']."', '".json_encode($jsonData) ."','".time()."')");
		}
			// Update the cl_pubmedia table
	}  else {
		if(isset($youtub_url) && !empty($youtub_url)){
			$og_url    	= 	fetch_or_get($youtub_url, "");
				try {
					require_once(cl_full_path("core/libs/htmlParser/simple_html_dom.php"));
					$og_data_object = file_get_html($og_url);
					if ($og_data_object) {
						$og_data_values = array(
							"title" => "",
							"description" => "",
							"image" => "",
							"type" => ""
						);

						foreach(array_keys($og_data_values) as $og_val) {
							if ($og_val == "title") {
								if ($og_data_object->find('title', 0)) {
									$og_data_values["title"] = $og_data_object->find('title', 0)->plaintext;
								}

								else if ($og_data_object->find("meta[name='og:title']", 0)) {
									$og_data_values["title"] = $og_data_object->find("meta[name='og:title']", 0)->content;
								}
							}

							else if($og_val == "description") {
								if ($og_data_object->find("meta[name='description']", 0)) {
									$og_data_values["description"] = $og_data_object->find("meta[name='description']", 0)->content;
								}

								else if($og_data_object->find("meta[property='og:description']", 0)) {
									$og_data_values["description"] = $og_data_object->find("meta[property='og:description']", 0)->content;
								}
							}

							else if($og_val == "image") {
								if($og_data_object->find("meta[name='image']", 0)) {
									$og_data_values["image"] = $og_data_object->find("meta[name='image']", 0)->content;
								}

								else if($og_data_object->find("meta[property='og:image']", 0)) {
									$og_data_values["image"] = $og_data_object->find("meta[property='og:image']", 0)->content;
								}
							}

							else if($og_val == "type") {
								if($og_data_object->find("meta[property='og:type']", 0)) {
									$og_data_values["type"] = $og_data_object->find("meta[property='og:type']", 0)->content;
								}

								else if($og_data_object->find("meta[name='type']", 0)) {
									$og_data_values["type"] = $og_data_object->find("meta[name='type']", 0)->content;
								}
							} 
						}

						$og_data_values = array(
							'title'       => cl_croptxt($og_data_values["title"], 160, '..'),
							'description' => cl_croptxt($og_data_values["description"], 300, '..'),
							'image'       => $og_data_values["image"],
							'type'        => $og_data_values["type"],
							'url'         => $og_url
						);

						if (not_empty($og_data_values['title'])) {
		
							$db->where ('id', $data['id']);
							$db->update ('cl_publications', ['og_data'=>json_encode($og_data_values)]);

						}
					}
				} 
				catch (Exception $e) {
					/*pass*/ 
				}
			
			$resp1 = $db->rawQuery('DELETE FROM `cl_pubmedia` WHERE `pub_id`="'.$data['id'].'"');
		}
	}
		#--------- commented code ---------------- 7APRIL#
		// if(isset($data['yt']) && !empty($data['yt'])) {
		//     function getYoutubeVideoId($url) {
		// 		$pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
		// 		preg_match($pattern, $url, $matches);
		// 		if (isset($matches[1])) {
		// 			return $matches[1];
		// 		} else {
		// 			return 'notFound';
		// 		}
		// 	}
		// 	$isExist = $db->rawQuery("SELECT * FROM `cl_pubmedia` where pub_id= ". $data['id']);
		// 	if(isset($isExist) && !empty($isExist)){
		// 		$q2 = "UPDATE `cl_pubmedia` SET 
		// 		`src` = '"  . getYoutubeVideoId($data['yt']) . "' , `type`='yt' , `json_data`='[]'
		// 		 WHERE `cl_pubmedia`.`pub_id` = ". $data['id'];
		// 		$resp1 = $db->rawQuery('UPDATE `cl_publications` SET `type` = "yt" WHERE `id`="'.$data['id'].'" ');
		// 	    $resp2 = $db->rawQuery($q2);
		// 	}else{
		// 		$db->rawQuery("INSERT INTO `cl_pubmedia` (`pub_id`, `type`, `src`, `time`) VALUES ('".$data['id']."', 'yt', '".getYoutubeVideoId($data['yt'])."','".time()."')");
		// 		$resp1 = $db->rawQuery('UPDATE `cl_publications` SET `type` = "yt" WHERE `id`="'.$data['id'].'" ');
		// 	}
		// 	$q = "UPDATE `cl_publications` SET `type` = 'yt' WHERE `cl_publications`.`id` = ". $data['id'];
		// 	$resp1 = $db->rawQuery($q);	
		// }
		#--------- commented code ---------------- 7APRIL#

	
	// NEW CODE 7 MARCH

   

    if(isset($_POST['deleteVideo']) && !empty($_POST['deleteVideo'])) {
        
        #-------- april 20 ----------#
        $db->where ("id", $data['id']);
		$user = $db->getOne ("cl_publications");
		$isExist = $db->rawQuery("SELECT * FROM `cl_publications` where id= ". $data['id']);
		if(isset($user) && !empty($user)){
			if(!empty($user['link_src'])){
				$text	=	filterText($_POST['text']);
        		$db->rawQuery('UPDATE `cl_publications` SET `text`="'.$text.'" ,`link_src` = "", `og_data`="" WHERE `id`="'.$_POST['deleteVideo'].'" ');
			}
		}
		#--------------------------
        $resp1 = $db->rawQuery('DELETE FROM `cl_pubmedia` WHERE `pub_id`="'.$_POST['deleteVideo'].'"');
        
        $resp1 = $db->rawQuery('UPDATE `cl_publications` SET `type` = "text" WHERE `id`="'.$_POST['deleteVideo'].'" ');
        
    }
    
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/talk/admin_panel/posts_pages'; // Set a default URL if the referer is not available
    header("Location: $referer");
    exit(); // Make sure to exit after sending the header to prevent further execution

}
else if ($action == 'get_categories') {	
    $data['status']    = 200;
    $categories = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id IS NULL");
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $data['data']    = $categories;
}
else if ($action == 'update_categories') {	
    $data['status']    = 200;
    
    // Read the raw POST data
    $requestPayload = file_get_contents("php://input");
    
    // Convert the JSON payload to a PHP associative array
    $data = json_decode($requestPayload, true);

    // echo var_dump($data['name']);die;
    
    
     $db->rawQuery("update `cl_categories` set name = '"  . $data['name'] . "' where id = " . $data['id']);
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $categories = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id IS NULL");
    $data['data']    = $categories;
}
else if ($action == 'update_banned_words') {	
    $data['status']    = 200;
    
    // Read the raw POST data
    $requestPayload = file_get_contents("php://input");
    
    // Convert the JSON payload to a PHP associative array
    $data = json_decode($requestPayload, true);

    // echo var_dump($data['name']);die;
    
    
     $db->rawQuery("update `cl_banned_words` set keyword = '"  . $data['keyword'] . "' where id = " . $data['id']);
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $categories = $db->rawQuery("SELECT * FROM `cl_banned_words`");
    $data['data']    = $categories;
}
else if ($action == 'update_sub_categories') {	
    $data['status']    = 200;
    
    // Read the raw POST data
    $requestPayload = file_get_contents("php://input");
    
    // Convert the JSON payload to a PHP associative array
    $data = json_decode($requestPayload, true);

    // echo var_dump($data['name']);die;
    
    
     $db->rawQuery("update `cl_categories` set name = '"  . $data['name'] . "' where id = " . $data['id']);
     $db->rawQuery("UPDATE cl_categories SET name = '"  . $data['name'] . "', parent_id = '"  . $data['parent_id'] . "' WHERE id = ". $data['id']);
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $categories = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id IS NULL");
    $data['data']    = $categories;
}
else if ($action == 'delete_categories') {	
    $data['status']    = 200;
    
    // Read the raw POST data
    $requestPayload = file_get_contents("php://input");
    
    // Convert the JSON payload to a PHP associative array
    $data = json_decode($requestPayload, true);

    // echo var_dump($data['name']);die;
    
    
     $db->rawQuery("DELETE FROM `cl_categories` WHERE id = " . $data['id']);
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $data['data']    = [];
}
else if ($action == 'delete_banned_words') {	
    $data['status']    = 200;
    
    // Read the raw POST data
    $requestPayload = file_get_contents("php://input");
    
    // Convert the JSON payload to a PHP associative array
    $data = json_decode($requestPayload, true);

    // echo var_dump($data['name']);die;
    
    
     $db->rawQuery("DELETE FROM `cl_banned_words` WHERE id = " . $data['id']);
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $data['data']    = [];
}
else if ($action == 'add_categories') {	
    $data['status']    = 200;
    
    // Read the raw POST data
    $requestPayload = file_get_contents("php://input");
    
    // Convert the JSON payload to a PHP associative array
    $data = json_decode($requestPayload, true);

    // echo var_dump($data['name']);die;
    
    
     $db->rawQuery("INSERT INTO `cl_categories` (`id`, `name`, `enabled`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, '".$data['name']."', '1', NULL, NULL, NULL);");
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $data['data']    = [];
}
else if ($action == 'add_banned_words') {	
    $data['status']    = 200;
    
    // Read the raw POST data
    $requestPayload = file_get_contents("php://input");
    
    // Convert the JSON payload to a PHP associative array
    $data = json_decode($requestPayload, true);

    // echo var_dump($data['name']);die;
    
    
     $db->rawQuery("INSERT INTO `cl_banned_words` (`id`, `keyword`) VALUES (NULL, '".$data['name']."');");
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $data['data']    = [];
}
else if ($action == 'add_sub_categories') {	
    $data['status']    = 200;
    
    // Read the raw POST data
    $requestPayload = file_get_contents("php://input");
    
    // Convert the JSON payload to a PHP associative array
    $data = json_decode($requestPayload, true);

    // echo var_dump($data['name']);die;
    
    
     $db->rawQuery("INSERT INTO `cl_categories` (`id`, `name`, `enabled`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, '".$data['name']."', '1', '".$data['parent_id']."', NULL, NULL);");
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $data['data']    = [];
}
else if ($action == 'get_sub_categories') {	
    $data['status']    = 200;
    $categories = $db->rawQuery("SELECT a.*, (SELECT name FROM cl_categories b WHERE b.id = a.parent_id) as parent_name FROM cl_categories as a WHERE parent_id IS NOT NULL;");
    // foreach($categories as $k => $cat){
    //     $categories[$k]['childs'] = $db->rawQuery("SELECT * FROM `cl_categories` where parent_id = " . $cat['id']);
    // }
    
    $data['data']    = $categories;
}
else if ($action == 'get_posts_comments_admin') {
    $data['status'] = 200;

    // Set the page and limit values (you can adjust these dynamically via user input)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default to page 1 if not provided
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default to 10 posts per page if not provided
    $offset = ($page - 1) * $limit;
    $postId = $_GET['post_id'];

    // Modify the query to add LIMIT and OFFSET for pagination
    $posts = $db->rawQuery("
        SELECT cl_publications.*, cl_pubmedia.*,cl_users.username,cl_users.avatar
        FROM cl_publications
        LEFT JOIN cl_pubmedia ON cl_publications.id = cl_pubmedia.pub_id
        INNER JOIN cl_users ON cl_publications.user_id = cl_users.id
        WHERE cl_publications.thread_id = $postId
        ORDER BY cl_publications.id DESC;
    ");
    
    $data['data'] = $posts;

    $data['pagination'] = [
        'current_page' => 1,
        'total_pages' => 1,
        'total_posts' => 100,
        'limit' => 0,
    ];
}
else if ($action == 'get_all_posts_admin') {
    $data['status'] = 200;

    // Set the page and limit values (you can adjust these dynamically via user input)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default to page 1 if not provided
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default to 10 posts per page if not provided
    $offset = ($page - 1) * $limit;

    // Modify the query to add LIMIT and OFFSET for pagination
    $words = $db->rawQuery("
        SELECT cl_publications.*, cl_pubmedia.src, cl_categories.name as category_name, cl_users.username as username 
        FROM cl_publications 
        LEFT JOIN cl_categories ON cl_publications.category_id = cl_categories.id
        INNER JOIN cl_users ON cl_publications.user_id = cl_users.id
        LEFT JOIN cl_pubmedia ON cl_publications.id = cl_pubmedia.pub_id
        WHERE text IS NOT NULL 
        ORDER BY id DESC 
        LIMIT $limit OFFSET $offset;
    ");
    
    $posts = [];
    foreach ($words as $word) {
        if ($word['og_data'] != '') {
            $og_data_array = json_decode($word['og_data']);
            if (array_key_exists('og_data', $word)) {
                $word['og_data'] = $og_data_array;
            }
        }
        $posts[] = $word;
    }
    
    $data['data'] = $posts;

    // Optional: Add pagination info (total count, pages)
    $totalPosts = $db->rawQueryOne("SELECT COUNT(*) as count FROM cl_publications WHERE text IS NOT NULL");
    $totalPages = ceil($totalPosts['count'] / $limit);
    $data['pagination'] = [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total_posts' => $totalPosts['count'],
        'limit' => $limit,
    ];
}
else if ($action == 'search_all_posts_admin') {
    $data['status']    = 200;
    $username = $_GET['username'];
    $text = $_GET['text'];
    
    $query = "
select cl_publications.*, cl_pubmedia.src, cl_categories.name as category_name, cl_users.username as username from cl_publications 
inner join cl_categories on cl_publications.category_id = cl_categories.id
inner join cl_users on cl_publications.user_id = cl_users.id
left join cl_pubmedia on cl_publications.id = cl_pubmedia.pub_id
where text is not null";
    
    if(!empty($username) && !empty($text)){
        $query .= " and text like '%".$text."%' OR username like '%".$username."%'";
    } else if(!empty($username)){
        $query .= " and username like '%".$username."%'";
    } else if(!empty($text)){
        $query .= " and text like '%".$text."%'";
    }
    $query .= " order by id DESC";
    
    
    $words = $db->rawQuery($query);

    $data['data'] = $words;  
}

// IP RESTRICTION CODE
else if ($action == 'add_restricted_ip') {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$ipFormData = $_POST['ipFormData'];
		$pairs = explode('&', $ipFormData);
		$ipFormData = [];
		foreach ($pairs as $pair) {
			list($key, $value) = explode('=', $pair);
			$ipFormData[urldecode($key)] = urldecode($value);
		}

		$ip_address = $ipFormData['ip_address'] ?? '';
		if ($ipFormData['ip_address'] != '') {
			$query = "INSERT INTO " . T_RESTRICTED_IPS . " (ip_address, time) VALUES ('$ip_address', '" . time() . "')";
			$ipAddress = $db->query($query);
			$data['status'] = 200;
			$data['message'] = cl_translate('IP address saved!');
		} else {
			$data['status'] = 400;
			$data['message'] = cl_translate('IP address in required');
		}
	}
}

else if ($action == 'delete_restricted_ip') {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$restricted_ip_id = $_POST['id'];

		$db = $db->where('id', $restricted_ip_id);
		$qr = $db->delete(T_RESTRICTED_IPS);

		if ($qr) {
			$data['status'] = 200;
			$data['message'] = cl_translate('IP address is deleted!');
		} else {
			$data['status'] = 400;
			$data['message'] = cl_translate('IP address is not deleted!');
		}
	}
}

else if ($action == 'edit_restricted_ip') {
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		$restricted_ip_id = $_GET['id'];

		$db = $db->where('id', $restricted_ip_id);
    	$restricted_ip_data = $db->get(T_RESTRICTED_IPS);

		foreach ($restricted_ip_data as $restricted_ip) {
			$restricted_ip = $restricted_ip;
		}
		
		if (!empty($restricted_ip_data)) {
			$data['status'] = 200;
			$data['data'] = $restricted_ip;
		} else {
			$data['status'] = 400;
			$data['message'] = cl_translate('IP address data not found!');
		}
	}
}

else if ($action == 'update_restricted_ip') {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$ipEditFormData = $_POST['ipEditFormData'];
		$pairs = explode('&', $ipEditFormData);
		$ipEditFormData = [];
		foreach ($pairs as $pair) {
			list($key, $value) = explode('=', $pair);
			$ipEditFormData[urldecode($key)] = urldecode($value);
		}

		$ip_address = $ipEditFormData['ip_address'] ?? '';
		$ip_address_id = $ipEditFormData['ip_address_id'] ?? '';
		if ($ipEditFormData['ip_address'] != '') {
			$db = $db->where('id', $ip_address_id);
			$up = $db->update(T_RESTRICTED_IPS, array(
				'ip_address' => $ip_address
			));
			$data['status'] = 200;
			$data['message'] = cl_translate('IP address edit!');
		} else {
			$data['status'] = 400;
			$data['message'] = cl_translate('IP address in required');
		}
	}
}

else if ($action == 'search_restricted_ip') {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$query = "SELECT * FROM " . T_RESTRICTED_IPS . " WHERE ip_address LIKE '%" . $_POST['searchIp'] . "%'";
		$cl['restricted_ips'] = $db->query($query);
		$html = '<tbody data-an="ads-list" id="restrictedIPsData">';
			if (not_empty($cl['restricted_ips'])) {
				foreach ($cl['restricted_ips'] as $cl['li']) {
					$html .= '<tr>
								<td scope="row">' . $cl['li']['id'] . '</td>
								<td>' . $cl['li']['ip_address'] . '</td>
								<td><time>' . $cl['li']['time'] . '</time></td>
								<td><div class="dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="material-icons">more_horiz</i></a><div class="dropdown-menu dropdown-menu-right"><a href="javascript:void(0);" class="dropdown-item" onclick="editRestrictiedIp(' . $cl['li']['id'] . ');" ' .cl_translate('Edit') . '</a><a href="javascript:void(0);" class="dropdown-item" onclick="deleteRestrictiedIp(' . $cl['li']['id'] .');">' . cl_translate('Delete') . '</a></ul></div></td>
							</tr>';
				}
			} else {
				$html .= '<tr>
							<td colspan="8"><div class="empty-table"><span class="icon"><i class="material-icons">library_books</i></span><h4>No restricted ip found</h4><p>It looks like there are no restricted ip in your database yet. All restricted ip of your website will be displayed here.</p></div></td>
						</tr>';
			}
		$html .= '</tbody>';
		
		if ($cl['restricted_ips'] != '') {
			$data['status'] = 200;
			$data['html'] = $html;
		} else {
			$data['status'] = 400;
		}
	}
}