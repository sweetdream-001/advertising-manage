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

function cl_admin_get_post_reports($args = array()) {
	global $db;

	$args           = (is_array($args)) ? $args : array();
    $options        = array(
        "offset"    => false,
        "limit"     => 7,
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
    $t_reports      = T_PUB_REPORTS;
    $sql            = cl_sqltepmlate('apps/cpanel/post_reports/sql/fetch_reports',array(
        'offset'    => $offset,
        't_users'   => $t_users,
        't_reports' => $t_reports,
        'limit'     => $limit,
        'offset_to' => $offset_to,
        'order'     => $order
    ));

    $data    = array();
    $reports = $db->rawQuery($sql);

    if (cl_queryset($reports)) {
        foreach ($reports as $row) {
            $row['u_url']    = cl_link($row['username']);
            $row['post_url'] = cl_link(cl_strf('thread/%d', $row['post_id']));
            $row['avatar']   = cl_get_media($row['avatar']);
            $row['time']     = date('d F, Y h:m', $row['time']);
            $data[]          = $row;
        }
    }

    return $data;
}

function cl_admin_get_total_post_reports() {
	global $db;

	$qr = $db->getValue(T_PUB_REPORTS, 'COUNT(*)');

	if (is_posnum($qr)) {
		return $qr;
	}

	return 0;
}

function cl_admin_get_post_report_data($report_id = false) {
	global $db;

	if (not_num($report_id)) {
		return array();
	}

    $data         = array();
    $t_users      = T_USERS;
    $t_reps       = T_PUB_REPORTS;
    $sql          = cl_sqltepmlate('apps/cpanel/post_reports/sql/fetch_report_data',array(
        't_users' => $t_users,
        't_reps'  => $t_reps,
        'rep_id'  => $report_id,
    ));

    $data   = array();
    $report = $db->rawQueryOne($sql);

    if (cl_queryset($report)) {
        $report['url']     = cl_link($report['username']);
        $report['avatar']  = cl_get_media($report['avatar']);
        $report['time']    = date('d M, Y h:m', $report['time']);
        $report['comment'] = cl_linkify_urls($report['comment']);
        $report['comment'] = cl_rn2br($report['comment']);
        $report['comment'] = cl_strip_brs($report['comment']);
        $data              = $report;
    }

    if ($report['seen'] == '0') {
    	$db = $db->where('id', $report_id);
    	$qr = $db->update(T_PUB_REPORTS, array(
    		'seen' => '1'
    	));
    }

    return $data;
}