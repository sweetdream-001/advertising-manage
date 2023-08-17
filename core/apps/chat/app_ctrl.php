<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)							@
# @ Author_url 1: https://jooj.us                        @
# @ Author_url 2: http://jooj.us/twitter-clone                       @
# @ Author E-mail: sales@jooj.us                                    @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2022 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

function cl_get_chats($args = array()){
	global $db, $cl, $me;
	
	$args         = (is_array($args)) ? $args : array();
	$options      = array(
        "user_id" => 0,
        "offset"  => false,
        "limit"   => 1000
    );

    $args          = array_merge($options, $args);
    $user_id       = $args['user_id'];
    $offset        = $args['offset'];
    $limit         = $args['limit'];
	$sql           = cl_sqltepmlate('apps/chat/sql/fetch_chats',array(
		't_msgs'   => T_MSGS,
		't_chats'  => T_CHATS,
		't_users'  => T_USERS,
		't_blocks' => T_BLOCKS,
		'offset'   => $offset,
		'user_id'  => $user_id,
		'limit'    => $limit
	));

	$data  = array();
	$chats = $db->rawQuery($sql);

	if (cl_queryset($chats)) {
		foreach ($chats as $chat) {
			$chat['name']         = cl_rn_strip($chat['name']);
            $chat['name']         = stripslashes($chat['name']);
			$chat['avatar']       = cl_get_media($chat['avatar']);
			$chat['time']         = cl_time2str($chat['time']);
			$chat['chat_url']     = cl_link(cl_strf("conversation/%s", $chat['username']));
			$chat['last_message'] = cl_rn_strip($chat['last_message']);
			$chat['last_message'] = stripslashes($chat['last_message']);
			
			if (empty($chat['new_messages'])) {
				$chat['new_messages'] = '';
			}

			$data[] = $chat;
		}
	}
	
	return $data;
}

function cl_get_conversation($args = array()){
	global $db, $cl, $me;
	$args           = (is_array($args)) ? $args : array();
	$options        = array(
        "user_one"  => false,
        "user_two"  => false,
        "new"       => false,
        "order"     => "DESC",
        "offset"    => false,
        "offset_to" => false,
        "ids"       => false,
        "limit"     => 10
    );

    $args           = array_merge($options, $args);
    $user_one       = $args['user_one'];
    $user_two       = $args['user_two'];
    $new            = $args['new'];
    $offset         = $args['offset'];
    $limit          = $args['limit'];
    $order          = $args['order'];
    $ids            = $args['ids'];
    $offset_to      = $args['offset_to'];
    $sql            = cl_sqltepmlate('apps/chat/sql/fetch_conversation', array(
        'offset'    => $offset,
        'limit'     => $limit,
        'user_one'  => $user_one,
        'user_two'  => $user_two,
        'offset_to' => $offset_to,
        'new'       => $new,
        'ids'       => $ids,
        'order'     => $order,
        't_users'   => T_USERS,
        't_msgs'    => T_MSGS
	));

    $data     = array();
    $update   = array();
	$messages = $db->rawQuery($sql);

	if (cl_queryset($messages)) {
		foreach ($messages as $message) {
			$message['side']    = (($message['sent_by'] == $me['id']) ? 'right' : 'left');
			$message['owner']   = (($message['owner'] == $me['id']) ? true : false);
			$message['time']    = cl_time2str($message['time']);
			$message['msg_raw'] = $message['message'];
			$message['message'] = cl_linkify_urls($message['message']);
			$message['message'] = stripcslashes($message['message']);

			if (not_empty($message['media_file'])) {
				$message['media_file'] = cl_get_media($message['media_file']);
				
				if ($message['media_type'] == 'image') {
					$message['media_name'] = cl_strf("%s-IMG-%d", strtoupper(cl_slug($cl['config']['name'])),time());
				}
			}

			if (empty($message['owner']) && empty($message['seen'])) {
				$update[] = $message['id'];
			}

			$data[] = $message;
		}

		if (not_empty($update)) {
			$db = $db->where('id',$update,"IN");
			$up = $db->update(T_MSGS, array(
				'seen' => time()
			));
		}
	}
	
	return $data;
}

