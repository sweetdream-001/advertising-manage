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

//ini_set('display_errors', 0);
//ini_set('display_startup_errors',0);
error_reporting(E_ERROR);

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

$sql_db_host   = (isset($sql_db_host) ? $sql_db_host : "");
$sql_db_user   = (isset($sql_db_user) ? $sql_db_user : "");
$sql_db_pass   = (isset($sql_db_pass) ? $sql_db_pass : "");
$sql_db_name   = (isset($sql_db_name) ? $sql_db_name : "");
$site_url      = (isset($site_url)    ? $site_url    : "");
$mysqli        = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
$server_errors = array();

if (mysqli_connect_errno()) {
    $server_errors = mysqli_connect_error();

    if (not_empty($server_errors)) {
        header('Content-Type: application/json');
        echo json(array('status' => 400, "errors" => $server_errors), true);
        exit();
    }
}

$db_connection     = $mysqli;
$query             = $mysqli->query("SET NAMES utf8");
$set_charset       = $mysqli->set_charset('utf8mb4');
$set_charset       = $mysqli->query("SET collation_connection = utf8mb4_unicode_ci");
$db                = new MysqliDb($mysqli);
$url               = $site_url;
$config            = cl_get_configurations();
$config["url"]     = $url;
$cl["server_mode"] = "prod";
$cl["languages"]   = cl_get_ui_langs();
$cl["is_logged"]   = false;
$cl["is_admin"]    = false;
$cl["config"]      = $config;
$me                = array();
$cl['auth_status'] = cl_is_logged();

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

if (not_empty($cl['auth_status']['auth'])) {
    $user_data_ = cl_user_data($cl['auth_status']['id']);
    $me         = $cl['me'] = ((empty($user_data_)) ? false : $user_data_);

    if (empty($me)) {
        header('Content-Type: application/json');
        echo json(array('status' => 400, "error" => 'Invalid access token'), true);
        exit();
    }

    else {

        if (isset($cl["languages"][$me['language']])) {
            $cl["curr_lang"] = array(
                "lang_data" => $cl["languages"][$me['language']],
                "lang_text" => cl_get_langs($me['language'])
            );
        }

        $cl['is_logged']  = true;
        $me['draft_post'] = array();
        $cl["is_admin"]   = (($me['admin'] == '1') ? true : false);
        
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
    }
}