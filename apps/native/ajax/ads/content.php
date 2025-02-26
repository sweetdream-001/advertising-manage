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
    		'yt_url'      => fetch_or_get($_POST['yt'], null), // Nullable YouTube URL
    		'status'      => fetch_or_get($_POST['status'], false),
    		'audience'    => fetch_or_get($_POST['audience'], false),
    		'description' => fetch_or_get($_POST['description'], false),
    		'cta'         => fetch_or_get($_POST['cta'], false)
    	);
        
        // At least one of cover or yt_url must be provided
    	if (!empty($ad_data_changes['cover']) || !empty($ad_data_changes['yt_url'])) {
            foreach ($ad_data_changes as $field_name => $field_val) {
                if ($field_name == 'company') {
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
        } else {
            $data['err_code'] = "Please upload a cover or add a YouTube video URL.";
        }
        
    	if (empty($data['err_code'])) {
            $data['status']   = 200;
    		$ad_update_data   = array(
                'company'     => cl_text_secure($ad_data_changes['company']),
    			'target_url'  => cl_text_secure($ad_data_changes['target_url']),
    			'og_data'     => (not_empty($ad_data_changes['yt_url'])) ? json_encode(youTubeToText($ad_data_changes['yt_url'])) : null,
    			'link_src'    => (not_empty($ad_data_changes['yt_url'])) ? getYoutubeVideoIdS($ad_data_changes['yt_url']) : null,
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

else if ($action == 'ad_like_count_update' && not_empty($cl['is_logged'])) {
    global $db, $cl;

    $user_id = (not_empty($cl["is_logged"])) ? $me["id"] : 0;
    $db = $db->where('user_id', $user_id);
    $has_liked = $db->getValue(T_AD_LIKES, 'COUNT(*)');
    
    if (not_empty($_POST['ad_id'])) {
        $adId = $_POST['ad_id'];
        if ($has_liked) {
            $db = $db->where('id', $adId);
            $likes_count = $db->getValue(T_ADS, 'likes_count');

            $db = $db->where('id', $adId);
            $up = $db->update(T_ADS, array(
                'likes_count' => $likes_count - 1
            ));

            if ($up) {
                $db = $db->where('id', $adId);
                $likes_count = $db->getValue(T_ADS, 'likes_count');
    
                $db = $db->where('user_id', $user_id);
                $adLike = $db->delete(T_AD_LIKES);
    
                $data = [
                    'errors' => 0,
                    'status' => 200,
                    'likes_count' => $likes_count
                ];
    
                return json_encode($data);
            }
        } else {
            $db = $db->where('id', $adId);
            $likes_count = $db->getValue(T_ADS, 'likes_count');
            
            $db = $db->where('id', $adId);
            $up = $db->update(T_ADS, array(
                'likes_count' => $likes_count + 1
            ));
    
            if ($up) {
                $db = $db->where('id', $adId);
                $likes_count = $db->getValue(T_ADS, 'likes_count');
    
                $likeByData = [
                    'ad_id' => $adId,
                    'user_id' => $user_id,
                    'time' => time()
                ];
                $adLike = $db->insert(T_AD_LIKES, $likeByData);
    
                $data = [
                    'errors' => 0,
                    'status' => 200,
                    'likes_count' => $likes_count
                ];
    
                return json_encode($data);
            }
        }
    }
}

elseif ($action == 'ad_reshare_count_update' && not_empty($cl['is_logged'])) {
    global $db, $cl;

    $user_id = (not_empty($cl["is_logged"])) ? $me["id"] : 0;
    $db = $db->where('user_id', $user_id);
    $has_reshared = $db->getValue(T_AD_SHARE, 'COUNT(*)');
    
    if (not_empty($_POST['ad_id'])) {
        $adId = $_POST['ad_id'];
        if ($has_reshared) {
            $db = $db->where('id', $adId);
            $reposts_count = $db->getValue(T_ADS, 'reposts_count');

            $db = $db->where('id', $adId);
            $up = $db->update(T_ADS, array(
                'reposts_count' => $reposts_count - 1
            ));

            if ($up) {
                $db = $db->where('id', $adId);
                $reposts_count = $db->getValue(T_ADS, 'reposts_count');
    
                $db = $db->where('user_id', $user_id);
                $adRepost = $db->delete(T_AD_SHARE);
    
                $data = [
                    'errors' => 0,
                    'status' => 200,
                    'reposts_count' => $reposts_count
                ];
    
                return json_encode($data);
            }
        } else {
            $db = $db->where('id', $adId);
            $reposts_count = $db->getValue(T_ADS, 'reposts_count');
            
            $db = $db->where('id', $adId);
            $up = $db->update(T_ADS, array(
                'reposts_count' => $reposts_count + 1
            ));
            
            if ($up) {
                $db = $db->where('id', $adId);
                $reposts_count = $db->getValue(T_ADS, 'reposts_count');
                
                $repostByData = [
                    'ad_id' => $adId,
                    'user_id' => $user_id,
                    'time' => time()
                ];
                $adRepost = $db->insert(T_AD_SHARE, $repostByData);
    
                $data = [
                    'errors' => 0,
                    'status' => 200,
                    'reposts_count' => $reposts_count
                ];
    
                return json_encode($data);
            }
        }
    }
}

elseif ($action == 'view_count_update' && not_empty($cl['is_logged'])) {
    global $db, $cl;

    $user_id = (not_empty($cl["is_logged"])) ? $me["id"] : 0;
    $db = $db->where('user_id', $user_id);
    $has_viewed = $db->getValue(T_AD_VIEW, 'COUNT(*)');

    // if (!$has_viewed) {
        if (not_empty($_POST['ad_id'])) {
            $adId = $_POST['ad_id'];
    
            $db = $db->where('id', $adId);
            $views_count = $db->getValue(T_ADS, 'views');
    
            $db = $db->where('id', $adId);
            $up = $db->update(T_ADS, array(
                'views' => $views_count + 1
            ));
    
            if ($up) {
                $db = $db->where('id', $adId);
                $views_count = $db->getValue(T_ADS, 'views');
    
                $viewByData = [
                    'ad_id' => $adId,
                    'user_id' => $user_id,
                    'time' => time()
                ];
                $adView = $db->insert(T_AD_VIEW, $viewByData);
    
                $data = [
                    'errors' => 0,
                    'status' => 200,
                    'views_count' => $views_count
                ];
    
                return json_encode($data);
            }
        }
    // }
}