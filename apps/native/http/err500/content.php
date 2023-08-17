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

$cl["page_title"] = cl_translate('Server error 500!');
$cl["page_desc"]  = $cl["config"]["description"];
$cl["page_kw"]    = $cl["config"]["keywords"];
$cl["pn"]         = "err500";
$cl["sbr"]        = true;
$cl["sbl"]        = true;
$cl["err_msg"]    = cl_session('err500_message');

if ($cl["err_msg"]) {
	cl_session_unset('err500_message');
}

$cl["http_res"] = cl_template("err500/content");
