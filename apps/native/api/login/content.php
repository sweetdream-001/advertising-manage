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

if (not_empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 400,
		'message' => 'You are already logged in'
	);
}
else {

    $valid_error      = false;
	$user_data_fileds = array(
		'email'       => fetch_or_get($_POST['email'], null),
		'password'    => fetch_or_get($_POST['password'], null),
        'device_type' => fetch_or_get($_POST['device_type'], "android"),
	);

	foreach ($user_data_fileds as $field_name => $field_val) {
		if ($field_name == 'email') {
			if (empty($field_val) || len($field_val) > 55) {
	            $valid_error     = false;
	            $data['code']    = 402;
	        	$data['message'] = "Incorrect credentials";
	        	$data['data']    = array(); break;
	        }
		}

		else if ($field_name == 'password') {
			if (empty($field_val) || len($field_val) > 20) {
	            $valid_error     = false;
	            $data['code']    = 402;
	        	$data['message'] = "Incorrect credentials";
	        	$data['data']    = array(); break;
	        }
		}

        else if ($field_name == 'device_type') {
            if (empty($field_val) || in_array($field_val, array("ios", "android")) != true) {
                $valid_error     = false;
                $data['code']    = 402;
                $data['message'] = "Incorrect device type";
                $data['data']    = array(); break;
            }
        }
	}

	if (empty($valid_error)) {
        $email    = cl_text_secure($user_data_fileds['email']);
        $password = cl_text_secure($user_data_fileds['password']);
        $db       = $db->where("active", array("1", "2"), "IN");
        $db       = $db->where("email", $email);
        $raw_user = $db->getOne(T_USERS);

        if (cl_queryset($raw_user) != true || (password_verify($password, $raw_user["password"]) != true)) {
        	$data['code']    = 402;
        	$data['message'] = "Incorrect credentials";
        	$data['data']    = array();
        } 

        else if ($raw_user["active"] != "1") {
        	$data['code']    = 402;
        	$data['message'] = "Account disabled or inactive";
        	$data['data']    = array();
        } 

        else {
        	$user_ip       = cl_get_ip();
        	$data_exp      = strtotime("+1 year");
        	$user_ip       = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
	        $session_id    = cl_create_user_session($raw_user["id"], cl_strf("mobile_%s", $user_data_fileds["device_type"]));
            $data['code']  = 200;
            $refresh_token = md5(rand(11111, 99999)) . time() . md5(microtime() . $raw_user["id"]);

            cl_update_user_data($raw_user["id"], array(
            	'ip_address'    => $user_ip,
            	'last_active'   => time(),
                'refresh_token' => $refresh_token
            ));

            $data['code']             = 200;
            $data['message']          = "User logged in successfully";
            $data['data']             = array(
                    'user'            => array(
                    'user_id'         => $raw_user['id'],
                    'first_name'      => $raw_user['fname'],
                  
                    'user_name'       => $raw_user['username'],
                    'profile_picture' => cl_get_media($raw_user['avatar']),
                    'cover_picture'   => cl_get_media($raw_user['cover']),
                    'email'           => $raw_user['email'],
                    'is_verified'     => (($raw_user['verified'] == '1') ? true : false),
                    'website'         => $raw_user['website'],
                    'about_you'       => $raw_user['about'],
                    'gender'          => $raw_user['gender'],
                    'country'         => $cl['countries'][$raw_user['country_id']],
                    'post_count'      => $raw_user['posts'],
                    'last_post'       => $raw_user['last_post'],
                    'last_ad'         => $raw_user['last_ad'],
                    'language'        => $raw_user['language'],
                    'following_count' => $raw_user['following'],
                    'follower_count'  => $raw_user['followers'],
                    'wallet'          => $raw_user['wallet'],
                    'ip_address'      => $raw_user['ip_address'],
                    'last_active'     => $raw_user['last_active'],
                    'member_since'    => date("M Y", $raw_user['joined']),
                    'profile_privacy' => $raw_user['profile_privacy']
                )
            );

            $data["auth"]           = array(
            	"auth_token"        => $session_id,
                "refresh_token"     => $refresh_token,
            	"auth_token_expiry" => $data_exp
            );
        }
    }
}