function cl_get_whole_conversation($args = array()){
	global $db,$cl,$me;

	$args          = (is_array($args)) ? $args : array();
	$options       = array(
        "user_one" => false,
        "user_two" => false,
        "order"    => "DESC"
    );

    $args          = array_merge($options, $args);
    $user_one      = $args['user_one'];
    $user_two      = $args['user_two'];
    $order         = $args['order'];
    $sql           = cl_sqltepmlate('apps/chat/sql/fetch_whole_conversation', array(
        'user_one' => $user_one,
        'user_two' => $user_two,
        'order'    => $order,
        't_users'  => T_USERS,
        't_msgs'   => T_MSGS
	));

    $data     = array();
	$messages = $db->rawQuery($sql);

	if (cl_queryset($messages)) {
		foreach ($messages as $message) {
			$message['side']      = (($message['sent_by'] == $me['id']) ? 'right' : 'left');
			$message['owner']     = (($message['owner'] == $me['id']) ? true : false);
			$message['time']      = date('d M, Y h:m',$message['time']);
			$message['media_raw'] = $message['media_file'];
			
			if (not_empty($message['media_file'])) {
				$message['media_file'] = cl_get_media($message['media_file']);
				
				if ($message['media_type'] == 'image') {
					$message['media_name'] = cl_strf("%s-IMG-%d",strtoupper(cl_slug($cl['config']['name'])),time());
				}
			}

			$data[] = $message;
		}
	}
	
	return $data;
}

function cl_create_conversations($user_one = null, $user_two = null) {
	global $db, $cl;
	if (not_num($user_one) || not_num($user_two)) {
		return false;
	}

	$time     = time();
	$t_chats  = T_CHATS;
	$db       = $db->where('user_one', $user_one);
	$db       = $db->where('user_two', $user_two);
	$convers1 = $db->getValue($t_chats,"COUNT(id)");

	if (empty($convers1)) {
		$db->insert($t_chats,array(
			'user_one' => $user_one,
			'user_two' => $user_two,
			'time'     => $time
		));

		$db       = $db->where('user_one',$user_two);
		$db       = $db->where('user_two',$user_one);
		$convers2 = $db->getValue($t_chats,"COUNT(id)");
		if (empty($convers2)) {
			$db->insert($t_chats,array(
				'user_two' => $user_one,
				'user_one' => $user_two,
				'time'     => $time
			));
		}
		else {
			$db = $db->where('user_one',$user_two);
			$db = $db->where('user_two',$user_one);
			$bl = $db->update($t_chats,array('time' => $time));
		}
	}
	else{
		$db       = $db->where('user_one',$user_one);
		$db       = $db->where('user_two',$user_two);
		$bl       = $db->update($t_chats,array('time' => $time));
		$db       = $db->where('user_one',$user_two);
		$db       = $db->where('user_two',$user_one);
		$convers2 = $db->getValue($t_chats,"COUNT(id)");
		if (empty($convers2)) {
			$db->insert($t_chats,array(
				'user_two' => $user_one,
				'user_one' => $user_two,
				'time'     => $time
			));
		}
		else{
			$db = $db->where('user_one',$user_two);
			$db = $db->where('user_two',$user_one);
			$bl = $db->update($t_chats,array('time' => $time));
		}
	}
}

function cl_send_message($data = null) {
	global $db, $cl;
	
	if (empty($data)) {
		return false;
	}

	$msg_id   = $db->insert(T_MSGS, $data);
	$sent_by  = $data['sent_by'];
	$sent_to  = $data['sent_to'];

	if (is_posnum($msg_id)) {
		cl_create_conversations($sent_by, $sent_to);
	}

	return $msg_id;
}

function cl_can_direct_message($user_id = false) {
	global $db, $cl;

	if (not_num($user_id)) {
		return false;	
	}

	else {
		$udata = cl_raw_user_data($user_id);
		$myid  = $cl['me']['id'];

		if(not_empty($udata)) {
			if ($myid == $user_id) {
				return false;
			}

			else if($udata['contact_privacy'] == 'everyone' && $cl['me']['contact_privacy'] == 'everyone') {
				return true;
			}

			else if($udata['contact_privacy'] == 'followed' && $cl['me']['contact_privacy'] == 'everyone' && cl_is_following($user_id, $myid)) {
				return true;
			}

			else if($cl['me']['contact_privacy'] == 'followed' && $udata['contact_privacy'] == 'everyone' && cl_is_following($myid, $user_id)) {
				return true;
			}

			else if(($cl['me']['contact_privacy'] == 'followed' && cl_is_following($myid, $user_id)) && ($udata['contact_privacy'] == 'followed' && cl_is_following($user_id, $myid))) {
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
}
