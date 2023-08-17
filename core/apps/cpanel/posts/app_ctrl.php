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

function cl_admin_get_posts($args = array()) {
    global $cl, $me, $db;

    $args           = (is_array($args)) ? $args : array();
    $options        = array(
        "offset"    => false,
        "limit"     => 10,
        "offset_to" => false,
        "order"     => 'DESC',
        "keyword"   => false,
    );

    $args           =  array_merge($options, $args);
    $offset         =  $args['offset'];
    $limit          =  $args['limit'];
    $order          =  $args['order'];
    $keyword        =  $args['keyword'];
    $offset_to      =  $args['offset_to'];
    $data           =  array();
    $t_pubs         =  T_PUBS;
    $sql            =  cl_sqltepmlate('apps/cpanel/posts/sql/fetch_posts',array(
        'offset'    => $offset,
        't_pubs'    => $t_pubs,
        'limit'     => $limit,
        'offset_to' => $offset_to,
        'order'     => $order,
        'keyword'   => $keyword,
    ));

    $data  = array();
    $posts = $db->rawQuery($sql);

    if (cl_queryset($posts)) {
        foreach ($posts as $row) {
            $row['url']           = cl_link(cl_strf('thread/%d', $row['id']));
            $row['replys_count']  = cl_number($row['replys_count']);
            $row['reposts_count'] = cl_number($row['reposts_count']);
            $row['likes_count']   = cl_number($row['likes_count']);
            $row['time']          = date('d M, Y h:m',$row['time']);
            $data[]               = $row;
        }
    }
    
    return $data;
}