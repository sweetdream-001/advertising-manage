<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                           @
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                    @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

if (empty($cl["is_logged"])) {
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else if ($action == 'upload_profile_avatar') {
    if (not_empty($_FILES['avatar']) && not_empty($_FILES['avatar']['tmp_name'])) {
        $file_info      =  array(
            'file'      => $_FILES['avatar']['tmp_name'],
            'size'      => $_FILES['avatar']['size'],
            'name'      => $_FILES['avatar']['name'],
            'type'      => $_FILES['avatar']['type'],
            'file_type' => 'thumbnail',
            'folder'    => 'avatars',
            'slug'      => 'avatar',
            'crop'      => array('width' => 512, 'height' => 512),
            'allowed'   => 'jpg,png,jpeg,gif'
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['cropped'])) {
            cl_delete_media($file_upload['filename']);
            cl_delete_media($me['raw_avatar']);

            cl_update_user_data($me['id'], array(
                'avatar' => $file_upload['cropped']
            ));

            $data['status'] = 200;
            $data['url']    = cl_get_media($file_upload['cropped']);
        } 

        else{
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
        }
    }
}

else if ($action == 'save_profile_avatar') {

    $me["start_up"]["avatar"] = 1;
    $data['status']           = 200;
    $data['progstat']         = $me["start_up"];

    cl_update_user_data($me['id'], array(
        'start_up' => json($me["start_up"], true)
    ));
}

else if ($action == 'save_profile_info') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $country_list     = array_keys($cl["countries"]);
    $user_data_fields = array(
        'fname'       => fetch_or_get($_POST['fname'], null),
        'bio'         => fetch_or_get($_POST['bio'], null),
        'country'     => fetch_or_get($_POST['country'], null),
        'gender'      => fetch_or_get($_POST['gender'], null)
    );

    foreach ($user_data_fields as $field_name => $field_val) {
        if ($field_name == 'fname') {
            if (empty($field_val) || len_between($field_val,3, 25) != true) {
                $data['err_code'] = "invalid_fname"; break;
            }
        }

        else if($field_name == 'bio') {
            if (len($field_val) > 140) {
                $data['err_code'] = "invalid_bio"; break;
            }
        }

        else if($field_name == 'country') {
            if (not_num($field_val) || (in_array($field_val, $country_list) != true)) {
                $data['err_code'] = "invalid_country"; break;
            }
        }

        else if($field_name == 'gender') {
            if (not_empty($field_val) && in_array($field_val, array('M', 'F', 'O', 'T')) != true) {
                $data['err_code'] = "invalid_gender"; break;
            }
        }
    }

    if (empty($data['err_code'])) {
        $me["start_up"]["info"] = 1;
        $data['status']         = 200;
        $data['progstat']       = $me["start_up"];

        cl_update_user_data($me["id"], array(
            'fname'      => cl_text_secure($user_data_fields['fname']),
            'about'      => cl_text_secure($user_data_fields['bio']),
            'country_id' => cl_text_secure($user_data_fields['country']),
            'gender'     => cl_text_secure($user_data_fields['gender']),
            'start_up'   => json($me["start_up"], true)
        ));
    }
}

else if($action == 'finish_startup') {
    $data['err_code'] = 0;
    $data['status']   = 200;
    $follow_users     = fetch_or_get($_POST["flw"], false);

    cl_update_user_data($me["id"], array(
        'start_up' => 'done'
    ));

    if ($follow_users == "Y") {
        $suggestions = cl_get_follow_suggestions(20);

        if (not_empty($suggestions)) {
            foreach ($suggestions as $row) {
                $udata = cl_raw_user_data($row["id"]);

                if (not_empty($udata)) {
                    if ($udata["follow_privacy"] == "everyone") {

                        cl_follow($me["id"], $udata["id"]);
                        cl_follow_increase($me["id"], $udata["id"]);

                        cl_notify_user(array(
                            'subject'  => 'subscribe',
                            'user_id'  => $udata["id"],
                            'entry_id' => $me["id"]
                        ));
                    }

                    else {
                        cl_follow_request($me['id'], $udata["id"]);

                        cl_notify_user(array(
                            'subject'  => 'subscribe_request',
                            'user_id'  => $udata["id"],
                            'entry_id' => $me["id"]
                        ));
                    }
                }
            }
        }
    }
}