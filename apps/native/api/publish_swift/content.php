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

if (empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 401,
		'data'    => array(),
		'message' => 'Unauthorized Access'
	);
}

else {
    $swift_data = $me['swift'];
    $swift_text = fetch_or_get($_POST['swift_text'], "");
    $swift_text = cl_croptxt($swift_text, 200);

    if (not_empty($me["last_swift"]) && isset($swift_data[$me["last_swift"]])) {
        $swift_data[$me["last_swift"]]["status"]   = "active";
        $swift_data[$me["last_swift"]]["time"]     = time();
        $swift_data[$me["last_swift"]]["exp_time"] = (time() + (24 * 3600));
        $swift_data[$me["last_swift"]]["text"]     = "";

        if (not_empty($swift_text)) {
            $swift_data[$me["last_swift"]]["text"] = cl_text_secure($swift_text);
        }

        cl_update_user_data($me["id"], array(
            "swift"        => cl_minify_js(json($swift_data, true)),
            "last_swift"   => "",
            "swift_update" => (time() + (24 * 3600))
        ));

        $data['code']    = 200;
        $data['message'] = "Swift published successfully";
        $data["data"]    = array();
    }
}