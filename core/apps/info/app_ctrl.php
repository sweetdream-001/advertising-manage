<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)							@
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                   @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2021 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

function cl_get_user_account_data($user_id = false, $args = array()) {
	global $db, $cl;

	if (empty($args) || is_array($args) != true) {
		return false;
	}

	else if(is_posnum($user_id) != true || empty($cl["is_logged"])) {
		return false;
	}

	else {
		$data      = array();
		$user_data = ($cl["me"]["id"] == $user_id) ? $cl["me"] : cl_user_data($user_id);


		if (not_empty($user_data)) {
			$data["user"] = $user_data;
		
			if (not_empty($args["user_info"]) && $args["user_info"] == "Y") {
				$data["user_info"]              = true;
				$data["user"]["wallet_history"] = array();

				$db = $db->where('user_id', $user_id);
				$db = $db->orderBy('id', 'DESC');
				$qr = $db->get(T_WALLET_HISTORY);

				if (cl_queryset($qr)) {
					foreach ($qr as $row) {
						$row['time']      = cl_time2str($row['time']);
						$row['json_data'] = json($row['json_data']);

						array_push($data["user"]["wallet_history"], $row);
					}
				}
			}

			if (not_empty($args["following"]) && $args["following"] == "Y") {
				$data["following"] = cl_get_followings($user_id);
			}

			if (not_empty($args["followers"]) && $args["followers"] == "Y") {
				$data["followers"] = cl_get_followers($user_id);
			}
			
			if (not_empty($args["posts"]) && $args["posts"] == "Y") {
				$db = $db->where('user_id', $user_id);
				$db = $db->where('status', 'orphan', "!=");
				$db = $db->orderBy('id', 'DESC');
				$qr = $db->get(T_PUBS);

				if (cl_queryset($qr)) {

					$data["posts"] = array();

					foreach ($qr as $row) {
						$row = cl_get_user_post_data($row, $user_data);

						if (not_empty($row)) {
							array_push($data["posts"], $row);
						}
					}
				}
			}

			if (not_empty($args["bookmarks"]) && $args["bookmarks"] == "Y") {
				$db = $db->where('user_id', $user_id);
				$db = $db->orderBy('id', 'DESC');
				$qr = $db->get(T_BOOKMARKS, null, array("publication_id"));

				if (cl_queryset($qr)) {

					$data["bookmarks"] = array();

					foreach ($qr as $row) {
						$bookmark_post_data = cl_raw_post_data($row["publication_id"]);

						if (not_empty($bookmark_post_data)) {

							$bookmark_post_data = cl_get_user_post_data($bookmark_post_data, $user_data);

							if (not_empty($row)) {
								array_push($data["bookmarks"], $bookmark_post_data);
							}
						}
					}
				}
			}
		}
		
		return $data;
	}
}

function cl_get_user_post_data($post = array(), $user_data = array()) {
	global $cl;

	if (empty($post) || empty($user_data)) {
		return false;
	}

	else {
		$post["time_raw"]      = $post["time"];
		$post['og_text']       = cl_text($post['text']);
		$post['og_image']      = $cl['config']['site_logo'];
		$post["time"]          = cl_time2str($post["time"]);
		$post['text']          = stripcslashes($post['text']);
		$post['text']          = htmlspecialchars_decode($post['text'], ENT_QUOTES);
		$post["text"]          = cl_linkify_urls($post['text']);
		$post["text"]          = cl_tagify_htags($post['text']);
		$post["text"]          = cl_linkify_htags($post['text']);
		$post["text"]          = cl_likify_mentions($post['text']);
		$post["url"]           = cl_link(cl_strf("thread/%d",$post['id']));
		$post["replys_count"]  = cl_number($post["replys_count"]);
		$post["reposts_count"] = cl_number($post["reposts_count"]);
		$post["likes_count"]   = cl_number($post["likes_count"]);
		$post["media"]         = array();
		$post["reply_to"]      = array();
		$post["owner"]         = array(
			'id'              => $user_data['id'],
			'url'             => $user_data['url'],
			'avatar'          => $user_data['avatar'],
			'username'        => $user_data['username'],
			'name'            => $user_data['name'],
			'verified'        => $user_data['verified']
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
			}
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
						$row["reply_to"]["is_owner"] = true;
					}
				}
			}
		}

		return $post;
	}
}