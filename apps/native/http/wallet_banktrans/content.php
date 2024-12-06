<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                           @
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                    @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

// RESTRICT USER CODE
$visitor_ip = $_SERVER['REMOTE_ADDR'];
cl_check_ip_restriction($visitor_ip);

if (empty($cl["is_logged"])) {
	cl_redirect("guest");
}

$transfer_sid = fetch_or_get($_GET["sid"], false);

$bank_trans_session = cl_session("bank_trans_session");

if ($cl["config"]["bank_method_status"] != "on" || empty($transfer_sid) || empty($bank_trans_session) || $bank_trans_session["sess_id"] != $transfer_sid) {
	cl_redirect("404");
}

else {
	$cl["page_title"] = cl_translate("Bank transfer");
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "wallet_banktrans";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["http_res"]   = cl_template("wallet_banktrans/content");
}