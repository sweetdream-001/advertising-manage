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

if (empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 401,
		'data'    => array(),
		'message' => 'Unauthorized Access'
	);
}
else {
	require_once(cl_full_path("core/apps/chat/app_ctrl.php"));

	$send_to   = fetch_or_get($_POST["user_id"], false);
	$type      = fetch_or_get($_POST["type"], false);
	$user_data = cl_raw_user_data($send_to);

	if (empty($user_data)) {
		$data['code']    = 400;
        $data['message'] = "Interlocutor ID is missing or invalid";
    	$data['data']    = array();
	}

	else if(cl_can_direct_message($send_to) != true) {
		$data['code']    = 400;
        $data['message'] = "You do not have permission direct messages to this chat";
    	$data['data']    = array();
	}

	else if(cl_is_blocked($send_to, $me['id']) || cl_is_blocked($me['id'], $send_to)) {
		$data['code']    = 400;
        $data['message'] = "Your account has been blocked by this account";
    	$data['data']    = array();
	}

	else {
		if ($type == "text") {
			if (not_empty($_POST['message']) && len_between($_POST['message'], 1, 3000)) {
				$insert_data  = array(
					'sent_by' => $me['id'],
					'sent_to' => $send_to,
					'owner'   => $me['id'],
					'message' => cl_text_secure($_POST['message']),
					'seen'    => 0,
					'time'    => time()
				);

				$message_id     = cl_send_message($insert_data);
				$message_data   = cl_get_conversation(array(
					'user_one'  => $me['id'],
					'user_two'  => $send_to,
					'limit'     => 100,
					'offset'    => false,
					'ids'       => array($message_id),
					'order'     => 'DESC',
					'offset_to' => 'gt'
				));

				if (not_empty($message_data) && is_array($message_data)) {
					$data['code']    = 200;
					$data['message'] = "Message sent";
					$data['data']    = fetch_or_get($message_data[0]);

					cl_push_notify_user(array(
                        'type'         => 'chat_message',
                        'notifier_id'  => $me['id'],
                        'recipient_id' => $send_to,
                        'entry_id'     => $message_id,
                        'chat_message' => array(
                        	'message_type' => 'text',
                        	'data' => cl_text_secure($_POST['message']),
                        	'message_id' => $message_id,
                        	'avatar' => $me['avatar'],
                        	'user_id' => $me['id'],
                        	'name' => $me['name']
                        )
                    ));
				}
				else {
					$data['code']    = 500;
			        $data['message'] = "An error occurred while processing your request. Please try again later.";
			    	$data['data']    = array();
				}
			}
			else {
				$data['code']    = 500;
		        $data['message'] = "An error occurred while processing your request. Please try again later.";
		    	$data['data']    = array();
			}
		}

		else if($type == "media") {
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

					$message_id     = cl_send_message($insert_data);
					$message_data   = cl_get_conversation(array(
						'user_one'  => $me['id'],
						'user_two'  => $send_to,
						'limit'     => 100,
						'offset'    => false,
						'ids'       => array($message_id),
						'order'     => 'DESC',
						'offset_to' => 'gt'
					));
					
					if (not_empty($message_data) && is_array($message_data)) {
						$data['code']    = 200;
						$data['message'] = "Message sent";
						$data['data']    = fetch_or_get($message_data[0]);

						cl_push_notify_user(array(
	                        'type'         => 'chat_message',
	                        'notifier_id'  => $me['id'],
	                        'recipient_id' => $send_to,
	                        'entry_id'     => $message_id,
	                        'chat_message' => array(
	                        	'message_type' => 'url',
	                        	'data' => cl_get_media($filename),
	                        	'message_id' => $message_id,
	                        	'avatar' => $me['avatar'],
	                        	'user_id' => $me['id'],
	                        	'name' => $me['name']
	                        )
	                    ));
					}
					else {
						$data['code']    = 500;
				        $data['message'] = "An error occurred while processing your request. Please try again later.";
				    	$data['data']    = array();
					}
	            }
	            else {
	            	$data['code']    = 500;
			        $data['message'] = "An error occurred while processing your request. Please try again later.";
			    	$data['data']    = array();
	            }
			}

			else {
				$data['code']    = 500;
		        $data['message'] = "An error occurred while processing your request. Please try again later.";
		    	$data['data']    = array();
			}
		}

		else {
			$data['code']    = 400;
	        $data['message'] = "The media type of the message is invalid or missing";
	    	$data['data']    = array();
		}
	}
}