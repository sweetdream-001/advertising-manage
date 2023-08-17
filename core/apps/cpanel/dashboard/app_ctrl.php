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

function cl_admin_total_users() {
	global $db, $cl;

	$db  = $db->where('active', array('1', '2'), 'IN');
	$qr  = $db->getValue(T_USERS, 'COUNT(*)');
	$num = 0;

	if (is_posnum($qr)) {
		$num = $qr;
	}

	return $num;
}

function cl_admin_total_posts($type = false) {
	global $db, $cl;

	$db  = $db->where('status', array('active','inactive','delete'), 'IN');
	$db  = (($type && in_array($type, array('image', 'video'))) ? $db->where('type', $type) : $db);
	$qr  = $db->getValue(T_PUBS, 'COUNT(*)');
	$num = 0;

	if (is_posnum($qr)) {
		$num = $qr;
	}

	return $num;
}

function cl_admin_annual_main_stats() {
    global $db, $cl;
    $t_posts     =  T_PUBS;
    $t_users     =  T_USERS;
    $stats       =  array(
        'users'  => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        'posts'  => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
    );

    $year_months = array(
        date('U',strtotime(date("Y:01:01 00:00:00"))),
        date('U',strtotime(date("Y:02:01 00:00:00"))),
        date('U',strtotime(date("Y:03:01 00:00:00"))),
        date('U',strtotime(date("Y:04:01 00:00:00"))),
        date('U',strtotime(date("Y:05:01 00:00:00"))),
        date('U',strtotime(date("Y:06:01 00:00:00"))),
        date('U',strtotime(date("Y:07:01 00:00:00"))),
        date('U',strtotime(date("Y:08:01 00:00:00"))),
        date('U',strtotime(date("Y:09:01 00:00:00"))),
        date('U',strtotime(date("Y:10:01 00:00:00"))),
        date('U',strtotime(date("Y:11:01 00:00:00"))),
        date('U',strtotime(date("Y:12:01 00:00:00")))
    );

    foreach (array_keys($stats) as $stat) {
        if ($stat == 'users') {
            foreach ($year_months as $m_num => $m_time) {
                $next_num   = ($m_num + 1);
                $next_month = (isset($year_months[$next_num]) ? $year_months[$next_num] : 0);
                $db         = $db->where('active', '1');
                $db         = $db->where('joined', $m_time, '>=');
                $db         = (($next_month) ? $db->where('joined',$next_month,'<=') : $db);
                $qr         = $db->getValue($t_users, 'COUNT(*)');

                if (not_empty($qr)) {
                	$stats['users'][$m_num] = intval($qr);
                }
            }
        }

        else if($stat == 'posts'){  
            foreach ($year_months as $m_num => $m_time) {
                $next_num   = ($m_num + 1);
                $next_month = (isset($year_months[$next_num]) ? $year_months[$next_num] : 0);
                $db         = $db->where('status', 'active');
                $db         = $db->where('time',$m_time,'>=');
                $db         = (($next_month) ? $db->where('time',$next_month,'<=') : $db);
                $qr         = $db->getValue($t_posts, 'COUNT(*)');

                if (not_empty($qr)) {
                	$stats['posts'][$m_num] = intval($qr);
                }
            }
        }
    }

    return $stats;
}