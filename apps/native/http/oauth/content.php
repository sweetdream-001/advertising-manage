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

// RESTRICT USER CODE
$visitor_ip = $_SERVER['REMOTE_ADDR'];
cl_check_ip_restriction($visitor_ip);

$provider_ls = array(
    'google' => 'Google',
    'facebook' => 'Facebook',
    'twitter' => 'Twitter',
    'discord' => 'Discord',
    'linkedin' => 'LinkedIn',
    'instagram' => 'Instagram',
    'vkontakte' => 'Vkontakte'
);

$provider      = false;
$provider_name = fetch_or_get($_GET['provider'], false);
// echo in_array($provider_name, array_keys($provider_ls));


if (not_empty($provider_name)) {
    $provider_name = strtolower($provider_name);

    if (in_array($provider_name, array_keys($provider_ls))) {
        $provider = $provider_ls[$provider_name];
    }
    else{
        cl_redirect("404");
    }
}

if (strtolower($provider_name) == "facebook" && $cl["config"]["facebook_oauth"] != "on") {
    cl_redirect("404");
}

else if (strtolower($provider_name) == "google" && $cl["config"]["google_oauth"] != "on") {
    cl_redirect("404");
}

else if (strtolower($provider_name) == "twitter" && $cl["config"]["twitter_oauth"] != "on") {
    cl_redirect("404");
}

else if (strtolower($provider_name) == "linkedin" && $cl["config"]["linkedin_oauth"] != "on") {
    cl_redirect("404");
}

else if (strtolower($provider_name) == "discord" && $cl["config"]["discord_status"] != "on") {
    cl_redirect("404");
}

else if (strtolower($provider_name) == "vkontakte" && $cl["config"]["vkontakte_status"] != "on") {
    cl_redirect("404");
}

else if (strtolower($provider_name) == "instagram" && $cl["config"]["instagram_status"] != "on") {
    cl_redirect("404");
}

else {
    require_once(cl_full_path("core/libs/oAuth/vendor/autoload.php"));
    require_once(cl_full_path("core/libs/oAuth/oauth_config.php"));

    
    if ($provider) {
        try {
            $hybridauth    = new Hybridauth\Hybridauth($oauth_config);
            $auth_provider = $hybridauth->authenticate($provider);
            $tokens        = $auth_provider->getAccessToken();
            $user_profile  = $auth_provider->getUserProfile();

            if ($user_profile && isset($user_profile->identifier)) {
                
                
                $fname      = fetch_or_get($user_profile->firstName, time());
                $prov_email = "mail.com";
                $prov_prefx = "xx_";
                //pre($user_profile,1);

    // echo var_dump($user_profile->identifier);die;
                if ($provider_name == 'google') {
                    $prov_email = 'google.com';
                    $prov_prefx = 'go_';
                } 

                else if ($provider_name == 'facebook') {
                    $prov_email = 'facebook.com';
                    $prov_prefx = 'fa_';
                } 

                else if ($provider_name == 'twitter') {
                    $prov_email = 'twitter.com';
                    $prov_prefx = 'tw_';
                }

                else if ($provider_name == 'linkedin') {
                    $prov_email = 'linkedin.com';
                    $prov_prefx = 'li_';
                }

                else if ($provider_name == 'discord') {
                    $prov_email = 'discord.com';
                    $prov_prefx = 'dc_';
                }

                else if ($provider_name == 'vkontakte') {
                    $prov_email = 'vkontakte.com';
                    $prov_prefx = 'vk_';
                }

                else if ($provider_name == 'instagram') {
                    $prov_email = 'instagram.com';
                    $prov_prefx = 'ig_';
                }

                $user_name  = uniqid($prov_prefx);
                $user_email = cl_strf('%s@%s', $user_name, $prov_email);

                if (not_empty($user_profile->email)) {
                    $user_email = $user_profile->email;
                }

                if (cl_email_exists($user_email)) {
                	$db        = $db->where('email', $user_email);
                	$user_data = $db->getOne(T_USERS);

                    cl_create_user_session($user_data['id'], 'web');
                    cl_redirect('/');
                } 

                else {
                	$about            = fetch_or_get($user_profile->description, "");
                	$email_code       = sha1(time() + rand(111,999));
    		        $password_hashed  = password_hash(time(), PASSWORD_DEFAULT);
    		        $user_ip          = cl_get_ip();
    		        $user_ip          = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
    		        $user_id          = $db->insert(T_USERS, array(
    		            'fname'       => cl_text_secure($fname),
    		            'username'    => $user_name,
    		            'password'    => $password_hashed,
    		            'email'       => $user_email,
    		            'active'      => '1',
    		            'about'       => cl_croptxt($about, 130),
    		            'em_code'     => $email_code,
    		            'last_active' => time(),
    		            'joined'      => time(),
                        'logged_in_with' => $provider_name,
                        'start_up'    => json(array('source' => 'oauth', 'avatar' => 0, 'info' => 0, 'follow' => 0), true),
    		            'ip_address'  => $user_ip,
    		            'language'    => $cl['config']['language'],
                        'country_id'  => $cl['config']['country_id'],
                        'display_settings' => json(array("color_scheme" => $cl["config"]["default_color_scheme"], "background" => $cl["config"]["default_bg_color"]), true)
    		        ));

    		        if (is_posnum($user_id)) {

    		        	cl_create_user_session($user_id, 'web');

    		            $avatar = fetch_or_get($user_profile->photoURL, null);

    	                if (is_url($avatar)) {
    	                	$avatar = cl_import_image(array(
    	                		'url' => $avatar,
    	                		'file_type' => 'thumbnail',
    				            'folder' => 'avatars',
    				            'slug' => 'avatar'
    	                	));

    	                	if ($avatar) {
    	                		cl_update_user_data($user_id, array('avatar' => $avatar));
    	                	}
    	                }

                        if ($cl['config']['affiliates_system'] == 'on') {

                            $ref_id = cl_session('ref_id');

                            if (is_posnum($ref_id)) {
                                $ref_udata = cl_raw_user_data($ref_id);

                                if (not_empty($ref_udata)) {
                                    cl_update_user_data($ref_id, array(
                                        'aff_bonuses' => ($ref_udata['aff_bonuses'] += 1)
                                    ));

                                    cl_session_unset('ref_id');
                                }
                            }
                        }

    		            cl_redirect('start_up');
    		        }
                }
            }
        }
        catch (Exception $e) {
            exit($e->getMessage());
        }
    } 

    else {
        cl_redirect("/");
    }
}