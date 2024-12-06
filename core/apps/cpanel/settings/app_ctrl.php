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

function cl_admin_save_config($key = "none", $val = "none") {
	global $db, $cl;
	
	
    if (in_array($key, array_keys($cl['config']))) {
        $db = $db->where('name', $key);
        $qr = $db->update(T_CONFIGS, array(
        	'value' => $val
        ));
    }
    else{
        return false;
    }
}