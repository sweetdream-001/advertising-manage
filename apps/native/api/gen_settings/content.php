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
	$valid_error      = false;
	$user_data_fields = array(
		'fname'       => fetch_or_get($_POST['first_name'], null),
	
		'about'       => fetch_or_get($_POST['about'], null),
		'email'       => fetch_or_get($_POST['email'], null),
		'gender'      => fetch_or_get($_POST['gender'], null),
		'website'     => fetch_or_get($_POST['website'], null),
		'country_id'  => fetch_or_get($_POST['country_id'], null),
        'uname'       => fetch_or_get($_POST['username'], null)
	);

	foreach ($user_data_fields as $field_name => $field_val) {
		if ($field_name == 'fname') {
			if (empty($field_val) || len_between($field_val, 3, 25) != true) {
	            $valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "Invlid user first name";
	            $data['code']    = 400; break;
	        }
		}

	

		else if ($field_name == 'about') {
			if (len($field_val) > 140) {
	            $valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "The text you entered is too long, the maximum length is 140 characters";
	            $data['code']    = 400; break;
	        }
		}

		else if($field_name == 'email') {
			if (empty($field_val) || (filter_var($field_val, FILTER_VALIDATE_EMAIL) != true || len($field_val) > 55)) {
				$valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "The email address you entered does not match the valid format.";
	            $data['code']    = 400; break;
			}

			else if(cl_email_exists($field_val) && ($field_val != $me['email'])) {
				$valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "This email address is already taken";
	            $data['code']    = 400; break;
			}
		}

		else if ($field_name == 'gender') {
			if (empty($field_val) || in_array($field_val, array('M', 'F', 'O', 'T')) != true) {
	            $valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "The user gender you selected is not valid";
	            $data['code']    = 400; break;
	        }
		}

		else if($field_name == 'website') {
        	if (not_empty($field_val) && (is_url($field_val) != true || len($field_val) > 115)) {
        		$valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "Website URL address you entered is not valid";
	            $data['code']    = 400; break;
        	}
        }

        else if($field_name == 'country_id') {
        	if (not_num($field_val) || (in_array($field_val, array_keys($cl["countries"])) != true)) {
        		$valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "Country your selected is not valid";
	            $data['code']    = 400; break;
        	}
        }

        else if ($field_name == 'uname') {
            if (empty($field_val)) {
               	$valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "Username is missing or inavlid";
	            $data['code']    = 400; break;
            }

            else if (len_between($field_val,3, 25) != true) {
                $valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "Username is missing or inavlid";
	            $data['code']    = 400; break;
            }

            else if (preg_match('/^[\w]+$/', $field_val) != true) {
                $valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "This username does not match the valid format. Please select a username of no more than 25 characters using only letters (a-z) numbers and underscores";
	            $data['code']    = 400; break;
            }

            else if(cl_uname_exists($field_val) && $field_val != $me['raw_uname']) {
                $valid_error     = true;
	            $data['data']    = array();
	            $data['message'] = "This username is already taken, please select another";
	            $data['code']    = 400; break;
            }
        }
	}

	if (empty($valid_error)) {
        cl_update_user_data($me["id"], array(
            'fname'      => cl_text_secure($user_data_fields['fname']),
         
            'username'   => cl_text_secure($user_data_fields['uname']),
            'email'      => cl_text_secure($user_data_fields['email']),
            'about'      => cl_text_secure($user_data_fields['about']),
            'gender'     => cl_text_secure($user_data_fields['gender']),
            'website'    => cl_text_secure($user_data_fields['website']),
            'country_id' => cl_text_secure($user_data_fields['country_id'])
        ));

        if ($user_data_fields['uname'] != $me['raw_uname']) {
            cl_update_user_data($me["id"], array(
                'verified' => '0'
            ));
        }

        $me               = cl_user_data($me["id"]);
        $data["code"]     = 200;
        $data["message"]  = "Profile data updated successfully";
        $data["data"]     = array(
        	'id'          => $me['id'],
        	'first_name'  => $me['fname'],
        	
        	'user_name'   => $me['raw_uname'],
        	'email'       => $me['email'],
        	'is_verified' => (($me['verified'] == '1') ? true : false),
        	'website'     => $me['website'],
        	'about_you'   => $me['about'],
        	'gender'      => $me['gender'],
        	'country'     => $me['country_name'],
        	'post_count'  => $me['posts'],
        	'ip_address'  => $me['ip_address'],
        	'following_count' => $me['following'],
        	'follower_count'  => $me['followers'],
        	'language'        => $me['language'],
        	'last_active'     => $me['last_active'],
        	'member_since'    => $me['joined']
        );
    }
}