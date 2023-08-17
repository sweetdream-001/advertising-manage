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

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

if ($action == 'load_more') {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $prof_id          = fetch_or_get($_GET['prof_id'], 0);
    $type             = fetch_or_get($_GET['type'], false);
    $users_list       = array();
    $html_arr         = array();

    if (is_posnum($prof_id) && is_posnum($offset) && in_array($type, array('followers','following', 'follow_requests'))) {
        if (cl_can_view_profile($prof_id)) {
        	if ($type == 'followers') {
        		$users_list = cl_get_followers($prof_id, 30, $offset);	
        	}

            else if($type = 'follow_requests' && not_empty($cl["is_logged"]) && $prof_id == $me["id"]) {
                $users_list = cl_get_follow_requests(30, $offset);
            }

        	else if ($type == 'following') {
        		$users_list = cl_get_followings($prof_id, 30, $offset);
        	}


        	if (not_empty($users_list)) {
    			foreach ($users_list as $cl['li']) {
    				$html_arr[] = cl_template('connections/includes/list_item');
    			}

    			$data['status'] = 200;
    			$data['html']   = implode("", $html_arr);
    		}
        }
    }
}

else if($action == 'accept_request' && not_empty($cl["is_logged"])) {
    $data['status']   = 404;
    $data['err_code'] = 0;
    $req_id           = fetch_or_get($_POST['req_id'], 0);

    if (is_posnum($req_id)) {
        $req_data = cl_db_get_item(T_CONNECTIONS, array("id" => $req_id));

        if (not_empty($req_data)) {
            $udata = cl_raw_user_data($req_data["follower_id"]);

            if (not_empty($udata)) {
                cl_update_user_data($req_data["follower_id"], array(
                    'following' => ($udata['following'] += 1)
                ));

                cl_update_user_data($me['id'], array(
                    'followers' => ($me['followers'] += 1)
                ));

                cl_notify_user(array(
                    'subject'  => 'subscribe_accept',
                    'user_id'  => $req_data["follower_id"],
                    'entry_id' => $me["id"]
                ));

                cl_db_update(T_CONNECTIONS, array(
                    "id" => $req_id
                ), array(
                    "status" => "active"
                ));

                $data['status'] = 200;
                $data['total']  = cl_get_follow_requests_total();
            }
        }
    }
}

else if($action == 'delete_request' && not_empty($cl["is_logged"])) {
    $data['status']   = 404;
    $data['err_code'] = 0;
    $req_id           = fetch_or_get($_POST['req_id'], 0);

    if (is_posnum($req_id)) {
        if (cl_db_delete_item(T_CONNECTIONS, array("id" => $req_id))) {
            $data['status'] = 200;
            $data['total']  = cl_get_follow_requests_total();
        }
    }
}