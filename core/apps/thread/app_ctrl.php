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


function cl_get_timeline_feed_for_thread($catId) {
	global $db, $cl, $me;

    $limit = false;
    $offset = false;
    $onset = false;
	
	if (empty($cl["is_logged"])) {
		return false;
	}

	$data           = array();
	$sql            = cl_sqltepmlate("apps/home/sql/fetch_timeline_feed",array(
		"t_posts"   => T_POSTS,
		"t_pubs"    => T_PUBS,
		"t_conns"   => T_CONNECTIONS,
		"t_reports" => T_PUB_REPORTS,
		"limit"     => $limit,
		"offset"    => $offset,
		"onset"     => $onset,
		"user_id"   => $me['id'],
		'cat_id'    => $catId,
		'thread' => true
 	));
    // echo $sql;exit("Okay");
   
	$query_res = $db->rawQuery($sql);
	$counter   = 0;

// 	 echo "<pre>";
// 	 print_r($query_res);exit;
	//  print_r(cl_get_timeline_ads());exit;
	


	if (cl_queryset($query_res)) {

		$pc = 0;

		foreach ($query_res as $row) {
			$post_data = cl_raw_post_data($row['publication_id']);

			$counter = $counter + 1;
			$pc = $pc + 1;

			if (not_empty($post_data) && in_array($post_data['status'], array('active'))) {
				$post_data['offset_id']   = $row['offset_id'];
				$post_data['is_repost']   = (($row['type'] == 'repost') ? true : false);
				$post_data['is_reposter'] = false;
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

				if ($row['user_id'] == $me['id']) {
					$post_data['is_reposter'] = true;
				}

				$post_data['attrs'] = ((not_empty($post_data['attrs'])) ? implode(' ', $post_data['attrs']) : '');
				$data[]             = cl_post_data($post_data);
			}

			// echo $cl['config']['advertising_system'];
			// echo "<br>";
			// echo cl_is_feed_nad_allowed();
			// echo "<br>";
			// echo $offset;
			// exit;


			if ($cl['config']['advertising_system'] == 'on') {
				if (cl_is_feed_nad_allowed()) {
					//if (not_empty($offset)) {
						if ($counter == 12) {							
							$ad      = cl_get_timeline_ads();
							// echo "<pre>";
							// print_r($data);
							// print_r($ad);
							//exit;
							if (not_empty($ad)) {
								$data[] = $ad;
							}
							$counter = 0;
						}
						
					//}
				}
			}

			// if($pc == 13){
			// 	echo "<pre>";print_r($data);
			// 	break;
			// 	exit("Here");

			// }


		}

		if ($cl['config']['advertising_system'] == 'on') {
			if (cl_is_feed_nad_allowed()) {
				//if (empty($offset)) {
					$ad = cl_get_timeline_ads();

					if (not_empty($ad)) {
						$data[] = $ad;
					}
				//}
			}
		}
	}

	// echo "<pre>";
	// print_r($data);
	// exit;
    foreach($data as $key => $datum){
        $data[$key]['cat_id'] = isset($datum['category_id']) ? $datum['category_id'] : 0;
        $cat = $db->query('SELECT * FROM `cl_categories` where id = '.$data[$key]['cat_id']);
        
        $data[$key]['cat_name'] = $cat[0]['name'] ?? "";
    }
    // echo json_encode($data);die;
        // Shuffle the data array in random order
    shuffle($data);

	return $data;
}

function cl_get_thread_data($post_id = false) {
	global $db, $cl;

	if (not_num($post_id)) {
		return false;
	}

	$post           = cl_raw_post_data($post_id);
	$data           = array(
		'post'      => array(),
		'next'      => array(),
		'can_reply' => true,
		'can_see'   => true
	);

	if (cl_queryset($post) && $post["status"] != "orphan") {
		$data['can_reply'] = cl_can_reply($post);
		$data['post']      = cl_post_data($post);
		$data['next']      = cl_get_thread_child_posts($post_id, 30);
	}

	return $data;
}

function cl_get_thread_parent_posts($post_obj = array()) {
    global $db, $cl;

    if (not_empty($post_obj['thread_id'])) {
        $db = $db->where('id', $post_obj['thread_id']);
        $db = $db->where('status', array('active','inactive','deleted'),'IN');
        $qr = $db->getOne(T_PUBS);

        if (cl_queryset($qr)) {
        	$cl['_'][] = cl_post_data($qr);

        	return cl_get_thread_parent_posts($qr);
        }
    }

    else {
    	return ((not_empty($cl['_'])) ? $cl['_'] : false);
    }
}

function cl_get_thread_child_posts($post_id = false, $limit = null, $offset = false, $offset_to = 'lt') {
    global $db, $cl;

    if (not_num($post_id)) {
    	return false;
    }

    $offset_to    = (($offset_to == 'gt') ? '>' : '<');
    $data         = array();
	$db           = $db->where('thread_id', $post_id);
	$db           = $db->where('status', array('active','inactive','deleted'),'IN');
	$db           = ((is_posnum($offset)) ? $db->where('id', $offset, $offset_to) : $db);
	$db           = $db->orderBy('id','DESC');
	$child_posts  = $db->get(T_PUBS, $limit);

	if (cl_queryset($child_posts)) {
		foreach ($child_posts as $row) {
			$row['replys'] = array();
			$db            = $db->where('thread_id', $row['id']);
			$db            = $db->where('status', array('active','inactive','deleted'),'IN');
			$db            = $db->orderBy('id','DESC');
			$replys        = $db->get(T_PUBS, 2);

			if (cl_queryset($replys)) {
				foreach ($replys as $reply) {
					$row['replys'][] = cl_post_data($reply);
				}
			}

			$data[] = cl_post_data($row);
		}
	}

	return $data;
}
