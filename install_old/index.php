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

header ("Access-Control-Allow-Origin: *");
ini_set('display_errors', 0);
session_start();
define("PROJ_RP", dirname(dirname(__FILE__)));
error_reporting(0);
ini_set("memory_limit", "-1");
set_time_limit(0);

$cl_tables = array('cl_bookmarks','cl_chats','cl_configs','cl_ui_langs','cl_connections','cl_invite_links','cl_verifications','cl_acc_validations', 'cl_pub_reports', 'cl_hashtags','cl_messages','cl_notifications','cl_posts','cl_publications','cl_publikes','cl_pubmedia','cl_sessions','cl_users', 'cl_profile_reports', 'cl_blocks', 'cl_affiliate_payouts', 'cl_ads', 'cl_wallet_history');
$page      = ((empty($_GET['p'])) ? 'terms' : strval($_GET['p']));

if (isset($_SESSION['init']) != true || is_array($_SESSION['init']) != true) {
	$_SESSION['init'] = array();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Installation - JOOJ Talk Social Media Platform</title>
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/icofont.css">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/themify.css">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/flag-icon.css">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/feather-icon.css">
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/animate.css">
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/bootstrap.css">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link id="color" rel="stylesheet" href="assets/css/color-1.css" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
	<script><?php require_once('content/js/jquery.min.js'); ?></script>
	<script><?php require_once('content/js/popper.min.js'); ?></script>
	<script><?php require_once('content/js/bootstrap.min.js'); ?></script>
	<script><?php require_once('content/js/install.master.script.js'); ?></script>

	<style><?php require_once('content/css/bootstrap.min.css'); ?></style>
	<style><?php require_once('content/css/install.master.style.css'); ?></style>

	<!-- 
		Please note that all the contents of the installation folder are only needed to install the script, and will be deleted after the script is installed.
		Due to the fact that this script has not yet defined URLs, static files are connected using php
	-->
</head>
<body>
<style>

.action-bar {
    display: none;
}
div.main-cont {
    margin-top: 10px!important;
    
}
body div.main-cont {
    padding-top: 10px!important;
}
</style>

					<?php if ($page == 'requirements'): ?>
						<?php require_once('content/temps/requirements.phtml'); ?>
					<?php elseif($page == 'installation' && isset($_SESSION['init']['reqs']) && empty($_SESSION['init']['reqs'])): ?>
						<?php require_once('content/temps/installation.phtml'); ?>
					<?php else: ?>
						<?php require_once('content/temps/terms.phtml'); ?>
					<?php endif; ?>
					<div class="main-cont-footer" style="margin-top: 30px;">
						<span>
							&copy; JOOJ Talk <?php echo date('Y'); ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
    	</div>
        </div>
         </div>
         	</div>
        </div>
         </div>
     <script src="assets/js/jquery-3.5.1.min.js"></script>
      <!-- Bootstrap js-->
      <script src="assets/js/bootstrap/bootstrap.bundle.min.js"></script>
      <!-- feather icon js-->
      <script src="assets/js/icons/feather-icon/feather.min.js"></script>
      <script src="assets/js/icons/feather-icon/feather-icon.js"></script>
      <!-- scrollbar js-->
      <!-- Sidebar jquery-->
      <script src="assets/js/config.js"></script>
      <!-- Plugins JS start-->
      <script src="assets/js/form-wizard/form-wizard-five.js"></script>
      <script src="assets/js/tooltip-init.js"></script>
      <!-- Plugins JS Ends-->
      <!-- Theme js-->
      <script src="assets/js/script.js"></script>
      
</body>
</html>