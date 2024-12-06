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

function cl_get_timeline_feed($limit = false, $offset = false, $onset = false) {
	
	global $db, $cl, $me;

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
		'cat_id'    => isset($_GET['cat_id']) ? $_GET['cat_id'] : ''
 	));
//echo $sql;exit("Okay");
   
	$query_res = $db->rawQuery($sql);
	$counter   = 0;

	//  echo "<pre>";
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

    // echo json_encode($data);die;
	return $data;
}

function cl_timeline_swifts() {
	global $db, $cl, $me;

	if (empty($cl["is_logged"])) {
		return false;
	}

	$data         = array();
	$sql          = cl_sqltepmlate("apps/home/sql/fetch_timeline_swifts",array(
		"t_users" => T_USERS,
		"t_conns" => T_CONNECTIONS,
		"user_id" => $me['id']
 	));

	$query_res = $db->rawQuery($sql);

	if (cl_queryset($query_res)) {
		foreach ($query_res as $row) {
            $row['name']       = cl_strf("%s", $row['fname']);      
            $row['avatar']     = cl_get_media($row['avatar']);
            $row['url']        = cl_link($row['username']);
            $row['is_user']    = ($me["id"] == $row["id"]);
            $row['swift']      = json($row['swift']);
            $row['has_unseen'] = false;

			$pc = 0;

            if (is_array($row['swift'])) {
            	$swifts_ls = array();

            	foreach ($row['swift'] as $swift_id => $swift_data) {

					$pc = $pc + 1;

            		if ($swift_data["status"] == "active" && $swift_data["exp_time"] > time()) {
	            		if ($swift_data["type"] == "image") {
	            			$swift_data["media"]["src"] = cl_get_media($swift_data["media"]["src"]);
	            		}
	            		else if($swift_data["type"] == "video") {
	            			$swift_data["media"]["source"] = cl_get_media($swift_data["media"]["source"]);
	            		}

	            		$swift_data["time"] = date("H:i", $swift_data["time"]);
	            		$swift_data["text"] = stripcslashes($swift_data["text"]);
						$swift_data["text"] = htmlspecialchars_decode($swift_data["text"], ENT_QUOTES);
						$swift_data["text"] = cl_linkify_urls($swift_data["text"]);
						$swift_data["seen"] = (in_array($me['id'], array_keys($swift_data["views"]))) ? 1 : 0;
						$swift_data["swid"] = $swift_id;
						

						if (empty($row['is_user'])) {
							if (empty($row['has_unseen']) && $swift_data["seen"] == 0) {
								$row['has_unseen'] = true;
							}
						}
						else {
							if (not_empty($swift_data["views"])) {
								$db = $db->where("id", array_keys($swift_data["views"]), "IN");
								$qr = $db->get(T_USERS, null, array("id", "username", "fname", "avatar"));

								if (cl_queryset($qr)) {
									foreach ($qr as $uinfo) {
							            $uinfo['name']   = cl_strf("%s", $uinfo['fname']);      
							            $uinfo['avatar'] = cl_get_media($uinfo['avatar']);
							            $uinfo['url']    = cl_link($uinfo['username']);
							            $uinfo['time']   = cl_time2str($swift_data["views"][$uinfo["id"]]);
							            $swift_data["views"][$uinfo["id"]] = $uinfo;
							        }
								}
							}
						}

						array_push($swifts_ls, $swift_data);
					}

					




            	}

            	if (not_empty($swifts_ls)) {
            		$row['swift'] = $swifts_ls;
            		$data[]       = $row;
            	}
            }
		}
	}

	return $data;
}

