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

function cl_get_profile_posts($user_id = false, $limit = 30, $media = false, $offset = false) {
	global $db, $cl, $me;

	if (not_num($user_id)) {
		return false;
	}

	$data         = array();
	$sql          = cl_sqltepmlate("apps/profile/sql/fetch_profile_posts",array(
		"t_posts" => T_POSTS,
		"t_pubs"  => T_PUBS,
		"media"   => $media,
		"limit"   => $limit,
		"offset"  => $offset,
		"user_id" => $user_id
 	));

	$query_res = $db->rawQuery($sql);
	$counter   = 0;

	if (cl_queryset($query_res)) {
		foreach ($query_res as $row) {
			$post_data = cl_raw_post_data($row['publication_id']);
			if (not_empty($post_data) && in_array($post_data['status'], array('active'))) {
				$post_data['offset_id']   = $row['offset_id'];
				$post_data['is_repost']   = (($row['type'] == 'repost') ? true : false);
				$post_data['is_reposter'] = false;
				$post_data['cat_id'] = $row['cat_id'];
				$post_data['cat_name'] = $row['cat_name'];
				$post_data['attrs']       = array();

				if ($post_data['is_repost']) {
					$post_data['attrs'][]  = cl_html_attrs(array('data-repost' => $row['offset_id']));
					$reposter_data         = cl_user_data($row['user_id']);
					$post_data['reposter'] = array(
						'name' => $reposter_data['name'],
						'username' => $reposter_data['username'],
						'url' => $reposter_data['url'],
					);
				}

				if (not_empty($cl['is_logged'])) {
					if ($row['user_id'] == $me['id']) {
						$post_data['is_reposter'] = true;
					}
				}

				$post_data['attrs'] = ((not_empty($post_data['attrs'])) ? implode(' ', $post_data['attrs']) : '');
				$data[]             = cl_post_data($post_data);
			}

			if ($cl['config']['advertising_system'] == 'on') {
				if (cl_is_feed_nad_allowed()) {
					if (not_empty($offset)) {
						if ($counter == 5) {
							$counter = 0;
							$ad      = cl_get_timeline_ads();

							if (not_empty($ad)) {
								$data[] = $ad;
							}
						}
						else {
							$counter += 1;
						}
					}
				}
			}
		}

		if ($cl['config']['advertising_system'] == 'on') {
			if (cl_is_feed_nad_allowed()) {
				if (empty($offset)) {
					$ad = cl_get_timeline_ads();

					if (not_empty($ad)) {
						$data[] = $ad;
					}
				}
			}
		}
	}

	return $data;
}

function cl_get_profile_likes($user_id = false, $limit = 30, $offset = false) {
	global $db, $cl, $me;

	if (not_num($user_id)) {
		return false;
	}

	$data      =  array();
	$db        = $db->where('user_id', $user_id);
	$db        = ((is_posnum($offset)) ? $db->where('id', $offset, '<') : $db);
	$db        = $db->orderBy('id', 'DESC');
	$query_res = $db->get(T_LIKES, $limit);

	if (cl_queryset($query_res)) {
		foreach ($query_res as $row) {
			$post_data = cl_raw_post_data($row['pub_id']);
			if (not_empty($post_data) && in_array($post_data['status'], array('active'))) {
				$post_data['offset_id'] = $row['id'];
				$data[]                 = cl_post_data($post_data);
			}
		}
	}

	return $data;
}

function cl_can_view_profile($user_id = false) {
	global $db, $cl;

	if (not_num($user_id)) {
		return false;
	}

	$udata = cl_raw_user_data($user_id);
	$myid  = ((empty($cl['is_logged'])) ? 0 : $cl['me']['id']);

	if (not_empty($udata)) {
		if (not_empty($myid) && $myid == $user_id) {
			return true;
		}

		else if (not_empty($myid) && $myid != $user_id && $udata['profile_privacy'] == 'nobody') {
			return false;
		}

		else if($udata['profile_privacy'] == 'everyone') {
			return true;
		}

		else if(not_empty($myid) && $udata['profile_privacy'] == 'followers' && cl_is_following($myid, $user_id)) {
			return true;
		}

		else {
			return false;
		}
	}
	else {
		return false;
	}
}