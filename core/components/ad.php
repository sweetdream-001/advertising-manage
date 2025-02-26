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

function cl_raw_ad_data($ad_id = false) {
	global $db;

    if (not_num($ad_id)) {
        return false;
    } 

    $db = $db->where('status', array('active', 'inactive', 'orphan'), 'IN');
    $db = $db->where('id', $ad_id);
    $ad = $db->getOne(T_ADS);

    if (empty($ad)) {
        return false;
    }

    return $ad;
}

function cl_insert_ad_data($data = array()) {
	global $db;

	$insert = $db->insert(T_ADS, $data);

	return ($insert == true) ? $insert : false;
}

function cl_update_ad_data($ad_id = null, $data = array()) {
    global $db;

	if ((not_num($ad_id)) || (empty($data) || is_array($data) != true)) {
        return false;
    } 

    $db     = $db->where('id', $ad_id);
    $update = $db->update(T_ADS, $data);
    
    return ($update == true) ? true : false;
}

function cl_get_timeline_ads($ad_id = false) {
    global $db, $cl;

    $udata        = ((not_empty($cl['is_logged'])) ? $cl['me'] : false);
    $sql          = cl_sqltepmlate('components/sql/ads/fetch_feed_ads', array(
        't_ads'   => T_ADS,
        't_users' => T_USERS,
        'udata'   => $udata,
        'ad_id'   => $ad_id
    ));

    $views   = cl_session('ad_views');
    $clicks  = cl_session('ad_clicks');
    $ad_data = $db->rawQueryOne($sql);
    $data    = array();

    if (is_array($views) != true) {
        $views = array();
    }

    if (is_array($clicks) != true) {
        $clicks = array();

        cl_session('ad_clicks', $clicks);
    }

    if (cl_queryset($ad_data)) {
        if (isset($ad_data['og_data'])) {
			if (!empty($ad_data['og_data'])) {
				$adYTData = json_decode($ad_data['og_data']);
				$ad_data['og_data']       = $adYTData->og_data;
			}
		}
        $ad_data['is_conversed'] = false;
        $ad_data['advertising']  = true;
        $ad_data['cover']        = cl_get_media($ad_data['cover']);
        $ad_data['time']         = cl_time2str($ad_data['time']);
        $ad_data["url"]           = cl_link(cl_strf("ad_thread/%d",$ad_data['id']));
        $ad_data["url2"]           = cl_link(cl_strf("ad_tmedia/%d",$ad_data['id']));
        $ad_data['description']  = stripcslashes($ad_data['description']);
        $ad_data['description']  = htmlspecialchars_decode($ad_data['description'], ENT_QUOTES);
        $ad_data['description']  = cl_linkify_urls($ad_data['description']);
        $ad_data['description']  = cl_rn2br($ad_data['description']);
        $ad_data['description']  = cl_strip_brs($ad_data['description']);
        $ad_data['company']      = stripcslashes($ad_data['company']);
        $ad_data['company']      = htmlspecialchars_decode($ad_data['company'], ENT_QUOTES);
        $ad_data['cta']          = stripcslashes($ad_data['cta']);
        $ad_data['cta']          = htmlspecialchars_decode($ad_data['cta'], ENT_QUOTES);
        $ad_data['show_stats']   = false;
        $ad_data['is_owner']     = false;
        $ad_data['has_liked']    = false;
        $ad_data['has_reposted'] = false;
        $ad_data['has_viewed']   = false;
        $ad_data['owner']        = array(
            'avatar'             => cl_get_media($ad_data['avatar']),
            'name'               => $ad_data['name'],
            'username'           => $ad_data['username'],
            'verified'           => $ad_data['verified'],
            'url'                => cl_link($ad_data['username'])
        );

        if (not_empty($udata)) {
            $ad_data['is_owner'] = ($ad_data['user_id'] == $udata['id']);
        }

        if (in_array($ad_data['id'], $clicks)) {
            $ad_data['is_conversed'] = true;
        }

        if (in_array($ad_data['id'], $views) != true) {
            array_push($views, $ad_data['id']);

            cl_session('ad_views', $views);

            cl_update_ad_data($ad_data['id'], array(
                'views' => ($ad_data['views'] += 1)
            ));
        }

        $data = $ad_data;
    }
    // echo '<pre>';print_r($data);'</pre>';die;

    return $data;
}

function cl_ad_has_liked($user_id = false, $post_id = false) {
	global $db, $cl;

	if (not_num($user_id) || not_num($post_id)) {
		return false;
	}

	$db = $db->where('user_id', $user_id);
	$db = $db->where('pub_id', $post_id);
	$qr = $db->getValue(T_LIKES, 'COUNT(*)');

	return (($qr > 0) ? true : false);
}

