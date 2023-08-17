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

function cl_admin_get_verification_requests($args = array()) {
	global $db;

	$args           = (is_array($args)) ? $args : array();
    $options        = array(
        "offset"    => false,
        "limit"     => 10,
        "offset_to" => false,
        "order"     => 'DESC'
    );

    $args           = array_merge($options, $args);
    $offset         = $args['offset'];
    $limit          = $args['limit'];
    $order          = $args['order'];
    $offset_to      = $args['offset_to'];
    $data           = array();
    $t_users        = T_USERS;
    $t_reqs         = T_VERIFICATIONS;
    $sql            = cl_sqltepmlate('apps/cpanel/account_verification/sql/fetch_requests',array(
        'offset'    => $offset,
        't_users'   => $t_users,
        't_reqs'    => $t_reqs,
        'limit'     => $limit,
        'offset_to' => $offset_to,
        'order'     => $order
    ));

    $data     = array();
    $requests = $db->rawQuery($sql);

    if (cl_queryset($requests)) {
        foreach ($requests as $row) {
            $row['url']         = cl_link($row['username']);
            $row['avatar']      = cl_get_media($row['avatar']);
            $row['last_active'] = date('d M, Y h:m', $row['last_active']);
            $row['time']        = date('d M, Y h:m', $row['time']);
            $data[]             = $row;
        }
    }

    return $data;
}

function cl_admin_get_verification_request_data($req_id = false) {
	global $db;

	if (not_num($req_id)) {
		return array();
	}

    $data         = array();
    $t_users      = T_USERS;
    $t_reqs       = T_VERIFICATIONS;
    $sql          = cl_sqltepmlate('apps/cpanel/account_verification/sql/fetch_request_data',array(
        't_users' => $t_users,
        't_reqs'  => $t_reqs,
        'req_id'  => $req_id,
    ));

    $data    = array();
    $request = $db->rawQueryOne($sql);

    if (cl_queryset($request)) {
        $request['url']           = cl_link(cl_strf('@%s', $request['username']));
        $request['avatar']        = cl_get_media($request['avatar']);
        $request['video_message'] = cl_get_media($request['video_message']);
        $request['last_active']   = date('d M, Y h:m', $request['last_active']);
        $request['time']          = date('d M, Y h:m', $request['time']);
        $request['file_name']     = cl_strf('%s - video appeal', $request['full_name']);
        $data                     = $request;
    }

    return $data;
}

function cl_admin_get_verification_requests_total() {
	global $db;

	$qr = $db->getValue(T_VERIFICATIONS, 'COUNT(*)');

	return (is_posnum($qr)) ? $qr : 0;
}