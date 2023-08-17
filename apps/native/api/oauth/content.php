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

if (not_empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 400,
		'message' => 'You are already logged in'
	);
}
else {
	$user_ip_addr     = cl_get_ip();
	$user_ip_addr     = ((filter_var($user_ip_addr, FILTER_VALIDATE_IP) == true) ? $user_ip_addr : '0.0.0.0');
	$api_access_token = fetch_or_get($_POST['access_token'], null);
	$api_type         = fetch_or_get($_POST['type'], null);
	$device_type      = fetch_or_get($_POST['device_type'], null);
	$user_data        = array(
		"username"    => "",
		"name"        => "",
		"email"       => "",
		"fname"       => "",
	
		"avatar"      => "",
		"status"      => false
	);

	if (empty($device_type) || in_array($device_type, array("ios", "android")) != true) {
		$device_type = "android";
	}

	if (empty($api_type) || in_array($api_type, array("facebook", "google", "twitter")) != true) {
		$data['code']    = 400;
        $data['message'] = "oAuth provider type is missing or invalid";
        $data['data']    = array();
	}

	else {
		if ($api_type == "facebook") {
			$api_cal = cl_curl_httpreq(cl_strf("https://graph.facebook.com/me?fields=email,id,name,age_range&access_token=%s", $api_access_token));
		
			if (is_array($api_cal)) {

				if (isset($api_cal["error"])) {
					$data['code']    = 402;
		            $data['message'] = "Access token is invalid or missing";
		            $data['data']    = array();
		            $data['error']   = $api_cal["error"];
				}
				else {
					$user_data["email"]    = fetch_or_get($api_cal["email"]);
					$user_data["name"]     = fetch_or_get($api_cal["name"]);
					$user_data["status"]   = true;
					$user_data["username"] = uniqid("fb_");

					if(empty($user_data["email"])) {
						$user_data["email"] =  cl_strf("%s@facebook.com", strtolower(preg_replace('/\s+/', '_', $user_data["username"])));
					}

					$fl_names = explode(" ", $user_data["name"]);

					if (count($fl_names) == 2) {
						$user_data["fname"] = $fl_names[0];
					
					}

					else {
						$user_data["fname"] = $user_data["name"];
					}
				}
			}

			else {
				$data['code']    = 500;
	            $data['message'] = "API Error – API unavailable";
	            $data['data']    = array();
			}
		}

		else if($api_type == "google") {

			$api_cal = cl_curl_httpreq(cl_strf("https://www.googleapis.com/oauth2/v1/userinfo?access_token=%s", $api_access_token));

			if (is_array($api_cal)) {

				if (isset($api_cal["error"])) {
					$data['code']    = 402;
		            $data['message'] = "Access token is invalid or missing";
		            $data['data']    = array();
		            $data['error']   = $api_cal;
				}
				else {
					$user_data["email"]    = fetch_or_get($api_cal["email"]);
					$user_data["avatar"]   = fetch_or_get($api_cal["picture"]);
					$user_data["name"]     = fetch_or_get($api_cal["name"]);
					$user_data["status"]   = true;
					$user_data["username"] = uniqid("go_");

					if(empty($user_data["email"])) {
						$user_data["email"] =  cl_strf("%s@google.com", strtolower(preg_replace('/\s+/', '_', $user_data["username"])));
					}

					$fl_names = explode(" ", $user_data["name"]);

					if (count($fl_names) == 2) {
						$user_data["fname"] = $fl_names[0];
					
					}

					else {
						$user_data["fname"] = $user_data["name"];
					}
				}
			}

			else {
				$data['code']    = 500;
	            $data['message'] = "API Error – API unavailable";
	            $data['data']    = array();
			}
		}

		if (not_empty($user_data["status"])) {
			
			if (cl_email_exists($user_data["email"]) != true) {
				$email_code       = sha1(time() + rand(111,999));
		        $password_hashed  = password_hash(time(), PASSWORD_DEFAULT);
		        $user_id          = cl_db_insert(T_USERS, array(
		            'fname'       => cl_text_secure($user_data["fname"]),
		           
		            'username'    => $user_data["username"],
		            'password'    => $password_hashed,
		            'email'       => $user_data["email"],
		            'active'      => '1',
		            'em_code'     => $email_code,
		            'last_active' => time(),
		            'joined'      => time(),
	                'start_up'    => json(array('source' => 'oauth', 'avatar' => 0, 'info' => 0, 'follow' => 0), true),
		            'ip_address'  => $user_ip_addr,
		            'language'    => $cl['config']['language'],
	                'country_id'  => $cl['config']['country_id']
		        ));
			}



	    	$raw_user = cl_db_get_item(T_USERS, array('email' => $user_data["email"]));

	    	cl_db_delete_item(T_SESSIONS, array(
	    		"user_id" => $raw_user["id"],
	    		"platform" => "mobile_android"
	    	));

	    	cl_db_delete_item(T_SESSIONS, array(
	    		"user_id" => $raw_user["id"],
	    		"platform" => "mobile_ios"
	    	));

	    	$data_exp      = strtotime("+1 year");
	        $session_id    = cl_create_user_session($raw_user["id"], cl_strf("mobile_%s", $device_type));
	        $refresh_token = md5(rand(11111, 99999)) . time() . md5(microtime() . $raw_user["id"]);

	        if (is_url($user_data["avatar"])) {
            	$avatar         = cl_import_image(array(
            		'url'       => $user_data["avatar"],
            		'file_type' => 'thumbnail',
		            'folder'    => 'avatars',
		            'slug'      => 'avatar'
            	));

            	if ($avatar) {
            		cl_update_user_data($raw_user["id"], array(
            			'avatar' => $avatar
            		));
            	}
            }

	        cl_update_user_data($raw_user["id"], array(
	        	'ip_address'    => $user_ip_addr,
	        	'last_active'   => time(),
	            'refresh_token' => $refresh_token
	        ));

	        $data['code']    = 200;
	        $data['message'] = "User logged in successfully";
	        $data['data']    = array(
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