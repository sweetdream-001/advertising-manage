<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)							@
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                   @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2021 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

function cl_get_affiliate_payouts($offset = false, $limit = false) {
	global $db, $cl;

	if (empty($cl['is_logged'])) {
		return array();
	}

	$data          = array();
	$sql           = cl_sqltepmlate('apps/affiliates/sql/fetch_history', array(
		't_affils' => T_AFF_PAYOUTS,
		'offset'   => $offset,
		'user_id'  => $cl['me']['id'],
		'limit'    => $limit
	));

	$history = $db->rawQuery($sql);
	
	if (cl_queryset($history)) {
		foreach ($history as $row) {
			$row['amount'] = cl_money($row['amount']);
			$row['time']   = date('d M, Y h:m', $row['time']);
			$data[]        = $row;
		}
	}

	return $data;
}