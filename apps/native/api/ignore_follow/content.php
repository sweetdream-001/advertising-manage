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

	$req_id = fetch_or_get($_POST['req_id'], 0);
    $req_id = (is_posnum($req_id)) ? $req_id : 0;

    $req_data = cl_db_get_item(T_CONNECTIONS, array(
        "id" => $req_id
    ));

    if (not_empty($req_data)) {
        $udata = cl_raw_user_data($req_data["follower_id"]);

        if (not_empty($udata)) {
            cl_db_delete_item(T_CONNECTIONS, array(
                "id" => $req_id
            ));

            $data['message'] = "Subscription request successfully deleted";
            $data['code']    = 200;
            $data['data']    = array(
                "total" => cl_get_follow_requests_total()
            );
        }
        else{
            $data['code']    = 400;
            $data['message'] = "Follow request ID is missing or invalid";
            $data['data']    = array();
        }
    }
    else{
        $data['code']    = 400;
        $data['message'] = "Follow request ID is missing or invalid";
        $data['data']    = array();
    }
}