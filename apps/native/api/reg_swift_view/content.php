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
	$swift_id       = fetch_or_get($_POST["swid"], false);
    $swift_user_id  = fetch_or_get($_POST["user_id"], false);

    if (not_empty($swift_id) && is_posnum($swift_user_id) && $swift_user_id != $me["id"]) {
        $swift_udata = cl_raw_user_data($swift_user_id);

        if (not_empty($swift_udata)) {
            $swift_data = cl_init_swift($swift_udata["swift"]);

            if (is_array($swift_data) && isset($swift_data[$swift_id])) {

                if (in_array($me["id"], $swift_data[$swift_id]["views"]) != true) {
                    $swift_data[$swift_id]["views"][$me["id"]] = time();

                    cl_update_user_data($swift_user_id, array(
                        "swift" => cl_minify_js(json($swift_data, true))
                    ));
                }
            }

            $data['code']    = 200;
            $data['message'] = "Swift view registered successfully";
            $data['data']    = array();
        }
        else {
        	$data         = array(
				'code'    => 400,
				'data'    => array(),
				'message' => 'User ID is not valid or missing'
			);
        }
    }
}