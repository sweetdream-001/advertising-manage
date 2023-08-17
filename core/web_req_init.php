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

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("settings.php");
require_once("definitions.php");
require_once("components/tools.php");
require_once("components/shortcuts.php");
require_once("components/compilers.php");
require_once("components/localization.php");
require_once("components/glob_context.php");
require_once("components/user.php");
require_once("components/post.php");
require_once("components/ad.php");
require_once("configs/conf.php");
require_once("libs/DB/vendor/autoload.php");

$cl["db_errors"] = array();
$sql_db_host     = (isset($sql_db_host) ? $sql_db_host : "");
$sql_db_user     = (isset($sql_db_user) ? $sql_db_user : "");
$sql_db_pass     = (isset($sql_db_pass) ? $sql_db_pass : "");
$sql_db_name     = (isset($sql_db_name) ? $sql_db_name : "");
$site_url        = (isset($site_url)    ? $site_url    : "");
$mysqli          = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);

if (mysqli_connect_errno()) {
    array_push($cl["db_errors"], mysqli_connect_error());

    if (not_empty($cl["db_errors"])) {
        echo cl_html_template("db_errors");
        die();
    }
}

$me            = array();
$db_connection = $mysqli;
$query         = $mysqli->query("SET NAMES utf8");
$set_charset   = $mysqli->set_charset('utf8mb4');
$set_charset   = $mysqli->query("SET collation_connection = utf8mb4_unicode_ci");
$db            = new MysqliDb($mysqli);
$url           = $site_url;
$config        = cl_get_configurations();

date_default_timezone_set($config["timezone"]);

$config["url"]       = $url;
$config["theme_url"] = cl_strf("%s/themes/%s",$url,$config["theme"]);
$config["site_logo"] = cl_strf("%s/%s",$config["theme_url"],$config["site_logo"]);
$config["site_fav"]  = cl_strf("%s/%s",$config["theme_url"],$config["site_favicon"]);
$cl["is_logged"]     = false;
$cl["is_admin"]      = false;
$cl["config"]        = $config;
$cl["server_mode"]   = "prod"; 
$cl['csrf_token']    = cl_generate_csrf_token(); 
$cl['ref_url']       = http_referer();
$cl['auth_status']   = cl_is_logged();
$cl["languages"]     = cl_get_ui_langs();
$cl["display_set"]   = array(
    "color_scheme"   => $cl["config"]["default_color_scheme"],
    "background"     => $cl["config"]["default_bg_color"]
);

$cl["curr_lang"] = array(
    "lang_data"  => array(
        "id" => "0",
        "name" => "Default",
        "slug" => "default",
        "status" => "1",
        "is_rtl" => "N"
    ),
    "lang_text" => cl_get_langs("default")
);

if (isset($_COOKIE["lang"])) {
    if (isset($cl["languages"][$_COOKIE["lang"]])) {
        $cl["curr_lang"] = array(
            "lang_data" => $cl["languages"][$_COOKIE["lang"]],
            "lang_text" => cl_get_langs($_COOKIE["lang"])
        );
    }
}
else if(isset($cl["languages"][$config["language"]])) {
    $cl["curr_lang"] = array(
        "lang_data" => $cl["languages"][$config["language"]],
        "lang_text" => cl_get_langs($config["language"])
    );
}

if (not_empty($cl['auth_status']['auth'])) {
    $cl['hash_session'] = $cl['auth_status']['token'];
    $user_data_         = cl_user_data($cl['auth_status']['id']);
    $me                 = $cl['me'] = ((empty($user_data_)) ? false : $user_data_);

    if (not_empty($me)) {
        if (isset($cl["languages"][$me['language']])) {
            $cl["curr_lang"] = array(
                "lang_data" => $cl["languages"][$me['language']],
                "lang_text" => cl_get_langs($me['language'])
            );
        }
        
        $cl['is_logged']    = true;
        $me['draft_post']   = array();
        $me['new_notifs']   = cl_total_new_notifs();
        $me['new_messages'] = cl_total_new_messages();
        $me['new_notifs']   = ((is_posnum($me['new_notifs'])) ? $me['new_notifs'] : '');
        $me['new_messages'] = ((is_posnum($me['new_messages'])) ? $me['new_messages'] : '');
        $cl["is_admin"]     = (($me['admin'] == '1') ? true : false);
        $cl["display_set"]  = json($me["display_settings"]);
        
        if (is_posnum($me['last_post'])) {
            $me['draft_post'] = cl_get_orphan_post($me['last_post']);

            if (empty($me['draft_post'])) {
                cl_delete_orphan_posts($me['id']);
                cl_update_user_data($me['id'],array(
                    'last_post' => 0
                ));
            }
        }

        if ($me['last_active'] < (time() - (60 * 30))) {
            cl_update_user_data($me['id'], array(
                'last_active' => time(),
                'ip_address'  => cl_get_ip()
            ));
        }

        if ($me["is_premium"] && $me["premium_ex_date"] < time()) {
            cl_update_user_data($me['id'], array(
                'is_premium' => "0"
            ));
        }
        if ($me["is_blue"] && $me["blue_ex_date"] < time()) {
            cl_update_user_data($me['id'], array(
                'is_blue' => "0"
            ));
        }
    }
}

else {
    if ($cl['config']['affiliates_system'] == 'on') {
        if (not_empty($_GET['ref'])) {
            $ref_uname = cl_text_secure($_GET['ref']);
            $ref_udata = cl_get_user_by_name($ref_uname);

            if (not_empty($ref_udata)) {
                cl_session('ref_id', $ref_udata['id']);
            }
        }
    }
}

if (not_empty($_GET['language'])) {
    $lang_name = cl_text_secure($_GET['language']);

    if (isset($cl["languages"][$lang_name])) {
        setcookie("lang", $lang_name, strtotime("+1 year"), '/') or die('unable to create cookie');

        if (not_empty($cl["is_logged"])) {
            $set_lang = cl_update_user_data($me['id'], array('language' => $lang_name));
            $ref_url  = http_referer();

            if ($cl['ref_url']) {
                cl_location($cl['ref_url']);
            }
            else {
                cl_redirect_after('/', 0.05);
            }
        }
    }
}

if (not_empty($cl["config"]["google_ad_horiz"])) {
    $cl["gads_horiz"] = htmlspecialchars_decode($cl["config"]["google_ad_horiz"]);
}

if (not_empty($cl["config"]["google_ad_vert"])) {
    $cl["gads_vert"] = htmlspecialchars_decode($cl["config"]["google_ad_vert"]);
}