function cl_ad_data($post = array()) {
    global $cl;

	if (empty($post)) {
		return false;
	}

    $post_owner_data       = cl_user_data($post["user_id"]);
    $user_id               = ((empty($cl['is_logged'])) ? false : $cl['me']['id']);
	if (!empty($post['og_data'])) {
		$adYTData = json_decode($post['og_data']);
		$post['yt_url']       = cl_strf("https://www.youtube.com/embed/%s", cl_get_youtube_video_id($adYTData->og_data->url));
	}
    $post["advertising"]   = true;
    $post["time_raw"]      = $post["time"];
    $post['og_text']       = cl_encode_og_text($post['description']);
    $post['og_image']      = $cl['config']['site_logo'];
    $post["time"]          = cl_time2str($post["time"]);
    $post['text']          = stripcslashes($post['description']);
	$post['text']          = htmlspecialchars_decode($post['description'], ENT_QUOTES);
    $post["htags"]         = cl_listify_htags($post['description']);
	$post["text"]          = cl_linkify_urls($post['description']);
    $post["text"]          = cl_tagify_htags($post['description'], $post["htags"]);
    $post["text"]          = cl_linkify_htags($post['description']);
    $post["text"]          = cl_likify_mentions($post['description']);
    $post["url"]           = cl_link(cl_strf("ad_thread/%d",$post['id']));
	$post["url2"]          = cl_link(cl_strf("ad_tmedia/%d",$post['id']));
    $post["replys_count"]  = cl_number($post["replys_count"]);
    $post["reposts_count"] = cl_number($post["reposts_count"]);
    $post["likes_count"]   = cl_number($post["likes_count"]);
    $post["can_delete"]    = false;
    $post["can_edit"]      = false;
	$post["media"]         = array();
	$post["is_owner"]      = false;
	$post["has_liked"]     = false;
	$post["has_saved"]     = false;
	$post["has_reposted"]  = false;
	$post["is_blocked"]    = false;
	$post["is_reported"]   = false;
	$post["me_blocked"]    = false;
	$post["can_see"]       = true;
    $post["reply_to"]      = array();
    $post["type"]          = "ad";
    $post['target']        = "ad";
    $post["owner"]         = array(
		'id'               => $post_owner_data['id'],
		'url'              => $post_owner_data['url'],
		'avatar'           => $post_owner_data['avatar'],
		'username'         => $post_owner_data['username'],
		'name'             => $post_owner_data['name'],
		'verified'         => $post_owner_data['verified']
	);

    // if ($post["type"] != "text") {
	// 	$post["media"] = cl_get_post_media($post["id"]);

	// 	if ($post["type"] == "image") {
	// 		$post['og_image'] = fetch_or_get($post["media"][0]['src'], false);

	// 		if (empty($post['og_image'])) {
	// 			$post['og_image'] = $cl['config']['site_logo'];
	// 		}

	// 		else {
	// 			$post['og_image'] = cl_get_media($post['og_image']);
	// 		}
	// 	}

	// 	else if ($post["type"] == "gif") {
	// 		$post['og_image'] = fetch_or_get($post["gif"], $cl['config']['site_logo']);
	// 	}

	// 	else if ($post["type"] == "video") {
	// 		$post['og_image'] = fetch_or_get($post["media"][0]["x"]["poster_thumb"], false);

	// 		if (empty($post['og_image'])) {
	// 			$post['og_image'] = $cl['config']['site_logo'];
	// 		}

	// 		else {
	// 			$post['og_image'] = cl_get_media($post['og_image']);
	// 		}
	// 	}

	// 	else if($post["type"] == "poll") {
	// 		$post["poll"] = cl_cacl_poll_votes(json($post["poll_data"]));
	// 	}
	// }
	// else {
	// 	if (not_empty($post['og_data'])) {
	// 		$post['og_data'] = json($post['og_data']);

	// 		if (cl_is_valid_og($post['og_data'])) {
	// 			if (not_empty($post['og_data']["image"]) && not_empty($post['og_data']["image_loc"])) {
	// 				$post['og_data']["image"] = cl_get_media($post['og_data']["image"]);
	// 			}

	// 			if (cl_get_youtube_video_id($post['og_data']['url'])) {
	// 				$post['og_data']["video_embed"] = cl_strf("https://www.youtube.com/embed/%s", cl_get_youtube_video_id($post['og_data']['url']));
	// 			}

	// 			else if(cl_get_vimeo_video_id($post['og_data']['url'])) {
	// 				$post['og_data']["video_embed"] = cl_strf("https://vimeo.com/%s", cl_get_vimeo_video_id($post['og_data']['url']));
	// 			}
				
	// 			else if(cl_get_vimeo_video_id($post['og_data']['url'])) {
	// 				$post['og_data']["video_embed"] = cl_strf("https://vimeo.com/%s", cl_get_vimeo_video_id($post['og_data']['url']));
	// 			}

	// 			else if(cl_is_google_mapurl($post['og_data']['url'])) {
	// 				$post['og_data']["google_maps_embed"] = true;
	// 			}
	// 		}
	// 	}
	// }

    if (not_empty($user_id) && ($post['user_id'] == $user_id)) {
		$post["is_owner"] = true;
	}
    
    if (!empty($post["audience"])) {
		$audience = $post["audience"];
		$audience = trim($audience, '[]');
		$audience = explode(',', $audience);
		$audience = array_map(function($value) {
			$value = trim($value);
			
			if (!empty($value) && $value[0] == '"' && $value[strlen($value) - 1] == '"') {
				$value = substr($value, 1, -1);
			}
			
			return $value;
		}, $audience);
		
	} else {
		$audience = [];
	}
	
    if (not_empty($cl["is_admin"]) || in_array($user_id, $audience) || not_empty($post["is_owner"])) {
        $post["can_see"] = true;
	}

    if (not_empty($post["is_owner"]) || not_empty($cl["is_admin"])) {
		$post["can_delete"] = true;
	}

    if (not_empty($post["is_owner"])) {
		if (empty($post["edited"])) {
			$post["can_edit"] = true;
			$post["edit_url"] = cl_link(cl_strf("edit_ad/%d", $post["id"]));
		}
	}

    if (not_empty($post["is_owner"])) {
		if (not_empty($post["edited"])) {
			$post["can_edit"] = true;
			$post["edit_url"] = cl_link(cl_strf("edit_ad/%d", $post["id"]));
		}
	}

    if (not_empty($user_id)) {
		$post["has_liked"]    = cl_has_liked($user_id, $post["id"]);
		$post["has_saved"]    = cl_has_saved($user_id, $post["id"]);
		$post["has_reposted"] = cl_has_reposted($user_id, $post["id"]);

		if (cl_is_blocked($post['user_id'], $user_id)) {
			$post['me_blocked'] = true;
		}

		else if (cl_is_blocked($user_id, $post['user_id'])) {
			$post['is_blocked'] = true;
		}

		if (empty($post["can_see"]) && $post["priv_wcs"] == "followers") {
			if (cl_is_following($user_id, $post["user_id"])) {
				$post["can_see"] = true;
			}
		}

		if (cl_is_reported($user_id, $post['id'])) {
			$post['is_reported'] = true;
		}
	}

    if (empty($post['offset_id'])) {
		$post["offset_id"] = $post['id'];
	}

    if (not_empty($post['offset_id'])) {
		$thread = cl_raw_post_data($post['offset_id']);

		if (not_empty($thread)) {

			$thread_owner = cl_user_data($thread['user_id']);

			if (not_empty($thread_owner)) {
				$post["reply_to"] = array(
					'id'          => $thread_owner['id'],
					'url'         => $thread_owner['url'],
					'avatar'      => $thread_owner['avatar'],
					'username'    => $thread_owner['username'],
					'name'        => $thread_owner['name'],
					'gender'      => $thread_owner['gender'],
					'is_owner'    => false,
					'thread_url'  => cl_link(cl_strf("ad_thread/%d", $post['offset_id']))
				);

				if (not_empty($user_id) && ($post["reply_to"]["id"] == $user_id)) {
					$post["reply_to"]["is_owner"] = true;
				}
			}
		}
		else {
			cl_recursive_delete_post($post['id']);
		}
	}

    return $post;
}

function cl_update_ad_thread_replys($id = false, $count = "plus") {
	global $db, $cl;

	if (not_num($id)) {
		return 0;
	}

	$db           = $db->where('id', $id);
	$post         = $db->getOne(T_ADS);
	$replys_count = 0;

	if (cl_queryset($post)) {
		$replys_count = (($count == "plus") ? ($post['replys_count'] += 1) : ($post['replys_count'] -= 1));
		$replys_count = ((is_posnum($replys_count)) ? $replys_count : 0);
		
		cl_update_ad_data($id, array(
			'replys_count' => $replys_count
		));
	}

	return $replys_count;
}