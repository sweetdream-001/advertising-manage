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
    $post_id = fetch_or_get($_POST['post_id'], 0);
    $option  = fetch_or_get($_POST['poll_id'], 0);

    if (is_posnum($post_id) && is_numeric($option)) {
        $post_data = cl_raw_post_data($post_id);

        if (not_empty($post_data) && $post_data["type"] == "poll") {
            $poll_data = json($post_data["poll_data"]);

            if (is_array($poll_data) && isset($poll_data[$option]) && cl_is_poll_voted($poll_data) == 0) {
                $poll_option_votes           = array_push($poll_data[$option]["voters"], $me["id"]);
                $poll_data[$option]["votes"] = $poll_option_votes;
                $poll_votes_result           = cl_cacl_poll_votes($poll_data);

                
                $update_status = cl_db_update(T_PUBS, array(
                    "id" => $post_id
                ), array(
                    "poll_data" => cl_minify_js(json($poll_data, true))
                ));

                if ($update_status !== true) {
                    $free_poll = array_map(function($option) {
                        return $option["voters"] = array();
                    }, $poll_data);

                    cl_db_update(T_PUBS, array(
                        "id" => $post_id
                    ), array(
                        "poll_data" => cl_minify_js(json($free_poll, true))
                    ));
                }

                $data["code"] = 200;
                $data["message"] = "Poll voted successfully";
                $data["data"] = array(
                    'poll_data' => $poll_votes_result
                );
            }
            else{
                $data['code'] = 400;
                $data['message'] = "An error occurred while processing your request";
                $data['data'] = array();
            }
        }
        else {
            $data['code'] = 400;
            $data['message'] = "Post id is missing or invalid";
            $data['data'] = array();
        }
    }
    else {
        $data['code'] = 400;
        $data['message'] = "Post id is missing or invalid";
        $data['data'] = array();
    }
}