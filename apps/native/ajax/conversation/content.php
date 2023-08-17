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

	require_once(cl_full_path("core/apps/chat/app_ctrl.php"));

	if ($action == 'send_message') {
		$data['status']   = 400;
		$data['err_code'] = 0;
		$send_to          = cl_session('interloc_user_id');

		if (is_posnum($send_to) && cl_is_blocked($send_to, $me['id']) != true && cl_is_blocked($me['id'], $send_to) != true) {
			if (cl_can_direct_message($send_to)) {
				if (not_empty($_FILES['image']) && not_empty($_FILES['image']['tmp_name'])) {	
		            $file_info      = array(
		                'file'      => $_FILES['image']['tmp_name'],
		                'size'      => $_FILES['image']['size'],
		                'name'      => $_FILES['image']['name'],
		                'type'      => $_FILES['image']['type'],
		                'file_type' => 'image',
		                'folder'    => 'images',
		                'slug'      => 'original',
		                'allowed'   => 'jpg,png,jpeg,gif'
		            );

		            $file_upload = cl_upload($file_info);

		            if (not_empty($file_upload['filename'])) {
		                $filename        = $file_upload['filename'];               
		                $insert_data     = array(
							'sent_by'    => $me['id'],
							'sent_to'    => $send_to,
							'owner'      => $me['id'],
							'message'    => 'Image',
							'media_file' => $filename,
							'media_type' => 'image',
							'seen'       => 0,
							'time'       => time()
						);

						$message = cl_send_message($insert_data);

						if (not_empty($message)) {
							$data['status'] = 200;

							cl_push_notify_user(array(
		                        'type'         => 'chat_message',
		                        'notifier_id'  => $me['id'],
		                        'recipient_id' => $send_to,
		                        'entry_id'     => $message,
		                        'chat_message' => array(
		                        	'message_type' => 'url',
		                        	'data' => cl_get_media($filename),
		                        	'message_id' => $message,
		                        	'avatar' => $me['avatar'],
		                        	'user_id' => $me['id'],
		                        	'name' => $me['name']
		                        )
		                    ));
						}
		            } 
				}

				else if (not_empty($_POST['message']) && len_between($_POST['message'], 1, 3000)) {
					$insert_data  = array(
						'sent_by' => $me['id'],
						'sent_to' => $send_to,
						'owner'   => $me['id'],
						'message' => cl_text_secure($_POST['message']),
						'seen'    => 0,
						'time'    => time()
					);

					$message = cl_send_message($insert_data);

					if (not_empty($message)) {
						$data['status'] = 200;

						cl_push_notify_user(array(
	                        'type'         => 'chat_message',
	                        'notifier_id'  => $me['id'],
	                        'recipient_id' => $send_to,
	                        'entry_id'     => $message,
	                        'chat_message' => array(
	                        	'message_type' => 'text',
	                        	'data' => cl_text_secure($_POST['message']),
	                        	'message_id' => $message,
	                        	'avatar' => $me['avatar'],
	                        	'user_id' => $me['id'],
	                        	'name' => $me['name']
	                        )
	                    ));
					}
				}
			}
		}
	}

	else if($action == 'get_new_messages') {
		$data['status']   = 400;
		$data['err_code'] = 0;
		$send_to          = cl_session('interloc_user_id');

		if (is_posnum($send_to) && cl_is_blocked($send_to, $me['id']) != true && cl_is_blocked($me['id'], $send_to) != true) {
			$html           = array();
			$offset         = ((is_posnum($_GET['offset'])) ? intval($_GET['offset']) : false);
			$messages       = cl_get_conversation(array(
				'user_one'  => $me['id'],
				'user_two'  => $send_to,
				'limit'     => 100,
				'offset'    => $offset,
				'order'     => "DESC",
				'offset_to' => 'gt'
			));

			if (not_empty($messages)) {

				$messages = array_reverse($messages);

				foreach ($messages as $cl['li']) {
					array_push($html, cl_template('conversation/includes/list_item'));
				}

				$data['status'] = 200;
				$data['html']   = implode('', $html);
				
			}

			$user_ls     = cl_db_get_item(T_USERS, array(
		        "id"     => $send_to,
		        "active" => "1"
		    ), array("last_active"));

		    $data['lastseen'] = array(
		    	"time_now" => time(),
		    	"time_num" => $user_ls["last_active"],
		    	"time_str" => cl_time2str($user_ls["last_active"])
		    );
		}
	}

	else if($action == 'get_old_messages') {
		$data['status']   = 400;
		$data['err_code'] = 0;
		$send_to          = cl_session('interloc_user_id');

		if (is_posnum($send_to) && cl_is_blocked($send_to, $me['id']) != true && cl_is_blocked($me['id'], $send_to) != true) {
			$html           = array();
			$offset         = ((is_posnum($_GET['offset'])) ? intval($_GET['offset']) : false);
			$messages       = cl_get_conversation(array(
				'user_one'  => $me['id'],
				'user_two'  => $send_to,
				'limit'     => 10,
				'offset'    => $offset,
				'order'     => "DESC",
				'offset_to' => 'lt'
			));

			if (not_empty($messages)) {
				foreach ($messages as $cl['li']) {
					array_push($html, cl_template('conversation/includes/list_item'));
				}

				$data['status'] = 200;
				$data['html']   = implode('', $html);
			}
		}
	}

	else if ($action == 'delete_message') {
		$data['status']   = 400;
		$data['err_code'] = 0;
		$message_id       = fetch_or_get($_POST['id']);

		if (is_posnum($message_id)) {
			$db           = $db->where('id', $message_id);
			$message_data = $db->getOne(T_MSGS);

			if (not_empty($message_data) && ($message_data['sent_by'] == $me['id'] || $message_data['sent_to'] == $me['id'])) {
				if ($message_data['sent_by'] == $me['id']) {
					if ($message_data['deleted_fs2'] == 'Y') {
						$db             = $db->where('id', $message_id);
						$q1             = $db->delete(T_MSGS);
						$data['status'] = 200;

						if (not_empty($message_data['media_file'])) {
							cl_delete_media($message_data['media_file']);
						}
					}
					else {
						$db             = $db->where('id',$message_id);
						$q1             = $db->update(T_MSGS, array('deleted_fs1' => 'Y'));
						$data['status'] = 200;
					}
				}

				else{
					if ($message_data['deleted_fs1'] == 'Y') {
						$db             = $db->where('id', $message_id);
						$q1             = $db->delete(T_MSGS);
						$data['status'] = 200;

						if (not_empty($message_data['media_file'])) {
							cl_delete_media($message_data['media_file']);
						}
					}
					else{
						$db             = $db->where('id',$message_id);
						$q2             = $db->update(T_MSGS, array('deleted_fs2' => 'Y'));
						$data['status'] = 200;
					}
				} 
			}
		}
	}

	else if($action == 'delete_chat') {
		$data['status']   = 400;
		$data['err_code'] = 0;
		$send_to          = cl_session('interloc_user_id');

		if (not_empty($send_to)) {

			$data['status'] = 200;
			$db             = $db->where('user_one', $me['id']);
			$db             = $db->where('user_two', $send_to);
			$rm             = $db->delete(T_CHATS);
			$messages       = cl_get_whole_conversation(array(
				'user_one'  => $me['id'],
				'user_two'  => $send_to
			));

			if (not_empty($messages)) {
				foreach ($messages as $message_data) {
					if ($message_data['sent_by'] == $me['id']) {
						if ($message_data['deleted_fs2'] == 'Y') {
							$db             = $db->where('id', $message_data['id']);
							$q1             = $db->delete(T_MSGS);
							$data['status'] = 200;

							if (not_empty($message_data['media_raw'])) {
								cl_delete_media($message_data['media_raw']);
							}
						}
						else {
							$db             = $db->where('id',$message_data['id']);
							$q1             = $db->update(T_MSGS, array('deleted_fs1' => 'Y'));
							$data['status'] = 200;
						}
					}

					else {
						if ($message_data['deleted_fs1'] == 'Y') {
							$db             = $db->where('id', $message_data['id']);
							$q1             = $db->delete(T_MSGS);
							$data['status'] = 200;

							if (not_empty($message_data['media_raw'])) {
								cl_delete_media($message_data['media_raw']);
							}
						}
						else{
							$db             = $db->where('id',$message_data['id']);
							$q2             = $db->update(T_MSGS, array('deleted_fs2' => 'Y'));
							$data['status'] = 200;
						}
					} 
				}
			}
		}
	}

	else if($action == 'clear_chat') {
		$data['status']   = 400;
		$data['err_code'] = 0;
		$send_to          = cl_session('interloc_user_id');

		if (not_empty($send_to)) {

			$data['status'] = 200;
			$messages       = cl_get_whole_conversation(array(
				'user_one'  => $me['id'],
				'user_two'  => $send_to
			));

			if (not_empty($messages)) {
				foreach ($messages as $message_data) {
					if ($message_data['sent_by'] == $me['id']) {
						if ($message_data['deleted_fs2'] == 'Y') {
							$db             = $db->where('id', $message_data['id']);
							$q1             = $db->delete(T_MSGS);
							$data['status'] = 200;

							if (not_empty($message_data['media_raw'])) {
								cl_delete_media($message_data['media_raw']);
							}
						}
						else {
							$db             = $db->where('id',$message_data['id']);
							$q1             = $db->update(T_MSGS, array('deleted_fs1' => 'Y'));
							$data['status'] = 200;
						}
					}

					else {
						if ($message_data['deleted_fs1'] == 'Y') {
							$db             = $db->where('id', $message_data['id']);
							$q1             = $db->delete(T_MSGS);
							$data['status'] = 200;

							if (not_empty($message_data['media_raw'])) {
								cl_delete_media($message_data['media_raw']);
							}
						}
						else{
							$db             = $db->where('id',$message_data['id']);
							$q2             = $db->update(T_MSGS, array('deleted_fs2' => 'Y'));
							$data['status'] = 200;
						}
					} 
				}
			}
		}
	}
}
