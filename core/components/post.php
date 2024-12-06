<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)							@
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                    @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

function cl_create_orphan_post($user_id = null, $type = "text") {
	global $db;

	if (not_num($user_id)) {
		return false;
	}

	$id           = $db->insert(T_PUBS, array(
		"user_id" => $user_id,
		"status"  => "orphan",
		"type"    => $type
	));

	return (is_posnum($id)) ? $id : 0;
}

function cl_get_orphan_post($id = null) {
	//exit("Here1");
	global $db;

	if (not_num($id)) {
		return false;
	}

	$db           = $db->where("id", $id);
	$db           = $db->where("status", "orphan");
	$query_result = $db->getOne(T_PUBS);
	$data         = array();

	if (cl_queryset($query_result)) {
		if (in_array($query_result['type'], array("image", "video", "audio", "document"))) {
			$media = cl_get_post_media($id);

			if (cl_queryset($media)) {
				$query_result['media'] = $media;
			}
		}

		$data = $query_result;
	}

	return $data;
}

function cl_get_post_media($post_id = false) {
	global $db;

	if (not_num($post_id)) {
		return array();
	}

	$db   = $db->where("pub_id",$post_id);
	$qr   = $db->get(T_PUBMEDIA);
	$data = array();

	if (cl_queryset($qr)) {
		foreach ($qr as $row) {
			$row['x'] = json($row['json_data']);
			$data[]   = $row;
		}
	}

	return $data;
}

function cl_delete_post_junk_files($post_id = false, $post_type = null) {
	global $db;

	if (not_num($post_id) || in_array($post_type, array("image", "video", "gif")) != true) {
		return false;
	}

	else {
		$db = $db->where("pub_id", $post_id);
		$db = $db->where("type", $post_type, "!=");
		$qr = $db->get(T_PUBMEDIA);

		if (cl_queryset($qr)) {
			foreach ($qr as $media_data) {
				if (in_array($media_data['type'], array('image','video'))) {
   
                    $json_data = json($media_data['json_data']);

                    cl_delete_media($media_data['src']);

                    if (not_empty($json_data['image_thumb'])) {
                        cl_delete_media($json_data['image_thumb']);
                    }

                    else if(not_empty($json_data['poster_thumb'])){
                    	cl_delete_media($json_data['poster_thumb']);
                    }
                }
			}

			$db = $db->where("pub_id", $post_id);
			$db = $db->where("type", $post_type, "!=");
			$qr = $db->delete(T_PUBMEDIA);
		}
	}
}

function cl_delete_orphan_posts($user_id = null) {
	global $db;

	if (not_num($user_id)) {
		return false;
	}

	$db           = $db->where("user_id", $user_id);
	$db           = $db->where("status", "orphan");
	$query_result = $db->get(T_PUBS);
	$data         = array();

	if (cl_queryset($query_result)) {

		foreach ($query_result as $row) {
			if (in_array($row['type'], array("image", "video", "audio"))) {
				$media = cl_get_post_media($row['id']);

				if (cl_queryset($media)) {
					foreach ($media as $media_data) {
						if (in_array($media_data['type'], array('image','video', 'audio'))) {
           
		                    $json_data = json($media_data['json_data']);

		                    cl_delete_media($media_data['src']);

		                    if (not_empty($json_data['image_thumb'])) {
		                        cl_delete_media($json_data['image_thumb']);
		                    }
		                    else if(not_empty($json_data['poster_thumb'])){
		                    	cl_delete_media($json_data['poster_thumb']);
		                    }
		                }
					}

					$db->where("pub_id",$row['id'])->delete(T_PUBMEDIA);
				}
			}
		}

		$db->where("user_id", $user_id)->where("status", "orphan")->delete(T_PUBS);
	}
}

function cl_update_post_data($post_id = null, $data = array()) {
    global $db;
    if ((not_num($post_id)) || (empty($data) || is_array($data) != true)) {
        return false;
    } 

    $db     = $db->where('id', $post_id);
    $update = $db->update(T_PUBS,$data);
    return ($update == true) ? true : false;
}

