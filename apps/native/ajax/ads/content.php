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

if ($action == 'upload_ad_cover' && not_empty($cl['is_logged'])) {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $ad_id            = fetch_or_get($_POST['ad_id'], false);
    $ad_data          = cl_raw_ad_data($ad_id);

    if (not_empty($ad_data) && $ad_data['user_id'] == $me['id']) {
		if (not_empty($_FILES['cover']) && not_empty($_FILES['cover']['tmp_name'])) {
	        $file_info      = array(
	            'file'      => $_FILES['cover']['tmp_name'],
	            'size'      => $_FILES['cover']['size'],
	            'name'      => $_FILES['cover']['name'],
	            'type'      => $_FILES['cover']['type'],
	            'file_type' => 'image',
	            'folder'    => 'covers',
	            'slug'      => 'cover',
	            'allowed'   => 'jpg,png,jpeg,gif'
	        );

	        $file_upload = cl_upload($file_info);

	        if (not_empty($file_upload['filename'])) {
	            $data['status'] = 200;
	            $data['url']    = cl_get_media($file_upload['filename']);

	            cl_delete_media($ad_data['cover']);

	            cl_update_ad_data($ad_id, array(
	                'cover' => $file_upload['filename']
	            ));

                if ($ad_data['status'] == 'active') {
                    cl_update_ad_data($ad_id, array(
                        'status' => 'inactive'
                    ));
                }
	        } 
	    }
    }
}

else if($action == 'save_ad_data' && not_empty($cl['is_logged'])) {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $ad_id            = fetch_or_get($_POST['ad_id'], false);
    $ad_data          = cl_raw_ad_data($ad_id);

    if (not_empty($ad_data) && $ad_data['user_id'] == $me['id']) {
    	$ad_data_changes  = array(
    		'cover'       => fetch_or_get($ad_data['cover'], false),
    		'company'     => fetch_or_get($_POST['company'], false),
    		'target_url'  => fetch_or_get($_POST['target_url'], false),
    		'status'      => fetch_or_get($_POST['status'], false),
    		'audience'    => fetch_or_get($_POST['audience'], false),
    		'description' => fetch_or_get($_POST['description'], false),
    		'cta'         => fetch_or_get($_POST['cta'], false)
    	);

    	foreach ($ad_data_changes as $field_name => $field_val) {
    		if ($field_name == 'cover') {
                if (empty($field_val)) {
                    $data['err_code'] = "invalid_cover"; break;
                }
            }

            else if ($field_name == 'company') {
                if (empty($field_val) || len_between($field_val, 1, 115) != true) {
                    $data['err_code'] = "invalid_company"; break;
                }
            }

            else if ($field_name == 'target_url') {
                if (empty($field_val) || is_url($field_val) != true) {
                    $data['err_code'] = "invalid_target_url"; break;
                }
            }

            else if ($field_name == 'status') {
                if (empty($field_val) || in_array($field_val, array('active', 'inactive')) != true) {
                    $data['err_code'] = "invalid_status"; break;
                }
            }

            else if ($field_name == 'audience') {
                if (empty($field_val) || are_all($field_val, 'numeric') != true) {
                    $data['err_code'] = "invalid_audience"; break;
                }
            }

            else if ($field_name == 'description') {
                if (empty($field_val) || len_between($field_val, 1, 550) != true) {
                    $data['err_code'] = "invalid_description"; break;
                }
            }

            else if ($field_name == 'cta') {
                if (empty($field_val) || len_between($field_val, 1, 32) != true) {
                    $data['err_code'] = "invalid_cta"; break;
                }
            }
    	}

    	if (empty($data['err_code'])) {
    		$data['status']   = 200;
    		$ad_update_data   = array(
    			'company'     => cl_text_secure($ad_data_changes['company']),
    			'target_url'  => cl_text_secure($ad_data_changes['target_url']),
    			'status'      => cl_text_secure($ad_data_changes['status']),
    			'audience'    => ((empty($ad_data_changes['audience'])) ? json(array(), true) : json($ad_data_changes['audience'], true)),
    			'description' => cl_text_secure($ad_data_changes['description']),
    			'cta'         => cl_text_secure($ad_data_changes['cta'])
    		);

    		cl_update_ad_data($ad_id, $ad_update_data);

    		if (not_empty($me['last_ad'])) {
    			cl_update_user_data($me['id'], array(
    				'last_ad' => 0
    			));
    		}

            if ($ad_data_changes["status"] == "active") {
                cl_update_ad_data($ad_id, array(
                    'approved' => 'N'
                ));
            }

            else {
                cl_update_ad_data($ad_id, array(
                    'approved' => 'Y'
                ));
            }
    	}
    }
}

else if($action == 'ad_conversion') {
    if ($cl['config']['advertising_system'] == 'on') {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $ad_id            = fetch_or_get($_POST['id'], false);
        $ad_data          = cl_raw_ad_data($ad_id);

        if (not_empty($ad_data)) {
            $ad_owner  = cl_raw_user_data($ad_data['user_id']);
            $conv_rate = $cl['config']['ad_conversion_rate'];
            $clicks    = cl_session('ad_clicks');

            if (not_empty($ad_owner)) {
                $data['status'] = 200;

                if (is_array($clicks) != true) {
                    $clicks = array();
                }

                cl_update_ad_data($ad_id, array(
                    'clicks' => ($ad_data['clicks'] += 1),
                    'budget' => ($ad_data['budget'] += $conv_rate)
                ));

                cl_update_user_data($ad_owner['id'], array(
                    'wallet' => ($ad_owner['wallet'] -= $conv_rate)
                ));

                if (in_array($ad_id, $clicks) != true) {
                    array_push($clicks, $ad_id);
                }

                cl_session('ad_clicks', $clicks);
            }
        }
    }
}

else if($action == 'delete_ad' && not_empty($cl['is_logged'])) {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $ad_id            = fetch_or_get($_POST['id'], false);
    $ad_data          = cl_raw_ad_data($ad_id);

    if (not_empty($ad_data) && $ad_data['user_id'] == $me['id']) {
        cl_delete_media($ad_data['cover']);

        $db             = $db->where('id', $ad_id);
        $qr             = $db->delete(T_ADS);
        $data['status'] = 200;
    }
}