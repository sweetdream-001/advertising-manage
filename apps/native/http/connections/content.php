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

// RESTRICT USER CODE
$visitor_ip = $_SERVER['REMOTE_ADDR'];
cl_check_ip_restriction($visitor_ip);

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

if (empty($_GET["uname"])) {
	cl_redirect("404");
}

$uname           = fetch_or_get($_GET["uname"], false);
$uname           = cl_text_secure($uname);
$cl['prof_user'] = cl_get_user_by_name($uname);
$cl['page_tab']  = fetch_or_get($_GET["tab"], "followers");

if (empty($cl['prof_user'])) {
	cl_redirect("404");
}

else {

	$cl['can_view']   = cl_can_view_profile($cl['prof_user']['id']);
	$cl["page_title"] = $cl['prof_user']['name'];
	$cl["page_desc"]  = $cl['prof_user']['about'];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "connections";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["users_list"] = array();

	if (not_empty($cl["is_logged"])) {
		$cl['prof_user']['owner']           = ($cl['prof_user']['id'] == $me['id']);
		$cl['prof_user']['follow_requests'] = cl_get_follow_requests_total();
	}

	if (not_empty($cl['can_view'])) {
		if ($cl['page_tab'] == 'followers') {
			$cl["users_list"] = cl_get_followers($cl['prof_user']['id'], 30, false);
		}

		else if ($cl['page_tab'] == 'follow_requests') {
			if (not_empty($cl['prof_user']['owner'])) {
				$cl["users_list"] = cl_get_follow_requests(30, false);
			}

			else{
				cl_redirect("404");
			}
		}

		else {
			$cl["users_list"] = cl_get_followings($cl['prof_user']['id'], 30, false);
		}
	}
	else {
		$cl['prof_user']['followers'] = 0;
		$cl['prof_user']['following'] = 0;
	}

	$cl["http_res"] = cl_template("connections/content");
}
