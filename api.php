<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)							@
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                    @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

header('Content-Type: application/json');

$data = array();
$api  = ((isset($_GET["api"])) ? $_GET["api"] : "");
$app  = ((isset($_GET["app"])) ? $_GET["app"] : "");

if ($api == "native") {
	require_once("core/web_req_init.php");
	set_error_handler('cl_json_server500_err');

	$csrf     = false;
	$action   = ((not_empty($_GET["action"])) ? $_GET["action"] : "");
	$app_stat = fetch_or_get($applications[$app], false);

	if ($app_stat == true) {
		$req_handler = cl_strf("apps/native/ajax/%s/content.php",$app);
		$errors      = array();
		$hash        = ((not_empty($_GET["hash"])) ? $_GET["hash"] : "");

		if (empty($hash)) {
		    $hash = ((not_empty($_POST["hash"])) ? $_POST["hash"] : "");
		}

		if ($csrf) {
			if (empty($hash) || empty(cl_verify_csrf_token($hash))) {
		        $data          =  array(
		            "status"   => "400",
		            "err_code" => "invalid_csrf_token",
		            "message"  => "ERROR: Invalid or missing CSRF token"
		        );

		        echo json_encode($data, JSON_PRETTY_PRINT);
		        exit();
		    }
	    }

		require_once(cl_full_path($req_handler));

		echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
		mysqli_close($mysqli);
		unset($cl);
		exit();
	}
	else {
	    cl_json_server500_err(false, "Error: Handler for request not found");
	}
}

else {
	require_once("core/api_req_init.php");
	set_error_handler('cl_json_server500_err');

	$req_handler = cl_strf("apps/native/api/%s/content.php", $app);
	$errors      = array();
	$hash        = ((not_empty($_GET["hash"])) ? $_GET["hash"] : "");

	if ($cl["config"]["system_api_status"] == "on") {
		if (file_exists(cl_full_path($req_handler))) {
			require_once(cl_full_path($req_handler));
		}
		else {
			$data          =  array(
	            "status"   => "400",
	            "err_code" => "invalid_endpoint",
	            "message"  => "Invalid endpoint error on API call"
	        );
		}

		echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
		mysqli_close($mysqli);
		unset($cl);
		exit();
	}

	else{
		$data          =  array(
            "status"   => "400",
            "err_code" => "api_is_disabled",
            "message"  => cl_strf("Unfortunately, the system API of %s is temporary not available.", $cl["config"]["name"])
        );

        echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        exit();
	}
}

