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

$cl["page_title"] = cl_translate("Hot topics");
$cl["page_desc"]  = $cl["config"]["description"];
$cl["page_kw"]    = $cl["config"]["keywords"];
$cl["pn"]         = "trending";
$cl["sbr"]        = true;
$cl["sbl"]        = true;
$cl["htags"]      = cl_get_hot_topics(30);
$cl["http_res"]   = cl_template("trending/content");