function cl_upsert_htags($text = "") {
	global $db;

	if (not_empty($text)) {

		preg_match_all('/(?:\s|^)#{1,}([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]*\s]+)/iu', $text, $htags);

		$htags = fetch_or_get($htags[1], null);

		if (not_empty($htags)) {
			$htags = array_unique($htags);

			foreach ($htags as $key => $htag) {
				$htag      = cl_remove_emoji($htag);
				$htag_id   = 0;
				$db        = $db->where('tag', cl_text_secure($htag));
				$htag_data = $db->getOne(T_HTAGS, array('id', 'posts'));

				if (not_empty($htag_data)) {

					$htag_id = $htag_data['id'];

					$db->where('id', $htag_id)->update(T_HTAGS, array(
						'time'  => time(),
						'posts' => ($htag_data['posts'] += 1)
					));
				}
				else{
					$htag_id    = $db->insert(T_HTAGS, array(
						'posts' => 1,
						'tag'   => $htag,
						'time'  => time()
					));
				}

				$text = preg_replace(cl_strf("/#%s\b/", $htag), cl_strf("{#id:%s#}", $htag_id), $text);
			}
		}
	}

	return $text;
}

function cl_update_htags($text = "") {
	global $db;

	if (not_empty($text)) {
		preg_match_all('/(\{\#id\:([0-9]+)\#\})/i', $text, $matches);

		$matches = fetch_or_get($matches[2], null);

		if (not_empty($matches)) {
			$matches = array_unique($matches);
			$matches = array_count_values($matches);

			foreach ($matches as $htag_id => $htag_usage) {

				$htag_data = cl_db_get_item(T_HTAGS, array('id' => $htag_id));

				if (not_empty($htag_data)) {
					$num = ($htag_data['posts'] -= $htag_usage);
					$num = ((is_posnum($num)) ? $num : 0);

					if (empty($num)) {
						$db = $db->where('id', $htag_data['id']);
						$qr = $db->delete(T_HTAGS);
					}
					else {
						$db = $db->where('id', $htag_data['id']);
						$qr = $db->update(T_HTAGS, array('posts' => $num));
					}
				}
			}
		}
	}
}

function cl_listify_htags($text = "") {
	global $db;

	if (not_empty($text)) {
		preg_match_all('/(\{\#id\:([0-9]+)\#\})/i', $text, $matches);

		$matches = fetch_or_get($matches[2], null);

		if (not_empty($matches)) {
			$db    = $db->where('id', $matches, "IN");		
			$htags = $db->get(T_HTAGS, null, array('id', 'tag'));

			return $htags;
		}
	}

    return array();
}

function cl_tagify_htags($text = "", $htags = array()) {
	global $db;

	if (not_empty($text) && not_empty($htags)) {
		foreach ($htags as $htag) {
			$text = str_replace(cl_strf("{#id:%d#}", $htag['id']), cl_strf("#%s", $htag['tag']), $text);
		}
	}

    return $text;
}

function cl_linkify_htags($text = "") {
    $text = preg_replace_callback('/(?:\s|^)#{1,}([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]*\s]+)/iu', function($m) {

        $tag = fetch_or_get($m[1], "");

        if (not_empty($tag)) {
        	return (" " . cl_html_el('a', cl_strf("#%s", $tag), array(
	            'href' => cl_link(cl_strf("search/posts?q=%s",cl_remove_emoji($tag))),
	            'class' => 'inline-link'
	        )) . " ");
        }

    }, $text);

    return $text;
}

function cl_get_hot_topics($limit = 8, $offset = false) {
    global $db, $cl, $me;

    $data = array();
    $sql  = cl_sqltepmlate('components/sql/post/fetch_htags', array(
    	't_htags' => T_HTAGS,
    	'limit'   => $limit,
    	'offset'  => $offset
    ));

    $tags = $db->rawQuery($sql);

    if (cl_queryset($tags)) {
    	foreach ($tags as $tag_data) {
    		$tag_data['tag']     = cl_rn_strip($tag_data['tag']);
    		$tag_data['hashtag'] = cl_strf("#%s", $tag_data['tag']);
    		$tag_data['url']     = cl_link(cl_strf("search/posts?q=%s", cl_remove_emoji($tag_data['tag'])));
    		$tag_data['total']   = cl_number($tag_data['posts']);
    		$data[]              = $tag_data;
    	}
    }
    
    return $data;
}

