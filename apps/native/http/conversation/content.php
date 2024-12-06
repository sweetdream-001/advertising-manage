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

if (empty($cl["is_logged"])) {
	cl_redirect("404");
}

$uname              = fetch_or_get($_GET["uname"], false);
$uname              = cl_text_secure($uname);
$cl['interlocutor'] = cl_get_user_by_name($uname);

if (empty($cl['interlocutor']) || ($cl['interlocutor']['id'] == $me['id'])) {
	cl_redirect("404");
}

if (cl_is_blocked($cl['interlocutor']['id'], $me['id'])) {
	cl_redirect("blocked");
}

else if(cl_is_blocked($me['id'], $cl['interlocutor']['id'])) {
	cl_redirect("404");
}

require_once(cl_full_path("core/apps/chat/app_ctrl.php"));

cl_session('interloc_user_id', $cl['interlocutor']['id']);

$cl["page_title"]  = $cl['interlocutor']['name'];
$cl["page_desc"]   = $cl["config"]["description"];
$cl["page_kw"]     = $cl["config"]["keywords"];
$cl["pn"]          = "conversation";
$cl["sbr"]         = true;
$cl["sbl"]         = true;
$cl["can_contact"] = cl_can_direct_message($cl['interlocutor']['id']);
$cl["messages"]    = cl_get_conversation(array(
	'user_one'     => $me['id'],
	'user_two'     => $cl['interlocutor']['id']
));

if (not_empty($cl["messages"])) {
	$cl["messages"] = array_reverse($cl["messages"]);
}

$cl["http_res"] = cl_template("conversation/content");

