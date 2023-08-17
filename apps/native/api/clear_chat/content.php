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
	$user_id     = fetch_or_get($_POST["user_id"], false);
    $delete_chat = fetch_or_get($_POST["delete_chat"], false);
    $user_data   = cl_raw_user_data($user_id);

    if (empty($user_data) || $user_id == $me["id"]) {
        $data['code']    = 400;
        $data['message'] = "Interlocutor ID is missing or invalid";
        $data['data']    = array();
    }
    else {
        require_once(cl_full_path("core/apps/chat/app_ctrl.php"));

        $chat_messages = cl_get_whole_conversation(array(
            'user_one' => $me['id'],
            'user_two' => $user_id
        ));

        if (not_empty($delete_chat) && $delete_chat == "1") {
            cl_db_delete_item(T_CHATS, array(
                'user_one' => $me['id'],
                'user_two' => $user_id
            ));
        }

        if (not_empty($chat_messages)) {
            foreach ($chat_messages as $message_data) {
                if ($message_data['sent_by'] == $me['id']) {
                    if ($message_data['deleted_fs2'] == 'Y') {

                        cl_db_delete_item(T_MSGS, array(
                            'id' => $message_data['id']
                        ));

                        if (not_empty($message_data['media_raw'])) {
                            cl_delete_media($message_data['media_raw']);
                        }
                    }
                    else {
                        $db = $db->where('id', $message_data['id']);
                        $qr = $db->update(T_MSGS, array(
                            'deleted_fs1' => 'Y'
                        ));
                    }
                }

                else {
                    if ($message_data['deleted_fs1'] == 'Y') {
                        cl_db_delete_item(T_MSGS, array(
                            'id' => $message_data['id']
                        ));

                        if (not_empty($message_data['media_raw'])) {
                            cl_delete_media($message_data['media_raw']);
                        }
                    }
                    else{
                        $db = $db->where('id', $message_data['id']);
                        $qr = $db->update(T_MSGS, array(
                            'deleted_fs2' => 'Y'
                        ));
                    }
                } 
            }
        }

        $data["code"]    = 200;
        $data["message"] = "Chat deleted successfully";
        $data["data"]    = array();
    }
}