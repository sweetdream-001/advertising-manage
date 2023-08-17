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

if (empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 401,
		'data'    => array(),
		'message' => 'Unauthorized Access'
	);
}
else {
	$post_id   = fetch_or_get($_POST['post_id'], 0);
	$post_data = cl_raw_post_data($post_id);

	if (empty($post_data) || ($post_data["user_id"] != $me["id"] && empty($cl["is_admin"]))) {
		$data['code']    = 400;
        $data['message'] = "Post id is missing or invalid";
        $data['data']    = array();
	}
    else {
        $post_owner = cl_raw_user_data($post_data['user_id']);

        if ($post_data['target'] == 'publication') {

            if (not_empty($post_owner)) {
                cl_update_user_data($post_data['user_id'], array(
                    'posts' => ($post_owner['posts'] -= 1)
                ));
            }

            cl_db_delete_item(T_POSTS, array(
                'publication_id' => $post_id
            ));
        }

        else {
            cl_update_thread_replys($post_data['thread_id'], 'minus');
        }

        $data["code"]    = 200;
        $data["message"] = "Post deleted successfully";
        $data["data"]    = array();

        cl_recursive_delete_post($post_id);
    }
}