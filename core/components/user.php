<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                                    @
# @ Author_url 1: https://jooj.us                                           @
# @ Author_url 2: http://jooj.us/twitter-clone                              @
# @ Author E-mail: sales@jooj.us                                            @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

function cl_get_ip() {
    if (not_empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    
    if (not_empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP)){
                    return $ip;
                }
            }
        } 

        else {
            if (filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
    }

    if (not_empty($_SERVER['HTTP_X_FORWARDED']) && filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_X_FORWARDED'];
    }
        
    if (not_empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && filter_var($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    }
        
    if (not_empty($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_FORWARDED_FOR'];
    }
        
    if (not_empty($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_FORWARDED'];
    }
        
    return $_SERVER['REMOTE_ADDR'];
}

function cl_create_user_session($user_id = 0, $platform = 'web') {
    global $db;
    if (empty($user_id)) {
        return false;
    }

    $data_exp        = strtotime("+1 year");
    $session_id      = sha1(rand(11111, 99999)) . time() . md5(microtime() . $user_id);
    $insert_data     = array(
        'user_id'    => $user_id,
        'session_id' => $session_id,
        'platform'   => $platform,
        'time'       => time(),
        'lifespan'   => $data_exp
    );

    
    $insert = $db->insert(T_SESSIONS, $insert_data);
    
    
    if ($platform == "web") {
        setcookie("user_id", $session_id, $data_exp, '/') or die('unable to create cookie');
    }

    return $session_id;
}

function cl_is_logged() {

    if (isset($_POST['session_id'])) {
        $id = cl_get_userfromsession_id($_POST['session_id'], false);
        if (is_numeric($id) && not_empty($id)) {
            return array(
                "auth"     => true,
                "id"       => $id,
                "token"    => fetch_or_get($_POST['session_id'], 'none'),
                "platform" => "mobile"
            );
        }
        else {
            return array(
                "auth"     => false,
                "token"    => false,
                "platform" => "mobile"
            );
        }
    }

    else if (isset($_GET['session_id'])) {
        $id = cl_get_userfromsession_id($_GET['session_id'], false);
        if (is_numeric($id) && not_empty($id)) {
            return array(
                "auth"     => true,
                "id"       => $id,
                "token"    => fetch_or_get($_GET['session_id'], 'none'),
                "platform" => "mobile"
            );
        }
        else {
            return array(
                "auth"     => false,
                "token"    => false,
                "platform" => "mobile"
            );
        }
    }
    
    else if (isset($_COOKIE['user_id']) && not_empty($_COOKIE['user_id'])) {
        $id = cl_get_userfromsession_id($_COOKIE['user_id'], "web");
        if (is_numeric($id) && not_empty($id)) {
            return array(
                "auth"     => true,
                "id"       => $id,
                "token"    => fetch_or_get($_COOKIE['user_id'], 'none'),
                "platform" => "web"
            );
        }
    }

    else {
        return array(
            "auth"     => false,
            "token"    => false,
            "platform" => "web"
        );
    }
}

function cl_get_userfromsession_id($session_id, $platform = 'web') {
    global $db;
    if (empty($session_id)) {
        return false;
    }
    
    $platform     = cl_text_secure($platform);
    $session_id   = cl_text_secure($session_id);
    $return       = $db->where('session_id', $session_id);
    $return       = $db->where('lifespan', time(), '>');
    $return       = (not_empty($platform)) ? $db->where('platform', $platform) : $db;
    $session_data = $db->getOne(T_SESSIONS, array('user_id'));

    if (cl_queryset($session_data)) {
        return $session_data["user_id"];
    }
    

    return false;
}

function cl_update_user_data($user_id = null,$data = array()) {
    global $db;
    if ((not_num($user_id)) || (empty($data) || is_array($data) != true)) {
        return false;
    } 

    $db     = $db->where('id', $user_id);
    $update = $db->update(T_USERS,$data);
    return ($update == true) ? true : false;
}

function cl_uname_exists($uname = "") {
    global $db;
    return ($db->where('username', cl_text_secure($uname))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}

function cl_email_exists($email = "") {
    global $db;
    return ($db->where('email', cl_text_secure($email))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}

function cl_verify_emcode($emcode = "") {
    global $db;
    return ($db->where('em_code', cl_text_secure($emcode))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}

function cl_verify_invite_code($invite_code = "") {
    global $db;

    $db = $db->where("code", cl_text_secure($invite_code));
    $db = $db->where("expires_at", time(), ">");
    $db = $db->where("mnu", "0", ">");
    $qr = $db->getOne(T_USER_INVITES);

    if (not_empty($qr)) {
        if ($qr["registered_users"] < $qr["mnu"]) {
            return true;
        }
    }

    return false;
}

function cl_user_data($user_id = 0) {
    global $db, $cl;
    if (not_num($user_id)) {
        return false;
    } 

    $db        = $db->where('id', $user_id);
    $user_data = $db->getOne(T_USERS);

    if (empty($user_data)) {
        return false;
    }
  
    $user_data['name']         = cl_strf("%s",$user_data['fname']);
    $user_data['name']         = cl_rn_strip($user_data['name']);  
    $user_data['name']         = stripcslashes($user_data['name']);  
    $user_data['name']         = htmlspecialchars_decode($user_data['name'], ENT_QUOTES);   
    $user_data['about']        = cl_rn_strip($user_data['about']);  
    $user_data['about']        = stripcslashes($user_data['about']);  
    $user_data['about']        = htmlspecialchars_decode($user_data['about'], ENT_QUOTES);   
    $user_data['raw_uname']    = $user_data['username'];
    $user_data['raw_avatar']   = $user_data['avatar'];
    $user_data['raw_cover']    = $user_data['cover'];
    $user_data['avatar']       = cl_get_media($user_data['avatar']);
    $user_data['cover']        = cl_get_media($user_data['cover']);
    $user_data['url']          = cl_link($user_data['raw_uname']);
    $user_data['chaturl']      = cl_link(cl_strf("conversation/%s",$user_data['raw_uname']));
    $user_data['is_online']    = ($user_data['last_active'] > (time() - 60));
    $user_data['last_active']  = cl_time2str($user_data['last_active']);
    $user_data['joined']       = cl_date("F Y", $user_data['joined']);
    $user_data['settings']     = json($user_data['settings']);
    $user_data['premium_settings'] = json($user_data['premium_settings']);
    $user_data['blue_settings'] = json($user_data['blue_settings']);
    $user_data['swift']        = cl_init_swift($user_data['swift']);
    $user_data['country_a2c']  = fetch_or_get($cl['country_codes'][$user_data['country_id']],'us');
    $user_data['country_name'] = cl_translate($cl['countries'][$user_data['country_id']], 'Unknown');
    

    if ($user_data["start_up"] != "done") {
        $user_data["start_up"] = json($user_data["start_up"]);

        if (is_array($user_data["start_up"]) != true) {
            $user_data["start_up"] = "done";
        }
    }

    return $user_data;
}

function cl_init_swift($json = false) {

    try {
        $swift = json($json);

        if (is_array($swift)) {
            return $swift;
        }
        else {
            return array();
        }
    }
    catch (Exception $e) {
        return array();
    }
}

function cl_can_swift($swift = array()) {
    if (is_array($swift)) {
        $active_posts = 0;

        foreach ($swift as $row) {
            if (not_empty($row["exp_time"]) && $row["exp_time"] > time()) {
                $active_posts += 1;
            }
        }

        return ($active_posts >= 20) ? false : true;
    }

    else {
        return false;
    }
}

function cl_delete_swift($swift_id = array()) {
    global $me, $cl;

    if (not_empty($cl["is_logged"])) {
        $swift_data = isset($me["swift"][$swift_id]) ? $me["swift"][$swift_id] : false;

        if (is_array($swift_data)) {
            if ($swift_data["type"] == "image") {
                cl_delete_media($swift_data["media"]["src"]);
            }
            else if($swift_data["type"] == "video") {
                cl_delete_media($swift_data["media"]["source"]);
            }

            unset($me["swift"][$swift_id]);

            return $me["swift"];
        }
    }
    else{
        return false;
    }
}

function cl_is_junked_swift($swift_data = array()) {

    if (is_array($swift_data)) {
        if ($swift_data["status"] != "active" || $swift_data["exp_time"] <= time()) {
            return true;
        }
    }

    return false;
}

function cl_raw_user_data($user_id = 0) {
    global $db;
    
    if (not_num($user_id)) {
        return false;
    } 

    $db        = $db->where('id', $user_id);
    $user_data = $db->getOne(T_USERS);

    if (empty($user_data)) {
        return false;
    }

    return $user_data;
}

function cl_get_user_settings($user_id = 0) {
    global $db;
    
    if (not_num($user_id)) {
        return false;
    } 

    $user_data = cl_raw_user_data($user_id);

    if (empty($user_data)) {
        return false;
    }

    return json($user_data["settings"]);
}

function cl_signout_user() {
    global $db;
    if (not_empty($_SESSION['user_id'])) {
        $db->where('session_id', cl_text_secure($_SESSION['user_id']));
        $db->delete(T_SESSIONS);
    }

    if (not_empty($_COOKIE['user_id'])) {
        $db->where('session_id', cl_text_secure($_COOKIE['user_id']));
        $db->delete(T_SESSIONS);
        unset($_COOKIE['user_id']);
        setcookie('user_id', null, -1);

        unset($_COOKIE['dark_mode']);
        setcookie('dark_mode', null, -1);
    }

    @session_destroy();

    cl_redirect('/');
}

function cl_signout_user_by_id($user_id = false) {
    global $db;

    if (not_num($user_id)) {
        return false;
    }

    $db = $db->where('user_id', $user_id);
    $qr = $db->delete(T_SESSIONS);

    return $qr;
}

function cl_delete_user_data($user_id = false) {
    global $db;
    if (not_num($user_id)) {
        return false;
    }

    else {
        $db        = $db->where('id', $user_id);
        $user_data = $db->getOne(T_USERS);

        if (cl_queryset($user_data)) {

            /*===== Delete user notifications =====*/
                $db = $db->where('notifier_id', $user_id);
                $qr = $db->delete(T_NOTIFS);

                $db = $db->where('recipient_id', $user_id);
                $qr = $db->delete(T_NOTIFS);
            /*====================================*/

            /*===== Delete user bookmarks =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_BOOKMARKS);
            /*====================================*/

            /*===== Delete user reports =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_PROF_REPORTS);
                
                $db = $db->where('profile_id', $user_id);
                $qr = $db->delete(T_PROF_REPORTS);
            /*====================================*/

            /*===== Delete user blocks =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_BLOCKS);
                
                $db = $db->where('profile_id', $user_id);
                $qr = $db->delete(T_BLOCKS);
            /*====================================*/

            /*===== Delete user aff payouts =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_AFF_PAYOUTS);
            /*====================================*/

            /*===== Delete user wallet history =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_WALLET_HISTORY);
            /*====================================*/

            /*===== Delete user post reposts =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_PUB_REPORTS);
            /*====================================*/

            /*===== Delete user ads =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->get(T_ADS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        cl_delete_media($row['cover']);
                    }

                    $db = $db->where('user_id', $user_id);
                    $qr = $db->delete(T_ADS);
                }
            /*====================================*/

            /*===== Delete banktrans requests =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->get(T_BANKTRANS_REQS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        cl_delete_media($row['receipt_img']);
                    }

                    $db = $db->where('user_id', $user_id);
                    $qr = $db->delete(T_BANKTRANS_REQS);
                }
            /*====================================*/

            /*===== Delete user likes =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->get(T_LIKES);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        $post_data = cl_raw_post_data($row['pub_id']);

                        if (not_empty($post_data) && ($post_data['user_id'] != $user_id)) {
                            $num = ($post_data['likes_count'] -= 1);
                            $num = (is_posnum($num)) ? $num : 0;
                            cl_update_post_data($row['pub_id'], array(
                                'likes_count' => $num
                            ));
                        }
                    }

                    $db = $db->where('user_id', $user_id);
                    $qr = $db->delete(T_LIKES);
                }
            /*====================================*/

            /*===== Delete user reposts =====*/
                $db = $db->where('user_id', $user_id);
                $db = $db->where('type', 'repost');
                $qr = $db->get(T_POSTS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        $post_data = cl_raw_post_data($row['publication_id']);

                        if (not_empty($post_data) && ($post_data['user_id'] != $user_id)) {
                            $num = ($post_data['reposts_count'] -= 1);
                            $num = (is_posnum($num)) ? $num : 0;
                            cl_update_post_data($row['publication_id'], array(
                                'reposts_count' => $num
                            ));
                        }
                    }

                    $db = $db->where('user_id', $user_id);
                    $db = $db->where('type', 'repost');
                    $qr = $db->delete(T_POSTS);
                }
            /*====================================*/
            
            /*===== Delete user publications =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->get(T_PUBS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        if ($row['target'] == 'pub_reply') {
                            cl_update_thread_replys($row['thread_id'], 'minus');
                        }

                        $db = $db->where('publication_id', $row['id']);
                        $qr = $db->delete(T_POSTS);

                        cl_recursive_delete_post($row['id']);
                    }
                }
            /*====================================*/

            /*===== Delete user chats =====*/
                $db = $db->where('user_one', $user_id);
                $qr = $db->delete(T_CHATS);

                $db = $db->where('user_two', $user_id);
                $qr = $db->delete(T_CHATS);

                $db = $db->where('sent_by', $user_id);
                $db = $db->where('sent_to', $user_id, '=', 'OR');
                $qr = $db->get(T_MSGS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        if (not_empty($row['media_file'])) {
                            cl_delete_media($row['media_file']);
                        }
                    }

                    $db = $db->where('sent_by', $user_id);
                    $db = $db->where('sent_to', $user_id, '=', 'OR');
                    $qr = $db->delete(T_MSGS);
                }
            /*====================================*/

            /*===== Delete user connections =====*/
                $db = $db->where('follower_id', $user_id);
                $qr = $db->get(T_CONNECTIONS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        $user_data = cl_raw_user_data($row['following_id']);

                        if (not_empty($user_data)) {
                            $num = ($user_data['followers'] -= 1);
                            $num = (is_posnum($num)) ? $num : 0;
                            
                            cl_update_user_data($user_data['id'], array(
                                'followers' => $num
                            ));
                        }
                    }

                    $db = $db->where('follower_id', $user_id);
                    $qr = $db->delete(T_CONNECTIONS);
                }

                $db = $db->where('following_id', $user_id);
                $qr = $db->get(T_CONNECTIONS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        $user_data = cl_raw_user_data($row['follower_id']);

                        if (not_empty($user_data)) {
                            $num = ($user_data['following'] -= 1);
                            $num = (is_posnum($num)) ? $num : 0;

                            cl_update_user_data($user_data['id'], array(
                                'following' => $num
                            ));
                        }
                    }

                    $db = $db->where('following_id', $user_id);
                    $qr = $db->delete(T_CONNECTIONS);
                }
            /*====================================*/

            if (not_empty($user_data["swift"])) {
                $swift_data = cl_init_swift($user_data["swift"]);

                if (not_empty($swift_data)) {
                    foreach ($swift_data as $row) {
                        if ($row["type"] == "image") {
                            cl_delete_media($row["media"]["src"]);
                        }
                        else if($row["type"] == "video") {
                            cl_delete_media($row["media"]["source"]);
                        }
                    }
                }
            }
            

            $db = $db->where('user_id', $user_id);
            $qr = $db->delete(T_SESSIONS);
            $db = $db->where('id', $user_id);
            $qr = $db->delete(T_USERS);

            return true;
        }

        else {
            return false;
        }
    }
}

function cl_get_user_by_name($uname = null) {
    global $cl,$db;

    if (empty($uname)) {
        return false;
    }

    $db    = $db->where('username',$uname);
    $udata = $db->getOne(T_USERS, 'id');

    if (cl_queryset($udata)) {
        $user_id = intval($udata['id']);
        $udata   = cl_user_data($user_id);
    }

    return $udata;
}

function cl_get_user_id_by_name($uname = null) {
    global $cl, $db;

    if (empty($uname)) {
        return false;
    }

    $db      = $db->where('username', $uname);
    $udata   = $db->getOne(T_USERS, 'id');
    $user_id = 0;

    if (cl_queryset($udata)) {
        $user_id = intval($udata['id']);
    }

    return $user_id;
}

function cl_is_following($follower_id = false, $following_id = false) {
    global $db;

    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    else if($follower_id == $following_id) {
        return false;
    }

    $db  = $db->where('follower_id', $follower_id);
    $db  = $db->where('following_id', $following_id);
    $db  = $db->where('status', 'active');
    $res = $db->getValue(T_CONNECTIONS,'COUNT(id)');
    
    return is_posnum($res);
}

function cl_follow_requested($follower_id = false, $following_id = false) {
    global $db;

    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    else if($follower_id == $following_id) {
        return false;
    }

    $db  = $db->where('follower_id', $follower_id);
    $db  = $db->where('following_id', $following_id);
    $db  = $db->where('status', 'pending');
    $res = $db->getValue(T_CONNECTIONS, 'COUNT(id)');
    
    return is_posnum($res);
}

function cl_is_blocked($user_id = false, $profile_id = false) {
    global $db;

    if (is_posnum($user_id) != true || is_posnum($profile_id) != true) {
        return false;
    }

    else if($user_id == $profile_id) {
        return false;
    }

    $db  = $db->where('user_id', $user_id);
    $db  = $db->where('profile_id', $profile_id);
    $res = $db->getValue(T_BLOCKS, 'COUNT(id)');
    
    return is_posnum($res);
}

function cl_is_reported($user_id = false, $post_id = false) {
    global $db;

    if (is_posnum($user_id) != true || is_posnum($post_id) != true) {
        return false;
    }

    $db  = $db->where('user_id', $user_id);
    $db  = $db->where('post_id', $post_id);
    $res = $db->getValue(T_PUB_REPORTS, 'COUNT(id)');
    
    return is_posnum($res);
}

function cl_unfollow($follower_id = false, $following_id = false){
    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    $rm = cl_db_delete_item(T_CONNECTIONS, array(
        'follower_id'  => $follower_id,
        'following_id' => $following_id
    ));
    
    return $rm;
}

function cl_follow($follower_id = false, $following_id = false){
    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    $insert_id         = cl_db_insert(T_CONNECTIONS,array(
        'follower_id'  => $follower_id,
        'following_id' => $following_id,
        'time'         => time()
    ));

    return $insert_id;
}

function cl_follow_increase($follower_id = false, $following_id = false){
    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    $follower_udata  = cl_raw_user_data($follower_id);
    $following_udata = cl_raw_user_data($following_id);

    if (not_empty($follower_udata)) {

        $follow_num = ($follower_udata['following'] += 1);
        $follow_num = (empty($follow_num)) ? 0 : $follow_num;

        cl_update_user_data($follower_id, array(
            'following' => $follow_num
        ));
    }

    if (not_empty($following_udata)) {
        $follow_num = ($following_udata['followers'] += 1);
        $follow_num = (empty($follow_num)) ? 0 : $follow_num;

        cl_update_user_data($following_id, array(
            'followers' => $follow_num
        ));
    }

    return true;
}

function cl_follow_decrease($follower_id = false, $following_id = false){
    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    $follower_udata  = cl_raw_user_data($follower_id);
    $following_udata = cl_raw_user_data($following_id);

    if (not_empty($follower_udata)) {

        $follow_num = ($follower_udata['following'] -= 1);
        $follow_num = (empty($follow_num)) ? 0 : $follow_num;

        cl_update_user_data($follower_id, array(
            'following' => $follow_num
        ));
    }

    if (not_empty($following_udata)) {
        $follow_num = ($following_udata['followers'] -= 1);
        $follow_num = (empty($follow_num)) ? 0 : $follow_num;

        cl_update_user_data($following_id, array(
            'followers' => $follow_num
        ));
    }

    return true;
}

function cl_follow_request($follower_id = false, $following_id = false){
    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    $insert_id         = cl_db_insert(T_CONNECTIONS,array(
        'follower_id'  => $follower_id,
        'following_id' => $following_id,
        'status'       => "pending",
        'time'         => time()
    ));

    return $insert_id;
}

function cl_get_followers($user_id = false, $limit = 30, $offset = false) {
    global $db, $cl;

    if (is_posnum($user_id) != true) {
        return false;
    }

    $data         = array();
    $sql          = cl_sqltepmlate('components/sql/user/fetch_followers',array(
        't_users' => T_USERS,
        't_conns' => T_CONNECTIONS,
        'user_id' => $user_id,
        'limit'   => $limit,
        'offset'  => $offset,
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['about']            = cl_rn_strip($row['about']);
            $row['about']            = stripslashes($row['about']);
            $row['name']             = cl_strf("%s",$row['fname']);      
            $row['avatar']           = cl_get_media($row['avatar']);
            $row['url']              = cl_link($row['username']);
            $row['last_active']      = date("d M, y h:m A",$row['last_active']);
            $row['is_following']     = false;
            $row['follow_requested'] = false;
            $row['is_user']          = false;
            $row['common_follows']   = array();
            $row['country_a2c']      = fetch_or_get($cl['country_codes'][$row['country_id']], 'us');
            $row['country_name']     = cl_translate($cl['countries'][$row['country_id']], 'Unknown');

            if (not_empty($cl['is_logged'])) {
                $row['is_following'] = cl_is_following($cl['me']['id'], $row['id']);

                if ($cl['me']['id'] == $row['id']) {
                    $row['is_user'] = true; 
                }

                if (empty($row['is_following'])) {
                    $row['follow_requested'] = cl_follow_requested($cl['me']['id'], $row['id']);
                }

                $row['common_follows'] = cl_get_common_follows($row['id']);
            }

            $data[] = $row;
        }
    }

    return $data;
}

function cl_get_followings($user_id = false, $limit = 30, $offset = false) {
    global $db, $cl;

    if (is_posnum($user_id) != true) {
        return false;
    }

    $data         =  array();
    $sql          =  cl_sqltepmlate('components/sql/user/fetch_followings', array(
        't_users' => T_USERS,
        't_conns' => T_CONNECTIONS,
        'user_id' => $user_id,
        'limit'   => $limit,
        'offset'  => $offset
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['about']            = cl_rn_strip($row['about']);
            $row['about']            = stripslashes($row['about']);
            $row['name']             = cl_strf("%s",$row['fname']);      
            $row['avatar']           = cl_get_media($row['avatar']);
            $row['url']              = cl_link($row['username']);
            $row['last_active']      = date("d M, y h:m A",$row['last_active']);
            $row['is_following']     = false;
            $row['follow_requested'] = false;
            $row['is_user']          = false;
            $row['country_a2c']      = fetch_or_get($cl['country_codes'][$row['country_id']], 'us');
            $row['country_name']     = cl_translate($cl['countries'][$row['country_id']], 'Unknown');
            $row['common_follows']   = array();

            if (not_empty($cl['is_logged'])) {
                $row['is_following'] = cl_is_following($cl['me']['id'], $row['id']);

                if ($cl['me']['id'] == $row['id']) {
                    $row['is_user'] = true; 
                }

                if (empty($row['is_following'])) {
                    $row['follow_requested'] = cl_follow_requested($cl['me']['id'], $row['id']);
                }

                $row['common_follows'] = cl_get_common_follows($row['id']);
            }

            $data[] = $row;
        }
    }

    return $data;
}

function cl_get_follow_suggestions($limit = 10, $offset = false) {
    global $db, $cl, $me;

    $data          = array();
    $user_id       = ((not_empty($cl['is_logged'])) ? $me['id'] : false);
    $sql           = cl_sqltepmlate('components/sql/user/fetch_follow_suggestions',array(
        't_users'  => T_USERS,
        't_conns'  => T_CONNECTIONS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $user_id,
        'limit'    => $limit,
        'offset'   => $offset
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['about']            = cl_rn_strip($row['about']);
            $row['about']            = stripslashes($row['about']);
            $row['name']             = cl_strf("%s",$row['fname']);
            $row['name']             = cl_rn_strip($row['name']);
            $row['name']             = stripslashes($row['name']);      
            $row['avatar']           = cl_get_media($row['avatar']);
            $row['url']              = cl_link($row['username']);
            $row['last_active']      = date("d M, y h:m A",$row['last_active']);
            $row['follow_requested'] = false;
            $row['country_a2c']      = fetch_or_get($cl['country_codes'][$row['country_id']], 'us');
            $row['country_name']     = cl_translate($cl['countries'][$row['country_id']], 'Unknown');
            $row['common_follows']   = array();

            if (not_empty($user_id)) {
                $row['follow_requested'] = cl_follow_requested($user_id, $row['id']);
                $row['common_follows']   = cl_get_common_follows($row['id']);
            }
            
            $data[] = $row;
        }
    }

    return $data;
}

function cl_get_common_follows($user_id = false) {
    global $db, $cl;

    if ($user_id == $cl["me"]["id"]) {
        return array();
    }

    $data         =  array();
    $sql          =  cl_sqltepmlate('components/sql/user/fetch_common_follows', array(
        't_users' => T_USERS,
        't_conns' => T_CONNECTIONS,
        'user_id' => $user_id,
        'myid'    => $cl["me"]["id"]
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['name']   = cl_strf("%s",$row['fname']);      
            $row['avatar'] = cl_get_media($row['avatar']);
            $row['url']    = cl_link($row['username']);
            $data[]        = $row;
        }
    }

    return $data;
}

function cl_is_feed_gad_allowed() {
    global $cl;

    if ($cl["is_logged"] == true) {
        if ($cl["me"]["is_premium"] == 1) {
            if (not_empty($cl["me"]["premium_settings"]["disable_adsense_ads"])) {
                return false;
            }
        }
    }
    if ($cl["is_logged"] == true) {
        if ($cl["me"]["is_blue"] == 1) {
            if (not_empty($cl["me"]["blue_settings"]["disable_adsense_ads"])) {
                return false;
            }
        }
    }

    return true;
}

function cl_is_feed_nad_allowed() {
    global $cl;

    if ($cl["is_logged"] == true) {
        if ($cl["me"]["is_premium"] == 1) {
            if (not_empty($cl["me"]["premium_settings"]["disable_native_ads"])) {
                return false;
            }
        }
    }
    if ($cl["is_logged"] == true) {
        if ($cl["me"]["is_blue"] == 1) {
            if (not_empty($cl["me"]["blue_settings"]["disable_native_ads"])) {
                return false;
            }
        }
    }


    return true;
}


function cl_get_follow_requests($limit = 30, $offset = false) {
    global $db, $cl, $me;

    $data         = array();
    $sql          = cl_sqltepmlate('components/sql/user/fetch_follow_requests', array(
        't_users' => T_USERS,
        't_conns' => T_CONNECTIONS,
        'user_id' => $me["id"],
        'limit'   => $limit,
        'offset'  => $offset
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['about']          = cl_rn_strip($row['about']);
            $row['about']          = stripslashes($row['about']);
            $row['name']           = cl_strf("%s",$row['fname']);      
            $row['avatar']         = cl_get_media($row['avatar']);
            $row['url']            = cl_link($row['username']);
            $row['last_active']    = date("d M, y h:m A",$row['last_active']);
            $row['pending_req']    = true;
            $row['common_follows'] = cl_get_common_follows($row['id']);
            $row['country_a2c']    = fetch_or_get($cl['country_codes'][$row['country_id']], 'us');
            $row['country_name']   = cl_translate($cl['countries'][$row['country_id']], 'Unknown');
            $data[]                = $row;
        }
    }

    return $data;
}

function cl_get_follow_requests_total() {
    global $db, $me;

    $db = $db->where("following_id", $me["id"]);
    $db = $db->where("status", "pending");
    $qr = $db->getValue(T_CONNECTIONS, "COUNT(*)");

    return (is_posnum($qr)) ? $qr : 0;
}

function cl_notify_user($data = array()) {
    global $db, $cl, $me;

    if (empty($data)) {
        return false;
    }

    if(cl_allow_user_notification($data['user_id'], $data['subject'])) {
        $total = cl_db_get_total(T_NOTIFS, array(
            'notifier_id' => $me['id'],
            'recipient_id' => $data['user_id'],
            'subject' => $data['subject'],
            'entry_id' => $data['entry_id']
        ));

        if (empty($total)) {
            cl_db_insert(T_NOTIFS, array(
                'notifier_id' => $me['id'],
                'recipient_id' => $data['user_id'],
                'status' => '0',
                'entry_id' => $data['entry_id'],
                'subject' => $data['subject'],
                'time' => time()
            ));
        }
        else {
            cl_db_update(T_NOTIFS, array(
                'notifier_id' => $me['id'],
                'recipient_id' => $data['user_id'],
                'subject' => $data['subject'],
                'entry_id' => $data['entry_id']
            ), array(
                'time' => time(),
                'status' => '0'
            ));
        }

        cl_push_notify_user(array(
            'notifier_id' => $me['id'],
            'recipient_id' => $data['user_id'],
            'entry_id' => $data['entry_id'],
            'type' => $data['subject']
        ));
    }

    if ($cl["config"]["email_notifications"] == "on") {
        if(cl_allow_user_email_notification($data['user_id'], $data['subject'])) {
            $my_curr_lang      = $cl["curr_lang"];
            $cl['enotif_user'] = cl_user_data($data['user_id']);
            $cl["curr_lang"]   = array(
                "lang_data"    => $cl["languages"][$cl['enotif_user']['language']],
                "lang_text"    => cl_get_langs($cl['enotif_user']['language'])
            );

            $cl['enotif_data'] = array(
                "url" => cl_link($me['username']),
                "subject" => $data['subject'],
                "user_avatar" => $me['avatar'],
                "user_name" => $me['name'],
                "title" => array(
                    "verified" => cl_translate('Your verification request has been accepted'),
                    "reply" => cl_translate('Replied to your post'),
                    "subscribe" => cl_translate('Started following you'),
                    "subscribe_request" => cl_translate('Wants to follow you'),
                    "subscribe_accept" => cl_translate('Accepted your follow request'),
                    "mention" => cl_translate('Mentioned you in a post'),
                    "like" => cl_translate('Liked your post'),
                    "repost" => cl_translate('Shared your publication'),
                    "visit" => cl_translate('Visited your profile'),
                    "ad_approval" => cl_translate('Your add has been approved')
                ),
                "notif_text" => array(
                    "verified" => cl_translate('Congratulations! Your account has been successfully verified. Click on the link below to learn more'),
                    "reply" => cl_translate('User <b>@{@user_name@}</b> replied to your post. Click on the link below to learn more', array("user_name" => $me["name"])),
                    "subscribe" => cl_translate('User <b>@{@user_name@}</b> started following you. Click on the link below to learn more', array("user_name" => $me["name"])),
                    "subscribe_request" => cl_translate('User <b>@{@user_name@}</b> wants to follow you. Click on the link below to learn more', array("user_name" => $me["name"])),
                    "subscribe_accept" => cl_translate('User <b>@{@user_name@}</b> accepted your follow request. Click on the link below to learn more', array("user_name" => $me["name"])),
                    "mention" => cl_translate('User <b>@{@user_name@}</b> mentioned you in a post.', array("user_name" => $me["name"])),
                    "like" => cl_translate('User <b>@{@user_name@}</b> liked your post.', array("user_name" => $me["name"])),
                    "repost" => cl_translate('User <b>@{@user_name@}</b> shared your publication.', array("user_name" => $me["name"])),
                    "visit" => cl_translate('User <b>@{@user_name@}</b> visited your profile.', array("user_name" => $me["name"])),
                    "ad_approval" => cl_translate('Congratulations! Your add has been approved. Click on the link below to learn more')
                )
            );

            if (in_array($data['subject'], array('reply', 'repost', 'like', 'mention'))) {
                $cl['enotif_data']['url'] = cl_link(cl_strf("thread/%d", $data['entry_id']));
            }

            else if ($data['subject'] == "ad_approval") {
                $cl['enotif_data']['url'] = cl_link(cl_strf("ads/%d", $data['entry_id']));
            }
            
            $send_email_data = array(
                'from_email'   => $cl['config']['email'],
                'from_name'    => $cl['config']['name'],
                'to_email'     => $cl['enotif_user']['email'],
                'to_name'      => $cl['enotif_user']['name'],
                'subject'      => cl_strf("%s - %s", $me['name'], $cl['enotif_data']['title'][$data['subject']]),
                'charSet'      => 'UTF-8',
                'is_html'      => true,
                'message_body' => cl_template('emails/notification')
            );

            try {
                cl_send_mail($send_email_data);
            }

            catch (Exception $e) {/*PASS*/}

            $cl["curr_lang"] = $my_curr_lang;
        }
    }
}

function cl_push_notify_user($data = array()) {
    global $db, $cl;

    if ($cl["config"]["push_notifs"] == "on") {

        $event_messages = array(
            "subscribe" => cl_translate('Started following you'),
            "reply" => cl_translate('Replied to your post'),
            "subscribe_accept" => cl_translate('Accepted your follow request'),
            "subscribe_request" => cl_translate('Wants to follow you'),
            "like" => cl_translate('Liked your post'),
            "repost"  => cl_translate('Shared your publication'),
            "visit" => cl_translate('Visited your profile'),
            "mention" => cl_translate('Mentioned you in a post'),
            "post_created" => cl_translate('Posted a new publication'),
            "chat_message" => cl_translate('Sent you a new message')
        );

        if(in_array($data['type'], array("post_created", "chat_message")) != true && cl_allow_user_notification($data['recipient_id'], $data['type']) != true) {
           return false; 
        }

        else {
            $recipient_data = cl_raw_user_data($data["recipient_id"]);
            $firebase_kye   = $cl["config"]["firebase_api_key"];
            $firebase_url   = "https://fcm.googleapis.com/fcm/send";

            if (not_empty($recipient_data)) {
                $pnotif_token = json($recipient_data["pnotif_token"]);
                
                if (is_array($pnotif_token) && not_empty($pnotif_token["token"])) {

                    try {

                        if ($pnotif_token["type"] == "ios") {
                            $notif_body = array(
                                "type"  => str_replace('_', '-', $data["type"]),
                                "title" => $cl["me"]["name"],
                                "body"  => $event_messages[$data["type"]],
                                "badge" => 1,
                                "sound" => "default"
                            );

                            if ($data["type"] == "chat_message") {
                                $notif_body["chat_message"] = $data["chat_message"];
                            }

                            else {
                                $notif_body["data"] = array(
                                    "type" => str_replace('_', '-', $data["type"]),
                                    "title" => $cl["me"]["name"],
                                    "body" => $event_messages[$data["type"]]
                                );
                            }

                            if (in_array($data["type"], array("subscribe", "subscribe_accept", "subscribe_request", "visit"))) {
                                $notif_body["user_id"] = $data["entry_id"];
                            }

                            else {
                                if ($data["type"] != "chat_message") {
                                    $notif_body["post_id"] = $data["entry_id"];
                                }
                            }

                            $req_fields = array(
                                "collapse_key" => "type_a",
                                "to" => $pnotif_token["token"], 
                                "notification" => $notif_body
                            );

                            $http_headers = array(cl_strf("Authorization: key=%s", $firebase_kye), "Content-Type: application/json; charset=utf-8");
                            $http_req     = curl_init();

                            curl_setopt($http_req, CURLOPT_URL, $firebase_url);
                            curl_setopt($http_req, CURLOPT_POST, true);
                            curl_setopt($http_req, CURLOPT_HTTPHEADER, $http_headers);
                            curl_setopt($http_req, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($http_req, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($http_req, CURLOPT_POSTFIELDS, json_encode($req_fields));
                            curl_setopt($http_req, CURLOPT_TIMEOUT, 50);
                            curl_setopt($http_req, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                            $result = curl_exec($http_req);

                            curl_close($http_req);
                        }
                        else {
                            $notif_body = array(
                                "type" => $data["type"],
                                "title" => $cl["me"]["name"],
                                "body" => $event_messages[$data["type"]],
                                "android_channel_id" => "colibri"
                            );

                            if ($data["type"] == "chat_message") {
                                $notif_body["chat_message"] = $data["chat_message"];
                            }

                            if (in_array($data["type"], array("subscribe", "subscribe_accept", "subscribe_request", "visit"))) {
                                $notif_body["user_id"] = $data["entry_id"];
                            }

                            else {
                                if ($data["type"] != "chat_message") {
                                    $notif_body["post_id"] = $data["entry_id"];
                                }
                            }

                            $req_fields = array(
                                "collapse_key" => "type_a",
                                "to" => $pnotif_token["token"], 
                                "data" => $notif_body
                            );

                            $http_headers = array(cl_strf("Authorization: key=%s", $firebase_kye), "Content-Type: application/json");
                            $http_req     = curl_init();

                            curl_setopt($http_req, CURLOPT_URL, $firebase_url);
                            curl_setopt($http_req, CURLOPT_POST, true);
                            curl_setopt($http_req, CURLOPT_HTTPHEADER, $http_headers);
                            curl_setopt($http_req, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($http_req, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($http_req, CURLOPT_POSTFIELDS, json_encode($req_fields));
                            curl_setopt($http_req, CURLOPT_TIMEOUT, 50);
                            curl_setopt($http_req, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                            $result = curl_exec($http_req);

                            curl_close($http_req);
                        }
                    } 

                    catch (Exception $e) {
                        /*pass*/ 
                    }
                }  
            } 
        }
    }
}

function cl_allow_user_notification($user_id = false, $subject = false) {
    if (not_num($user_id)) {
        return false;
    }

    else if(in_array($subject, array("ad_approval"))) {
        return true;
    }

    else {
        $udata_settings = cl_get_user_settings($user_id);

        if (not_empty($udata_settings) && is_array($udata_settings)) {
            if (not_empty($udata_settings["notifs"][$subject])) {
                return true;
            }
            else {
                return false;
            }
        }

        return false;
    }
}

function cl_allow_user_email_notification($user_id = false, $subject = false) {
    if (not_num($user_id)) {
        return false;
    }

    else if(in_array($subject, array("ad_approval"))) {
        return true;
    }

    else {
        $udata_settings = cl_get_user_settings($user_id);

        if (not_empty($udata_settings) && is_array($udata_settings)) {
            if (not_empty($udata_settings["enotifs"][$subject])) {
                return true;
            }
            else {
                return false;
            }
        }

        return false;
    }
}

function cl_get_user_mentions($text = "") {
    if (empty($text) || is_string($text) != true) {
        return array();
    }

    $users = array();

    preg_match_all('/@([a-zA-Z0-9_]{3,32})/is', $text, $mentions);
    
    if (is_array($mentions) && not_empty($mentions[1])) {
        $users = $mentions[1];
    }

    return $users;
}

function cl_notify_mentioned_users($users = array(), $post_id = false) {
    global $db, $cl, $me;

    if (empty($cl['is_logged']) || is_posnum($post_id) != true) {
        return false;
    }

    foreach ($users as $username) {
        $uid = cl_get_user_id_by_name($username);

        if ($uid && ($uid != $me['id'])) {
            cl_notify_user(array(
                'subject'  => 'mention',
                'user_id'  => $uid,
                'entry_id' => $post_id
            ));
        }
    }
}

function cl_likify_mentions($text = "") {
    $text = preg_replace_callback('/@([a-zA-Z0-9_]{3,32})/is', function($m) {

        $uname = fetch_or_get($m[1]);

        if (not_empty($uname) && cl_get_user_id_by_name($uname)) {
            return (" " . cl_html_el('a', cl_strf("@%s", $uname), array(
                'href'   => cl_link($uname),
                'target' => '_blank',
                'class'  => 'inline-link',
            )) . " ");
        }
        else{
            return $uname;
        }

    }, $text);

    return $text;
}

function cl_total_new_notifs() {
    global $db, $cl;

    if (empty($cl['is_logged'])) {
        return 0;
    }

    $sql           = cl_sqltepmlate('apps/notifications/sql/fetch_unseen_total', array(
        't_notifs' => T_NOTIFS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $cl['me']['id']
    ));

    $qr = $db->rawQueryOne($sql);

    if (cl_queryset($qr)) {
        return fetch_or_get($qr['total'], 0);
    }

    return 0;
}

function cl_total_new_messages() {
    global $db, $cl;

    if (empty($cl['is_logged'])) {
        return 0;
    }

    $sql           = cl_sqltepmlate('apps/chat/sql/fetch_total', array(
        't_msgs'   => T_MSGS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $cl['me']['id']
    ));

    $qr = $db->rawQueryOne($sql);

    if (cl_queryset($qr)) {
        return fetch_or_get($qr['total'], 0);
    }

    return 0;
}

function cl_get_blocked_user_ids($user_id = false) {
    global $db, $cl;

    if (not_num($user_id)) {
        return array();
    }

    else {
        $db    = $db->where('user_id', $user_id);
        $users = $db->get(T_BLOCKS, null, array('profile_id'));
        $data  = array();

        if (cl_queryset($users)) {
            foreach ($users as $row) {
                $data[] = $row['profile_id'];
            }
        }

        return $data;
    }
}

function cl_get_blocked_users() {
    global $db, $cl;

    $data          = array();
    $sql           = cl_sqltepmlate('components/sql/user/fetch_blocked_users', array(
        't_users'  => T_USERS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $cl['me']['id']
    ));

    $users = $db->rawQuery($sql);

    if (cl_queryset($users)) {
        foreach ($users as $row) {
            $row['about']          = cl_rn_strip($row['about']);
            $row['about']          = stripslashes($row['about']);
            $row['name']           = cl_strf("%s",$row['fname']);      
            $row['avatar']         = cl_get_media($row['avatar']);
            $row['url']            = cl_link($row['username']);
            $row['common_follows'] = array();
            $row['country_a2c']    = fetch_or_get($cl['country_codes'][$row['country_id']], 'us');
            $row['country_name']   = cl_translate($cl['countries'][$row['country_id']], 'Unknown');

            if (not_empty($cl['is_logged'])) {
                $row['common_follows'] = cl_get_common_follows($row['profile_id']);
            }

            $data[] = $row;
        }
    }

    return $data;
}

function cl_calc_affiliate_bonuses() {
    global $cl;

    if (empty($cl['is_logged'])) {
        return "0.00";
    }

    else {
        $money      = "0.00";
        $bonus_rate = $cl['config']['aff_bonus_rate'];

        if (not_empty($cl['me']['aff_bonuses'])) {
            $money = ($bonus_rate * $cl['me']['aff_bonuses']);
        }

        return $money;
    }
}

function cl_aff_request_exists() {
    global $db, $cl;

    $db = $db->where('user_id', $cl['me']['id']);
    $db = $db->where('status', 'pending');
    $qr = $db->getValue(T_AFF_PAYOUTS, 'COUNT(*)');

    return ($qr > 0);
}

function cl_mention_ac_search($username = false) {
    global $db, $cl;

    $db = $db->where("username", "%$username%", "LIKE");
    $db = $db->orWhere("fname", "%$username%", "LIKE");
    $db = $db->where("active", "1");
    $qr = $db->get(T_USERS, 50, array("username", "fname",  "avatar", "verified"));

    if (cl_queryset($qr)) {
        $data = array();

        foreach ($qr as $row) {
            $row['name']     = cl_strf("%s", $row['fname']);      
            $row['avatar']   = cl_get_media($row['avatar']);
            $row['url']      = cl_link($row['username']);
            $row['name']     = cl_rn_strip($row['name']);
            $row['name']     = stripslashes($row['name']);

            array_push($data, array(
                "name" => $row["name"],
                "avatar" => $row["avatar"],
                "username" => $row["username"],
                "verified" => $row["verified"],
                "url" => $row["url"]
            ));
        }

        return $data;
    }

    return false;
}

function cl_hashtag_ac_search($hashtag = false) {
    global $db, $cl;

    $db = $db->where("tag", "%$hashtag%", "LIKE");
    $db = $db->where("posts", "0", ">");
    $qr = $db->get(T_HTAGS, 50);

    if (cl_queryset($qr)) {
        $data = array();

        foreach ($qr as $row) {
            array_push($data, array(
                "tag" => $row["tag"],
                "posts" => cl_number($row["posts"])
            ));
        }

        return $data;
    }

    return false;
}

function cl_get_ui_langs() {
    global $db;

    $db = $db->where("status", "1");
    $qr = $db->get(T_UI_LANGS);

    if (cl_queryset($qr)) {
        $langs = array();

        foreach($qr as $lang_data) {
            $langs[$lang_data["slug"]] = $lang_data;
        }

        return $langs;
    }

    return array();
}

function cl_money_recipients_search($username = false) {
    global $db, $cl;

    if ($cl["is_logged"] != true) {
        return array();
    }

    $db = $db->where("id", $cl["me"]["id"], "!=");
    $db = $db->where("username", "%$username%", "LIKE");
    $db = $db->orWhere("fname", "%$username%", "LIKE");
    $db = $db->where("active", "1");
    $qr = $db->get(T_USERS, 50, array("id", "username", "fname",  "avatar", "verified"));

    if (cl_queryset($qr)) {
        $data = array();

        foreach ($qr as $row) {
            $row['name']   = cl_strf("%s", $row['fname']);      
            $row['avatar'] = cl_get_media($row['avatar']);
            $row['name']   = cl_rn_strip($row['name']);
            $row['name']   = stripslashes($row['name']);

            array_push($data, array(
                "id" => $row["id"],
                "name" => $row["name"],
                "avatar" => $row["avatar"],
                "username" => $row["username"],
                "verified" => $row["verified"]
            ));
        }

        return $data;
    }

    return false;
}
