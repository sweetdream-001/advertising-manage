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

function cl_get_account_wallet_history($offset = false, $limit = 10) {
	global $db, $cl;

	$data    = array();
	$db      = $db->where('user_id', $cl['me']['id']);
	$db      = (is_posnum($offset)) ? $db->where('id', $offset, '<') : $db;
	$db      = $db->orderBy('id','DESC');
	$history = $db->get(T_WALLET_HISTORY, $limit);

	if (cl_queryset($history)) {
		foreach ($history as $row) {
			$row['time']      = cl_time2str($row['time']);
			$row['json_data'] = json($row['json_data']);
			$data[]           = $row;
		}
	}

	return $data;
}