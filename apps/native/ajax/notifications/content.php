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
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}
else {
    
    if ($action == 'load_more') {
    	$data['err_code'] = 0;
        $data['status']   = 400;
        $offset           = fetch_or_get($_GET['offset'], 0);
        $type             = fetch_or_get($_GET['type'], false);
        $notifs_list      = array();
        $html_arr         = array();

        if (is_posnum($offset) && in_array($type, array('notifs', 'mentions'))) {

            require_once(cl_full_path("core/apps/notifications/app_ctrl.php"));

        	$notifs_list =  cl_get_notifications(array(
                "type"   => $type,
                "offset" => $offset,
                "limit"  => 50,
            ));

        	if (not_empty($notifs_list)) {
    			foreach ($notifs_list as $cl['li']) {
    				$html_arr[] = cl_template('notifications/includes/list_item');
    			}

    			$data['status'] = 200;
    			$data['html']   = implode("", $html_arr);
    		}
        }
    }

    else if($action == 'delete') {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $notif_id         = fetch_or_get($_POST['id'], false);
        $ids              = array();

        if (is_posnum($notif_id)) {

            cl_db_delete_item(T_NOTIFS, array(
                "id" => $notif_id
            ));
            
            $data['status'] = 200;
        }
    }

    else if($action == 'delete_all') {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $type             = fetch_or_get($_POST['type'], array());

        if (not_empty($type) && in_array($type, array("notifs", "mentions"))) {

            if ($type == "notifs") {
                $db = $db->where('recipient_id', $me['id']);
                $db = $db->where('subject', "mention", "!=");
                $rq = $db->delete(T_NOTIFS);
            }
            else {
                $db = $db->where('recipient_id', $me['id']);
                $db = $db->where('subject', "mention");
                $rq = $db->delete(T_NOTIFS);
            }

            $data['status'] = 200;
        }
    }
}