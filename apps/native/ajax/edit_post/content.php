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

if (empty($cl["is_logged"])) {
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else if ($action == 'get_data') {
	$data['status']   = 400;
    $data['err_code'] = 0;
    $post_id          = fetch_or_get($_GET["id"], false);
	$post_id          = cl_text_secure($post_id);
	$cl['post_data']  = cl_raw_post_data($post_id);

	if (not_empty($cl['post_data']) && $cl['post_data']["user_id"] == $me["id"] && empty($cl['post_data']["edited"])) {
		$cl['post_data']["htags"] = cl_listify_htags($cl['post_data']['text']);
	    $cl['post_data']["text"]  = cl_tagify_htags($cl['post_data']['text'], $cl['post_data']["htags"]);
	    

	    $data['status'] = 200;
        $data['html']   = cl_template('timeline/modals/edit');
	}
}

else if ($action == 'save_data') {
	$data['status']   = 400;
    $data['err_code'] = 0;
    $post_id          = fetch_or_get($_POST["id"], false);
	$post_id          = cl_text_secure($post_id);
	$post_data        = cl_raw_post_data($post_id);
	$curr_pn          = fetch_or_get($_POST['curr_pn'], "none");
	$max_post_length  = $cl["config"]["max_post_len"];


	if (not_empty($post_data) && $post_data["user_id"] == $me["id"] && empty($post_data["edited"])) {
		$post_text = fetch_or_get($_POST['post_text'], "");
		$post_text = cl_croptxt($post_text, $max_post_length);
		$post_text = cl_upsert_htags($post_text);
		$mentions  = cl_get_user_mentions($post_text);

		cl_update_post_data($post_id, array(
            "text"   => cl_text_secure($post_text),
            "edited" => time()
        ));

        if (not_empty($mentions)) {
            cl_notify_mentioned_users($mentions, $post_id);
        }

        $data['status'] = 200;

        if ($curr_pn !== "thread") {
            $cl['li']     = cl_post_data(cl_raw_post_data($post_id));
            $data['html'] = cl_template('timeline/post');
        }
	}
}
