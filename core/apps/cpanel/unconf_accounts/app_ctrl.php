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

function cl_get_unconfirmed_accounts() {
	global $db;

	$db = $db->where("time", (time() - 604800), "<");
	$qr = $db->getValue(T_ACC_VALIDS, "COUNT(*)");

	if (is_posnum($qr)) {
		return $qr;
	}

	return 0;
}