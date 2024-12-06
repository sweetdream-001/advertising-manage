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

function cl_admin_get_restricted_ips($args = array()) {
    global $cl, $me, $db;
    
    $args = (is_array($args)) ? $args : array();
    $options        = array(
        "limit"     => $args['limit'],
        "order"     => 'DESC'
    );
    $args = array_merge($options, $args);
    $limit          =  $args['limit'];
    $order          =  $args['order'];
    $data           =  array();
    $sql            =  cl_sqltepmlate('apps/cpanel/ip_restriction/sql/fetch_restricted_ips',array(
        't_restricted_ips'     => T_RESTRICTED_IPS,
        'limit'     => $limit,
        'order'     => $order
    ));

    $data = array();
    $restricted_ips  = $db->rawQuery($sql);
    // echo '<pre>';print_r($restricted_ips);'</pre>';die;

    if (cl_queryset($restricted_ips)) {
        foreach ($restricted_ips as $ip_data) {
            $ip_data['id']          = $ip_data["id"];
            $ip_data['ip_address']  = $ip_data['ip_address'];
            $ip_data['time']        = cl_time2str($ip_data['name']);

            array_push($data, $ip_data);
        }
    }
    
    return $data;
}