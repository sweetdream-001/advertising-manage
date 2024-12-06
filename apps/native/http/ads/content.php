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
	cl_redirect("guest");
}

else {

	require_once(cl_full_path("core/apps/ads/app_ctrl.php"));

	$cl["page_title"] = cl_translate('Advertisement');
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "ads";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;

	if ($cl['config']['advertising_system'] == 'off') {
		$cl["http_res"] = cl_template("ads/includes/systemoff");
	}
	else {
		$cl["page_tab"] = fetch_or_get($_GET['page'], 'active');
		$cl["ads_ls"]   = array();  

		if (in_array($cl["page_tab"], array('upsert', 'edit'))) {
			if (isset($_GET['ad_id'])) {
				$ad_id              = fetch_or_get($_GET['ad_id'], 0);
				$cl['countries_ls'] = array();
				$cl['ad_data']      = cl_get_or_create_orphan_ad($ad_id);
				$cl['ad_data']      = cl_ad_vue_preprocess($cl['ad_data']);

				foreach ($cl['countries'] as $k => $v) {
					$cl['countries_ls'][$k] = cl_translate($v);
				}
			} else {
				$insert_data = [
					'user_id' => $me['id'],
					'status'  => 'orphan',
					'time'    => time()
				];
				$ad_id = cl_insert_ad_data($insert_data);
				$cl['countries_ls'] = array();
				$cl['ad_data']      = cl_get_or_create_orphan_ad($ad_id);
				$cl['ad_data']      = cl_ad_vue_preprocess($cl['ad_data']);

				foreach ($cl['countries'] as $k => $v) {
					$cl['countries_ls'][$k] = cl_translate($v);
				}
			}
		}

		else if($cl["page_tab"] == 'active') {
			$cl["ads_ls"] = cl_get_user_ads(array(
				'type' => 'active'
			));
		}

		else if($cl["page_tab"] == 'archive') {
			$cl["ads_ls"] = cl_get_user_ads(array(
				'type' => 'inactive'
			));
		}
		
		else if($cl["page_tab"] == 'pending') {
			$cl["ads_ls"] = cl_get_user_ads(array(
				'type' => 'pending'
			));
		}

		$cl["http_res"] = cl_template("ads/content");
	}
}
