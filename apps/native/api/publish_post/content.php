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
    $max_post_length = $cl["config"]["max_post_len"];
	$post_data       = $me['draft_post'];
    $post_text       = fetch_or_get($_POST['post_text'], "");
    $gif_src         = fetch_or_get($_POST['gif_src'], "");
    $og_data         = fetch_or_get($_POST['og_data'], array());
    $thread_id       = fetch_or_get($_POST['thread_id'], 0);
    $post_text       = cl_croptxt($post_text, $max_post_length);
    $thread_data     = array();
    $post_privacy    = fetch_or_get($_POST['privacy'], "everyone");
    $poll_data       = fetch_or_get($_POST['poll_data'], array());
    $poll_data       = cl_decode_array($poll_data);

    if (not_empty($thread_id)) {
        $thread_data  = cl_raw_post_data($thread_id);
        $post_privacy = "everyone";
    }
    else {
        if (in_array($post_privacy, array("everyone", "followers", "mentioned")) != true) {
            $post_privacy = "everyone";
        }
    }

    if (not_empty($post_data) && not_empty($post_data["media"])) {
        
        $thread_id      = ((is_posnum($thread_id)) ? $thread_id : 0);
        $post_id        = $post_data['id'];
        $post_text      = cl_upsert_htags($post_text);
        $mentions       = cl_get_user_mentions($post_text);
        $qr             = cl_update_post_data($post_id, array(
            "text"      => cl_text_secure($post_text),
            "status"    => "active",
            "thread_id" => $thread_id,
            "time"      => time(),
            "priv_wcs"  => $me["profile_privacy"],
            "priv_wcr"  => $post_privacy
        ));

        if (empty($thread_id)) {
            cl_db_insert(T_POSTS, array(
                "user_id"        => $me['id'],
                "publication_id" => $post_id,
                "time"           => time()
            ));

            $data['posts_total'] = ($me['posts'] += 1);
            
            cl_update_user_data($me['id'], array(
                'posts' => $data['posts_total']
            ));
        }

        else {
            $data['replys_total'] = cl_update_thread_replys($thread_id, 'plus');

            cl_update_post_data($post_id, array(
                "target" => "pub_reply"
            ));

            if ($thread_data['user_id'] != $me['id']) {
                cl_notify_user(array(
                    'subject'  => 'reply',
                    'user_id'  => $thread_data['user_id'],
                    'entry_id' => $post_id
                ));
            }
        }

        if (not_empty($mentions)) {
            cl_notify_mentioned_users($mentions, $post_id);
        }

        $post_data       = cl_raw_post_data($post_id);
        $data['data']    = cl_post_data($post_data);
        $data['code']    = 200;
        $data['message'] = "Post published successfully";

        cl_delete_post_junk_files($post_data['id'], $post_data['type']);
    }

    else {
        if (not_empty($post_text) || not_empty($gif_src)) {
            $thread_id      = ((is_posnum($thread_id)) ? $thread_id : 0);
            $post_text      = cl_upsert_htags($post_text);
            $mentions       = cl_get_user_mentions($post_text);
            $insert_data    = array(
                "user_id"   => $me['id'],
                "text"      => cl_text_secure($post_text),
                "status"    => "active",
                "type"      => "text",
                "thread_id" => $thread_id,
                "time"      => time(),
                "priv_wcs"  => $me["profile_privacy"],
                "priv_wcr"  => $post_privacy
            );

            if(not_empty($post_text) && empty($poll_data) != true && cl_is_valid_poll($poll_data)) {
                $insert_data['og_data']   = "";
                $gif_src                  = "";
                $insert_data['type']      = "poll";
                $insert_data['poll_data'] = array_map(function($option) {
                    return array(
                        "option" => cl_text_secure($option["value"]),
                        "voters" => array(),
                        "votes"  => 0
                    );
                }, $poll_data);

                $insert_data['poll_data'] = json($insert_data['poll_data'], true);
            }

            else if (not_empty($gif_src) && is_url($gif_src)) {
                $insert_data['og_data'] = "";
                $insert_data['type']    = "gif";
            }

            else if(not_empty($og_data) && is_array($og_data)) {
                $insert_data['og_data'] = json($og_data, true);
                $gif_src                = "";
            }

            $post_id = cl_db_insert(T_PUBS, $insert_data);

            if (is_posnum($post_id)) {
                if (empty($thread_id)) {
                    cl_db_insert(T_POSTS, array(
                        "user_id" => $me['id'],
                        "publication_id" => $post_id,
                        "time" => time()
                    ));


                    $data['posts_total'] = ($me['posts'] += 1);

                    cl_update_user_data($me['id'], array(
                        'posts' => $data['posts_total']
                    ));
                }

                else {
                    $data['replys_total'] = cl_update_thread_replys($thread_id,'plus');

                    cl_update_post_data($post_id, array(
                        "target" => "pub_reply"
                    ));

                    if ($thread_data['user_id'] != $me['id']) {
                        cl_notify_user(array(
                            'subject'  => 'reply',
                            'user_id'  => $thread_data['user_id'],
                            'entry_id' => $post_id
                        ));
                    }
                }

                if (not_empty($gif_src) && is_url($gif_src)) {
                    cl_db_insert(T_PUBMEDIA, array(
                        "pub_id" => $post_id,
                        "type"   => "gif",
                        "src"    => $gif_src,
                        "time"   => time(),
                    ));
                }

                $post_data       = cl_raw_post_data($post_id);
		        $data['data']    = cl_post_data($post_data);
		        $data['code']    = 200;
		        $data['message'] = "Post published successfully";

                if (not_empty($mentions)) {
                    cl_notify_mentioned_users($mentions, $post_id);
                }
            }
        }
        else {
        	$data['code']     = 400;
	        $data['err_code'] = "invalid_post_data";
	        $data['message']  = "Invalid data for publication. Please check your details";
	    	$data['data']     = array();
        }
    }

    cl_delete_orphan_posts($me['id']);
    cl_update_user_data($me['id'], array(
        'last_post' => 0
    ));
}