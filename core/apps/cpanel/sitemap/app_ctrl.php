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

function cl_admin_get_publication_indexes() {
	global $db;

	$db    = $db->where('target', 'publication');
	$db    = $db->where('status', 'active');
	$db    = $db->orderBy('likes_count','DESC');
	$db    = $db->orderBy('replys_count','DESC');
	$db    = $db->orderBy('reposts_count','DESC');
	$posts = $db->get(T_PUBS, null, array('id'));
	$data  = array();

	if (cl_queryset($posts)) {
		foreach ($posts as $row) {
			$data[] = cl_link(cl_strf("thread/%d", $row['id']));
		}
	}

	return $data;
}

function cl_admin_get_user_indexes() {
	global $db;

	$db    = $db->where('index_privacy', 'Y');
	$db    = $db->where('active', '1');
	$db    = $db->orderBy('followers','DESC');
	$db    = $db->orderBy('posts','DESC');
	$users = $db->get(T_USERS, null, array('username'));
	$data  = array();

	if (cl_queryset($users)) {
		foreach ($users as $row) {
			$data[] = cl_link($row['username']);
		}
	}

	return $data;
}



	