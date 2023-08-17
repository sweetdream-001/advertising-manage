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

function cl_admin_delete_user_old_swifts() {
	global $db, $cl;

	$db = $db->where("swift_update", 0, ">");
	$qr = $db->get(T_USERS, null, array("id", "swift"));

	if (cl_queryset($qr)) {
		foreach ($qr as $row) {
			$user_swifts = cl_init_swift($row["swift"]);

			if (is_array($user_swifts) && not_empty($user_swifts)) {
				foreach ($user_swifts as $swift_id => $swift_data) {
					if (cl_is_junked_swift($swift_data)) {
						if ($swift_data["type"] == "image") {
				            cl_delete_media($swift_data["media"]["src"]);
				        }
				        else if($swift_data["type"] == "video") {
				            cl_delete_media($swift_data["media"]["source"]);
				        }

				        unset($user_swifts[$swift_id]);
					}
				}
			}

			if (count($user_swifts) > 0) {
				cl_update_user_data($row["id"], array(
					"swift" => json($user_swifts, true)
				));
			}
			else {
				cl_update_user_data($row["id"], array(
					"swift" => json(array(), true),
					"swift_update" => 0
				));
			}
		}
	}
}