function cl_get_htag_id($htag = "") {
	global $db;

	if (empty($htag)) {
		return false;
	}

	$htag_id      = 0;
	$db           = $db->where('tag', $htag);
	$query_result = $db->getOne(T_HTAGS, 'id');

	if (cl_queryset($query_result)) {
		$htag_id = $query_result['id'];
	}

	return $htag_id;
}

function cl_update_thread_replys($id = false, $count = "plus") {
	global $db, $cl;

	if (not_num($id)) {
		return 0;
	}

	$db           = $db->where('id', $id);
	$post         = $db->getOne(T_PUBS);
	$replys_count = 0;

	if (cl_queryset($post)) {
		$replys_count = (($count == "plus") ? ($post['replys_count'] += 1) : ($post['replys_count'] -= 1));
		$replys_count = ((is_posnum($replys_count)) ? $replys_count : 0);
		
		cl_update_post_data($id, array(
			'replys_count' => $replys_count
		));
	}

	return $replys_count;
}

function cl_post_data($post = array()) {

	//echo "<pre>";print_r($post);
	
	global $cl;

	if (empty($post)) {
		return false;
	}

	$post_owner_data       = cl_user_data($post["user_id"]);
	$user_id               = ((empty($cl['is_logged'])) ? false : $cl['me']['id']);
	$post["advertising"]   = false;
	$post["time_raw"]      = $post["time"];
	$post['og_text']       = cl_encode_og_text($post['text']);
	$post['og_image']      = $cl['config']['site_logo'];
	$post["time"]          = cl_time2str($post["time"]);
	$post['text']          = stripcslashes($post['text']);
	$post['text']          = htmlspecialchars_decode($post['text'], ENT_QUOTES);
	$post["htags"]         = cl_listify_htags($post['text']);
	$post["text"]          = cl_linkify_urls($post['text']);
    $post["text"]          = cl_tagify_htags($post['text'], $post["htags"]);
    $post["text"]          = cl_linkify_htags($post['text']);
    $post["text"]          = cl_likify_mentions($post['text']);
    $post["url"]           = cl_link(cl_strf("thread/%d",$post['id']));
	$post["url2"]           = cl_link(cl_strf("tmedia/%d",$post['id']));
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
	$post["can_see"]       = false;
	$post["reply_to"]      = array();
	$post["owner"]         = array(
		'id'               => $post_owner_data['id'],
		'url'              => $post_owner_data['url'],
		'avatar'           => $post_owner_data['avatar'],
		'username'         => $post_owner_data['username'],
		'name'             => $post_owner_data['name'],
		'verified'         => $post_owner_data['verified']
	);

	if ($post["type"] != "text") {
		$post["media"] = cl_get_post_media($post["id"]);

		if ($post["type"] == "image") {
			$post['og_image'] = fetch_or_get($post["media"][0]['src'], false);

			if (empty($post['og_image'])) {
				$post['og_image'] = $cl['config']['site_logo'];
			}

			else {
				$post['og_image'] = cl_get_media($post['og_image']);
			}
		}

		else if ($post["type"] == "gif") {
			$post['og_image'] = fetch_or_get($post["gif"], $cl['config']['site_logo']);
		}

		else if ($post["type"] == "video") {
			$post['og_image'] = fetch_or_get($post["media"][0]["x"]["poster_thumb"], false);

			if (empty($post['og_image'])) {
				$post['og_image'] = $cl['config']['site_logo'];
			}

			else {
				$post['og_image'] = cl_get_media($post['og_image']);
			}
		}

		else if($post["type"] == "poll") {
			$post["poll"] = cl_cacl_poll_votes(json($post["poll_data"]));
		}
	}
	else {
		if (not_empty($post['og_data'])) {
			$post['og_data'] = json($post['og_data']);

			if (cl_is_valid_og($post['og_data'])) {
				if (not_empty($post['og_data']["image"]) && not_empty($post['og_data']["image_loc"])) {
					$post['og_data']["image"] = cl_get_media($post['og_data']["image"]);
				}

				if (cl_get_youtube_video_id($post['og_data']['url'])) {
					$post['og_data']["video_embed"] = cl_strf("https://www.youtube.com/embed/%s", cl_get_youtube_video_id($post['og_data']['url']));
				}

				else if(cl_get_vimeo_video_id($post['og_data']['url'])) {
					$post['og_data']["video_embed"] = cl_strf("https://vimeo.com/%s", cl_get_vimeo_video_id($post['og_data']['url']));
				}
				
				else if(cl_get_vimeo_video_id($post['og_data']['url'])) {
					$post['og_data']["video_embed"] = cl_strf("https://vimeo.com/%s", cl_get_vimeo_video_id($post['og_data']['url']));
				}

				else if(cl_is_google_mapurl($post['og_data']['url'])) {
					$post['og_data']["google_maps_embed"] = true;
				}
			}
		}
	}
	if (not_empty($user_id) && ($post['user_id'] == $user_id)) {
		$post["is_owner"] = true;
	}

	if (not_empty($cl["is_admin"]) || $post["priv_wcs"] == "everyone" || not_empty($post["is_owner"])) {
		$post["can_see"] = true;
	}

	if (not_empty($post["is_owner"]) || not_empty($cl["is_admin"])) {
		$post["can_delete"] = true;
	}

	
	
	if (not_empty($post["is_owner"])) {
		if (empty($post["edited"])) {
			$post["can_edit"] = true;
			$post["edit_url"] = cl_link(cl_strf("edit_post/%d", $post["id"]));
		}
	}
	if (not_empty($post["is_owner"])) {
		if (not_empty($post["edited"])) {
			$post["can_edit"] = true;
			$post["edit_url"] = cl_link(cl_strf("edit_post/%d", $post["id"]));
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

	if (not_empty($post['thread_id'])) {
		$thread = cl_raw_post_data($post['thread_id']);

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
					'thread_url'  => cl_link(cl_strf("thread/%d", $post['thread_id']))
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

	//echo "<pre>";print_r($post);exit("IAM");

	return $post;
}

function cl_raw_post_data($post_id = 0) {
    global $db;

    if (not_num($post_id)) {
        return false;
    } 

    $db        = $db->where('status', array('active', 'inactive', 'deleted'), 'IN');
    $db        = $db->where('id', $post_id);
    $post_data = $db->getOne(T_PUBS);

    if (empty($post_data)) {
        return false;
    }

    return $post_data;
}

function cl_recursive_delete_post($post_id = false) {
	global $db, $cl;

	if (not_num($post_id)) {
		return false;
	}

	$post_data = cl_raw_post_data($post_id);

	if (not_empty($post_data)) {

		if (not_empty($post_data["og_data"])) {
			$post_data['og_data'] = json($post_data['og_data']);

			if (not_empty($post_data['og_data']["image_loc"])) {
				cl_delete_media($post_data['og_data']["image_loc"]);
			}
		}

		$post_data['media']  = cl_get_post_media($post_id);
		$db                  = $db->where('thread_id', $post_id);
		$post_data['replys'] = $db->get(T_PUBS,null,array('id'));

		foreach ($post_data['media'] as $row) {
			if (in_array($row['type'], array('image','video'))) {
				cl_delete_media($row['src']);

				if (not_empty($row['x']['image_thumb'])) {
					cl_delete_media($row['x']['image_thumb']);
				}
				else if(not_empty($row['x']['poster_thumb'])) {
					cl_delete_media($row['x']['poster_thumb']);
				}
			}
		}

		$db = $db->where('id', $post_id);
		$rm = $db->delete(T_PUBS);

		$db = $db->where('pub_id', $post_id);
		$rm = $db->delete(T_PUBMEDIA);

		$db = $db->where('publication_id', $post_id);
		$rm = $db->delete(T_BOOKMARKS);

		$db = $db->where('pub_id', $post_id);
		$rm = $db->delete(T_LIKES);

		$db = $db->where('post_id', $post_id);
		$rm = $db->delete(T_PUB_REPORTS);

		$db = $db->where('subject', array('like', 'repost', 'mention', 'reply'), 'IN');
        $db = $db->where('entry_id', $post_id);
        $rm = $db->delete(T_NOTIFS);

        if (not_empty($post_data['text'])) {
        	cl_update_htags($post_data['text']);
        }
        
		if (not_empty($post_data['replys'])) {
			foreach ($post_data['replys'] as $row) {
				cl_recursive_delete_post($row['id']);
			}
		}
	}
}

function cl_can_reply($thread_data = array()) {
	global $me, $cl;

	if (not_empty($cl["is_logged"]) && not_empty($thread_data)) {
		if($thread_data["user_id"] == $me["id"]) {
			return true;
		}

		else {
			if (cl_is_blocked($thread_data['user_id'], $me["id"])) {
				return false;
			}

			else if (cl_is_blocked($me["id"], $thread_data['user_id'])) {
				return false;
			}

			else if($thread_data["priv_wcs"] == "followers" && cl_is_following($me["id"], $thread_data['user_id']) != true) {
				return false;
			}

			else if($thread_data["priv_wcr"] == "followers" && cl_is_following($me["id"], $thread_data['user_id']) != true) {
				return false;
			}

			else if($thread_data["priv_wcr"] == "mentioned") {
				$mentions = cl_get_user_mentions($thread_data["text"]);

				if (empty($mentions) || in_array($me["raw_uname"], $mentions) != true) {
					return false;
				}

				else {
					return true;
				}
			}

			else {
				return true;
			}
		}
	}

	else {
		return false;
	}
}

function cl_has_liked($user_id = false, $post_id = false) {
	global $db, $cl;

	if (not_num($user_id) || not_num($post_id)) {
		return false;
	}

	$db = $db->where('user_id', $user_id);
	$db = $db->where('pub_id', $post_id);
	$qr = $db->getValue(T_LIKES, 'COUNT(*)');

	return (($qr > 0) ? true : false);
}

function cl_has_saved($user_id = false, $post_id = false) {
	global $db, $cl;

	if (not_num($user_id) || not_num($post_id)) {
		return false;
	}

	$db = $db->where('user_id', $user_id);
	$db = $db->where('publication_id', $post_id);
	$qr = $db->getValue(T_BOOKMARKS, 'COUNT(*)');

	return (($qr > 0) ? true : false);
}

function cl_has_reposted($user_id = false, $post_id = false) {
	global $db, $cl;

	if (not_num($user_id) || not_num($post_id)) {
		return false;
	}

	$db = $db->where('user_id', $user_id);
	$db = $db->where('publication_id', $post_id);
	$db = $db->where('type', 'repost');
	$qr = $db->getValue(T_POSTS, 'COUNT(*)');

	return (($qr > 0) ? true : false);
}

function cl_get_post_likes($post_id = false, $limit = 10, $offset = false) {
    global $db, $cl;

    if (is_posnum($post_id) != true) {
        return false;
    }

    $data         = array();
    $sql          = cl_sqltepmlate('components/sql/post/fetch_likes',array(
        't_users' => T_USERS,
        't_likes' => T_LIKES,
        'post_id' => $post_id,
        'limit'   => $limit,
        'offset'  => $offset
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
        	$row['about']            = cl_rn_strip($row['about']);
            $row['about']            = stripslashes($row['about']);
            $row['name']             = cl_strf("%s",$row['fname']);      
            $row['avatar']           = cl_get_media($row['avatar']);
            $row['url']              = cl_link($row['username']);
            $row['username']         = cl_strf($row['username']);
            $row['last_active']      = date("d M, y h:m A",$row['last_active']);
            $row['is_following']     = false;
            $row['follow_requested'] = false;
            $row['is_user']          = false;
            $row['country_a2c']      = fetch_or_get($cl['country_codes'][$row['country_id']], 'us');
            $row['country_name']     = cl_translate($cl['countries'][$row['country_id']], 'Unknown');
            $row['common_follows']   = array();

            if (not_empty($cl['is_logged'])) {
                $row['is_following'] = cl_is_following($cl['me']['id'], $row['id']);

                if ($cl['me']['id'] == $row['id']) {
                    $row['is_user'] = true; 
                }

                if (empty($row['is_following'])) {
                	$row['follow_requested'] = cl_follow_requested($cl['me']['id'], $row['id']);
                }
            }

            $data[] = $row;
        }
    }

    return $data;
}

function cl_cacl_poll_votes($poll = array()) {
	$data           = array(
		"has_voted" => cl_is_poll_voted($poll),
		"total"     => 0,
		"options"   => array()
	);

	foreach ($poll as $poll_option) {
		$data["total"] += $poll_option["votes"];
	}

	foreach ($poll as $poll_option) {
		$poll_option_data = array(
			"percentage"  => 0,
			"total"       => $poll_option["votes"],
			"option"      => $poll_option["option"]
		);

		if (is_posnum($data["total"])) {
			$poll_option_data["percentage"] = number_format(($poll_option["votes"] / $data["total"]) * 100);
		}

		array_push($data["options"], $poll_option_data);

		if (not_empty($user_id) && in_array($user_id, $poll_option["voters"])) {
			$data["has_voted"] = 1;
		}
	}

	return $data;
}

function cl_is_poll_voted($poll = array()) {
	global $cl, $me;

	$user_id = (not_empty($cl["is_logged"])) ? $me["id"] : 0;

	foreach ($poll as $poll_option) {
		if (not_empty($user_id) ) {
			if (in_array($user_id, $poll_option["voters"])) {
				return 1;
			}
		}
	}

	return 0;
}

// new code 8 april updated

function youTubeToText($url)
{
	if(empty($url) || is_url($url)) {

		$og_url    = fetch_or_get($url, "");

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
					$data['status']  = 200;
					$data['og_data'] = $og_data_values;
					return $data;
				}
			}
		} 

		catch (Exception $e) {
			/*pass*/ 
			$data['status']  = 400;
			$data['og_data'] = $e->getMessage();
			return $data;
		}
	}

}
	
	function concatUrlWithText($text,$url){

		$pattern = '/(https?:\/\/[^\s]+)/';
		$urls = []; // Initialize an empty array to store URLs
	
		if (preg_match_all($pattern, $text, $matches)) {
			// Check if any URLs were matched
			if (!empty($matches[0])) {
				// $matches[0] contains an array of matched URLs
				$urls = $matches[0];
			}
		}
		// Check if any URLs were found
		if (!empty($urls)) {
	
			$youtub_url = $urls[0]; // Get the first matched URL
			$update['type']= "text";
	
		}

	}


	function concatUrlWithTextNew($text, $url) {
		$pattern = '/(https?:\/\/[^\s]+)/';
		$urls = []; // Initialize an empty array to store URLs
	
		if (preg_match_all($pattern, $text, $matches)) {
			// Check if any URLs were matched
			if (!empty($matches[0])) {
				// $matches[0] contains an array of matched URLs
				$urls = $matches[0];
			}
		}

		

		// Check if any URLs were found
		if (!empty($urls) && !empty($url)) {
			// Remove all URLs from the text
			$text = preg_replace($pattern, '', $text);
	
			// Concatenate the URL with the text
			$newText = $text . ' ' . $urls;

			// $text .= ' ' . $url;
		}
		return $newText;

		// return $text;
	}

	function removeMultip($text){
		$pattern = '/(https?:\/\/[^\s]+)/';
		$urls = []; // Initialize an empty array to store URLs
	
		if (preg_match_all($pattern, $text, $matches)) {
			// Check if any URLs were matched
			if (!empty($matches[0])) {
				// $matches[0] contains an array of matched URLs
				$urls = $matches[0];
				// Get the first matched URL
				$firstUrl = $urls[0];
				// Remove all URLs except the first one
				$text = preg_replace($pattern, "", $text);
				// Replace the first URL back into the text
				$text = str_replace($firstUrl, '', $text) . $firstUrl;
				return $text;
			}
		}
		return false;
	}
	
	function concatUrlWithText1($text, $url) {
		$pattern = '/(https?:\/\/[^\s&]+)/';
		// $textWithoutUrls = preg_replace($pattern, '', $text);
		// $newText = $textWithoutUrls . ' ' . $url;
		$newText = preg_replace($pattern, $url, $text);

		$text = $newText . " ". $url;
		return $text;
	}

	function getYoutubeVideoIdS($url) {
		$pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
		preg_match($pattern, $url, $matches);
	
		if (isset($matches[1])) {
			return $matches[1];
		} else {
			return '';
		}
	}

	function replaceUrlsWithNewOne($text, $newUrl){
		$pattern = '/(https?:\/\/[^\s]+)/';
		// Replace all URLs in the text with the new URL
		$text = preg_replace($pattern, $newUrl, $text);
		return $text;
	}

	function removeUrls($text,$newUrl){
		$pattern 	= '/(https?:\/\/[^\s]+)/';
		// Remove all URLs from the text
		$text 		= preg_replace($pattern, '', $text);

		$onlyText	=	rtrim($text);
		return $onlyText." ".$newUrl;
	}

	function filterText($text){
		$pattern 	= '/(https?:\/\/[^\s]+)/';
		// Remove all URLs from the text
		$text 		= preg_replace($pattern, '', $text);

		return rtrim($text);
	}