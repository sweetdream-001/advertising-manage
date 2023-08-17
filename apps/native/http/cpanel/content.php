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

if (empty($cl["is_logged"])) {
	cl_redirect("404");
}

else if(empty($cl['is_admin'])) {
	cl_redirect("404");
}

$cl["page_title"] = cl_translate("Control panel");
$cl["page_desc"]  = $cl["config"]["description"];
$cl["page_kw"]    = $cl["config"]["keywords"];
$cl["pn"]         = "cpanel";
$cl["cp_section"] = fetch_or_get($_GET['section'], 'dashboard');
$section_handler  = cl_full_path(cl_strf('apps/native/http/cpanel/sections/%s.php', $cl["cp_section"]));

if (file_exists($section_handler)) {
	require_once($section_handler);

	echo cl_template("cpanel/content");
	exit();
}
else {
	cl_redirect("404");
}

