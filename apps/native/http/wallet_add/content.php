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

else if (in_array("on", array($cl['config']['paypal_method_status'], $cl['config']['rzp_method_status'], $cl['config']['paystack_method_status'], $cl['config']['stripe_method_status'], $cl['config']['bank_method_status'])) != true) {
	cl_redirect("404");
}

else {

	if ($cl['config']['stripe_method_status'] == 'on') {	
		$cl["app_statics"] = array(
			"scripts" => array(
				cl_js_template("statics/js/libs/Stripe/stripe")
			)
		);
	}

	if ($cl['config']['rzp_method_status'] == 'on') {	
		$cl["app_statics"] = array(
			"scripts" => array(
				cl_js_template("statics/js/libs/Razorpay/checkout")
			)
		);
	}

	$cl["page_title"] = cl_translate('Replenish wallet');
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "wallet_add";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["http_res"]   = cl_template("wallet_add/content");
}