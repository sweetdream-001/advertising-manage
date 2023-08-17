<?php 
# @*************************************************************************@
# @ @author JOOJ Team (JOOJ.us)						            			@
# @ @author_url 1: https://jooj.us                                          @
# @ @author_url 2: http://jooj.us/twitter-clone                             @
# @ @author_email: sales@jooj.us                                            @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 27.07.2020 JOOJ Talk. All rights reserved.                @
# @*************************************************************************@

require_once(cl_full_path('core/libs/paypal/vendor/autoload.php'));

$paypal_conf = array(
 	"secret_key"      => $cl['config']['paypal_api_key'],
 	"publishable_key" => $cl['config']['paypal_api_pass']
);

$paypal_creds = new \PayPal\Auth\OAuthTokenCredential($cl['config']['paypal_api_key'], $cl['config']['paypal_api_pass']);
$paypal       = new \PayPal\Rest\ApiContext($paypal_creds);

$paypal->setConfig(array(
	'mode' => $cl['config']['paypal_mode']
));

