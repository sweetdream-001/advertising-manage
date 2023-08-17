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

function cl_raw_ad_data($ad_id = false) {
	global $db;

    if (not_num($ad_id)) {
        return false;
    } 

    $db = $db->where('status', array('active', 'inactive', 'orphan'), 'IN');
    $db = $db->where('id', $ad_id);
    $ad = $db->getOne(T_ADS);

    if (empty($ad)) {
        return false;
    }

    return $ad;
}

function cl_update_ad_data($ad_id = null, $data = array()) {
    global $db;
    
    if ((not_num($ad_id)) || (empty($data) || is_array($data) != true)) {
        return false;
    } 

    $db     = $db->where('id', $ad_id);
    $update = $db->update(T_ADS, $data);
    
    return ($update == true) ? true : false;
}

function cl_get_timeline_ads($ad_id = false) {
    global $db, $cl;

    $udata        = ((not_empty($cl['is_logged'])) ? $cl['me'] : false);
    $sql          = cl_sqltepmlate('components/sql/ads/fetch_feed_ads', array(
        't_ads'   => T_ADS,
        't_users' => T_USERS,
        'udata'   => $udata,
        'ad_id'   => $ad_id
    ));

    $views   = cl_session('ad_views');
    $clicks  = cl_session('ad_clicks');
    $ad_data = $db->rawQueryOne($sql);
    $data    = array();

    if (is_array($views) != true) {
        $views = array();
    }

    if (is_array($clicks) != true) {
        $clicks = array();

        cl_session('ad_clicks', $clicks);
    }

    if (cl_queryset($ad_data)) {
        $ad_data['is_conversed'] = false;
        $ad_data['advertising']  = true;
        $ad_data['cover']        = cl_get_media($ad_data['cover']);
        $ad_data['time']         = cl_time2str($ad_data['time']);
        $ad_data['description']  = stripcslashes($ad_data['description']);
        $ad_data['description']  = htmlspecialchars_decode($ad_data['description'], ENT_QUOTES);
        $ad_data['description']  = cl_linkify_urls($ad_data['description']);
        $ad_data['description']  = cl_rn2br($ad_data['description']);
        $ad_data['description']  = cl_strip_brs($ad_data['description']);
        $ad_data['company']      = stripcslashes($ad_data['company']);
        $ad_data['company']      = htmlspecialchars_decode($ad_data['company'], ENT_QUOTES);
        $ad_data['cta']          = stripcslashes($ad_data['cta']);
        $ad_data['cta']          = htmlspecialchars_decode($ad_data['cta'], ENT_QUOTES);
        $ad_data['show_stats']   = false;
        $ad_data['is_owner']     = false;
        $ad_data['owner']        = array(
            'avatar'             => cl_get_media($ad_data['avatar']),
            'name'               => $ad_data['name'],
            'username'           => $ad_data['username'],
            'verified'           => $ad_data['verified'],
            'url'                => cl_link($ad_data['username'])
        );

        if (not_empty($udata)) {
            $ad_data['is_owner'] = ($ad_data['user_id'] == $udata['id']);
        }

        if (in_array($ad_data['id'], $clicks)) {
            $ad_data['is_conversed'] = true;
        }

        if (in_array($ad_data['id'], $views) != true) {
            array_push($views, $ad_data['id']);

            cl_session('ad_views', $views);

            cl_update_ad_data($ad_data['id'], array(
                'views' => ($ad_data['views'] += 1)
            ));
        }

        $data = $ad_data;
    }

    return $data;
}
