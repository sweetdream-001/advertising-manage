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
        'fname'       => fetch_or_get($_POST['first_name'],null),
       
        'uname'       => fetch_or_get($_POST['username'], null),
        'email'       => fetch_or_get($_POST['email'], null),
        'password'    => fetch_or_get($_POST['password'], null)
    );

    foreach ($user_data_fileds as $field_name => $field_val) {
        if ($field_name == 'fname') {
            if (empty($field_val) || len_between($field_val, 3, 25) != true) {
                $valid_error     = true;
                $data['code']    = 410;
                $data['message'] = "Invalid user first name"; break;
            }
        }

     

        else if ($field_name == 'uname') {
            if (empty($field_val) || len_between($field_val, 3, 25) != true || preg_match('/^[\w]+$/', $field_val) != true) {
                $valid_error     = true;
                $data['code']    = 410;
                $data['message'] = "Invalid username"; break;
            }

            else if(cl_uname_exists($field_val)) {
                $valid_error     = true;
                $data['code']    = 410;
                $data['message'] = "This username is already taken"; break;
            }
        }

        else if ($field_name == 'email') {
            if (empty($field_val) || (filter_var(trim($field_val), FILTER_VALIDATE_EMAIL) == false) || len($field_val) > 55) {
                $valid_error     = true;
                $data['code']    = 410;
                $data['message'] = "Invalid email address"; break;
            }

            else if (cl_email_exists($field_val)) {
                $valid_error     = true;
                $data['code']    = 409;
                $data['message'] = "Email ID already registered"; break;
            }
        }

        else if ($field_name == 'password') {
            if (empty($field_val) || len_between($field_val, 6, 20) != true) {
                $valid_error     = true;
                $data['code']    = 410;
                $data['message'] = "Invalid password"; break;
            }
        }
    }

    if (empty($valid_error)) {
        $email_code       = sha1(time() + rand(111,999));
        $password_hashed  = password_hash($user_data_fileds["password"], PASSWORD_DEFAULT);
        $user_ip          = cl_get_ip();
        $user_ip          = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
        $insert_data      = array(
            'fname'       => cl_text_secure($user_data_fileds["fname"]),
          
            'username'    => cl_text_secure($user_data_fileds["uname"]),
            'password'    => $password_hashed,
            'email'       => cl_text_secure($user_data_fileds["email"]),
            'active'      => '1',
            'em_code'     => $email_code,
            'last_active' => time(),
            'joined'      => time(),
            'ip_address'  => $user_ip,
            'language'    => $cl['config']['language'],
        ); $user_id       = $db->insert(T_USERS, $insert_data);

        if (is_posnum($user_id)) {
            $data['code']    = 200;
            $data['message'] = "User logged in successfully";
            $data['data']    = array();
        }
        else {
        	$data['data']    = array();
            $data['code']    = 500;
            $data['message'] = "An error occurred while processing your request. Please try again later";
        }
    }
}