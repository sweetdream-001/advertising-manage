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

function cl_translate($text = '', $data = array()) {
    global $cl;

    $langkey = cl_gen_lang_key($text);
    $langs   = $cl["curr_lang"]["lang_text"];
    
    if (in_array($langkey, array_keys($langs))) {
        $text_val = $langs[$langkey];

        if (not_empty($data) && is_array($data)) {
            foreach ($data as $key => $val) {
                $text_val = preg_replace_callback("/\{\@(.+?)\@\}/", function($m) use ($data) {
                    return ((isset($data[$m[1]])) ? $data[$m[1]] : '');
                }, $text_val);
            }
        }

        return stripslashes($text_val);
    }

    if ($cl["server_mode"] == 'dev') {
        try {
            $file_path = cl_full_path("core/langs/default.json");
            $def_lang  = file_get_contents($file_path);
            $def_lang  = json($def_lang);

            if (is_array($def_lang)) {
                $def_lang[$langkey] = $text;
                $def_lang           = json($def_lang,1);
                $save_lang          = file_put_contents($file_path,$def_lang);

                if (empty($save_lang)) {
                    die("Failed to save localization text: $text");
                }
            }
        } catch (Exception $e) { /*pass*/ }
    }

    return $text;
}

function cl_gen_lang_key($text = '') {
    if (empty($text) || is_string($text) != true) {
        return "";
    }

    $text    = trim($text);
    $langkey = preg_replace('/\{\@(.*?)\@\}/', '', $text);
    $langkey = strtolower(cl_slug($langkey));
    $langkey = cl_croptxt($langkey,65);
    return $langkey;
}

function cl_get_langs($lang = 'default') {
    global $cl;

    try {
        $language  = ($cl["server_mode"] == 'dev') ? "default" : $lang;
        $file_path = cl_full_path("core/langs/$language.json");

        if (file_exists($file_path) && is_readable($file_path)) {
            $usr_lang    = file_get_contents($file_path);
            $custom_lang = false;
            $usr_lang    = json($usr_lang);

            if ($cl["server_mode"] == 'prod') {
                $custom_lang = file_get_contents(cl_full_path("core/langs/custom/$language.json"));
            }

            if (not_empty($custom_lang)) {
                $custom_lang = json($custom_lang);

                if (is_array($custom_lang)) {
                    $usr_lang = array_merge($usr_lang, $custom_lang);
                }
            }

            if (is_array($usr_lang)) {
                foreach ($usr_lang as $key => $val) {
                    $usr_lang[$key] = stripcslashes($usr_lang[$key]);
                    $usr_lang[$key] = html_entity_decode($usr_lang[$key], ENT_QUOTES | ENT_HTML5);
                    $usr_lang[$key] = htmlspecialchars_decode($usr_lang[$key]);
                }

                return $usr_lang;
            }
            else {
                cl_session('lang', 'default');

                if (not_empty($cl["me"])) {
                    if ($cl["me"]["language"] != "default") {
                        cl_update_user_data($cl["me"]["id"], array(
                            "language" => "default"
                        ));
                    }
                }

                throw new Exception("Failed to load the display language, the system cannot continue.");
            }
        }
        else{
            cl_session('lang', 'default');

            if (not_empty($cl["me"])) {
                if ($cl["me"]["language"] != "default") {
                    cl_update_user_data($cl["me"]["id"], array(
                        "language" => "default"
                    ));
                }
            }

            die(cl_strf("Failed to load the display language <b>(%s)</b>, the system cannot continue. <a href='/'>Click here to reset</a>", fetch_or_get($cl['language_codes'][$lang]["name"])));
        }
    } 

    catch (Exception $e) {
        cl_session('lang', 'default');

        if (not_empty($cl["me"])) {
            if ($cl["me"]["language"] != "default") {
                cl_update_user_data($cl["me"]["id"], array(
                    "language" => "default"
                ));
            }
        }

        die($e->getMessage());
    }
}

