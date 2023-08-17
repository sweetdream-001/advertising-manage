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

function cl_get_affiliate_payouts($args = array()) {
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
    $t_reqs         = T_AFF_PAYOUTS;
    $sql            = cl_sqltepmlate('apps/cpanel/affiliate_payouts/sql/fetch_requests',array(
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
            $row['url']    = cl_link($row['username']);
            $row['avatar'] = cl_get_media($row['avatar']);
            $row['time']   = date('d M, Y h:m', $row['time']);
            $row['amount'] = cl_money($row['amount']);
            $data[]        = $row;
        }
    }

    return $data;
}

function cl_get_affiliate_payouts_total() {
	global $db;

	$qr = $db->getValue(T_AFF_PAYOUTS, 'COUNT(*)');

	return (is_posnum($qr)) ? $qr : 0;
}