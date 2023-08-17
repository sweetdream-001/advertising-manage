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

function cl_admin_get_user_invitations() {
	global $db;

	$db->where("expires_at", time(), "<")->delete(T_USER_INVITES);

	$db      = $db->orderBy("id", "DESC");
	$invites = $db->get(T_USER_INVITES);
	$data    = array();

	if (cl_queryset($invites)) {
		foreach ($invites as $itemdata) {
			$itemdata["time"]        = date("d M, Y h:i", $itemdata["time"]);
			$itemdata["expire_time"] = date("d M, Y h:i", $itemdata["expires_at"]);
			$itemdata["link"]        = cl_link(cl_strf("guest?auth=signup&invite_code=%s", $itemdata["code"]));
			$itemdata["link_short"]  = cl_croptxt($itemdata["link"], 45, "...");


			array_push($data, $itemdata);
		}
	}

	return $data;
}