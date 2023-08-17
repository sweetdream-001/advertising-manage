<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)					        	   	@
# @ Author_url 1: https://jooj.us                                           @
# @ Author_url 2: http://jooj.us/twitter-clone                              @
# @ Author E-mail: sales@jooj.us                                            @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

require_once("../core/settings.php");

$sql_db_host   = (isset($sql_db_host) ? $sql_db_host : "");
$sql_db_user   = (isset($sql_db_user) ? $sql_db_user : "");
$sql_db_pass   = (isset($sql_db_pass) ? $sql_db_pass : "");
$sql_db_name   = (isset($sql_db_name) ? $sql_db_name : "");
$site_url      = (isset($site_url)    ? $site_url    : "");
$mysqli        = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
$server_errors = array();

if (mysqli_connect_errno()) {
    $server_errors[] = "Error: Failed to connect to MySQL Server: " . mysqli_connect_error();
}

if (empty($server_errors) != true) {
    foreach ($server_errors as $serv_error) {
        echo "<h3>{$serv_error}</h3>";
    }

    die();
}

require_once("../core/libs/DB/vendor/autoload.php");

$query        = $mysqli->query("SET NAMES utf8");
$set_charset  = $mysqli->set_charset('utf8mb4');
$set_charset  = $mysqli->query("SET collation_connection = utf8mb4_unicode_ci");
$db           = new MysqliDb($mysqli);
$curr_version = $db->where('name', 'version')->getOne('cl_configs');
$curr_theme   = $db->where('name', 'theme')->getOne('cl_configs');
$curr_version = (empty($curr_version['value'])) ? 0 : $curr_version['value'];
$curr_theme   = (empty($curr_theme['value'])) ? 'default' : $curr_theme['value'];
$new_version  = '1.3.4';
$update       = (version_compare($curr_version, $new_version) == -1);
$errors       = array();
$update_stat  = false;
$new_sql      = array(
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Timezone', 'timezone', 'UTC', '');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Bank transfer gateway', 'bank_method_status', 'on', '/^(on|off)$/');",
	"CREATE TABLE `cl_banktrans_requests` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `user_id` INT(11) NOT NULL ,  `amount` VARCHAR(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0.00' ,  `receipt_img` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL ,  `currency` VARCHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'USD' ,  `time` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;",
	"ALTER TABLE `cl_banktrans_requests` ADD `message` VARCHAR(1210) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `receipt_img`;",
	"ALTER TABLE `cl_wallet_history` ADD `status` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'success' AFTER `time`;",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Bank account number', 'bt_bank_account_number', '', ''), (NULL, 'Routing code', 'bt_bank_routing_code', '', '');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Bank account name', 'bt_bank_account_name', '', ''), (NULL, 'Bank country', 'bt_bank_country_name', '1', ''), (NULL, 'Bank address', 'bt_bank_address', '', ''), (NULL, 'Bank name', 'bt_bank_name', '', ''), (NULL, 'Bank SVG Logo', 'bt_bank_svg_logo', '', '');",
	"ALTER TABLE `cl_wallet_history` ADD `trans_id` VARCHAR(130) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `status`;",
	"ALTER TABLE `cl_banktrans_requests` ADD `trans_id` VARCHAR(130) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `currency`;",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'System API status', 'system_api_status', 'on', '/^(on|off)$/');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Guest page status', 'guest_page_status', 'on', '/^(on|off)$/');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Username restrictions', 'username_restrictions', '', ''), (NULL, 'User email restrictions', 'useremail_restrictions', '', '');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Post video download system', 'post_video_download_system', 'on', '/^(on|off)$/');",
	"ALTER TABLE `cl_publications` CHANGE `type` `type` ENUM('text','video','image','gif','poll','audio','document') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'text';",
	"ALTER TABLE `cl_pubmedia` CHANGE `type` `type` ENUM('image','video','gif','audio','document') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'RazorPay payment method status', 'rzp_method_status', 'on', '/^(on|off)$/'), (NULL, 'RazorPay API Public key', 'rzp_api_key', '', ''), (NULL, 'RazorPay API Secret key', 'rzp_api_secret', '', '');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'LinkedIN oAuth status', 'linkedin_oauth', 'on', '/^(on|off)$/'), (NULL, 'LinkedIn API ID', 'linkedin_api_id', '', ''), (NULL, 'LinkedIn API Key', 'linkedin_api_key', '', '');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Discord oAuth status', 'discord_status', 'on', '/^(on|off)$/'), (NULL, 'Discord API ID', 'discord_api_id', '', ''), (NULL, 'Discord API Key', 'discord_api_key', '', '');",
	"ALTER TABLE `cl_users` ADD `logged_in_with` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'system' AFTER `joined`;",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Vkontakte oAuth status', 'vkontakte_status', 'on', '/^(on|off)$/'), (NULL, 'Vkontakte API ID', 'vkontakte_api_id', '', ''), (NULL, 'Vkontakte API Key', 'vkontakte_api_key', '', '');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Instagram oAuth status', 'instagram_status', 'on', '/^(on|off)$/'), (NULL, 'Instagram API ID', 'instagram_api_id', '', ''), (NULL, 'Instagram API Key', 'instagram_api_key', '', '');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Premium account system status', 'prem_account_system_status', 'off', '/^(on|off)$/');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Premium account m/price', 'premium_account_mprice', '0.00', '/^([0-9]{1,11}\\.[0-9]{1,11}|[0-9]{1,11})$/');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Premium account y/price', 'premium_account_yprice', '0.00', '/^([0-9]{1,11}\\.[0-9]{1,11}|[0-9]{1,11})$/');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Blue account y/price', 'blue_account_yprice', '0.00', '/^([0-9]{1,11}\\.[0-9]{1,11}|[0-9]{1,11})$/');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Max post length blue', 'max_post_len_blue', '10000', '/^[0-9]{1,11}$/');",
	"ALTER TABLE `cl_users` ADD `is_premium` ENUM('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' AFTER `info_file`;",
	"ALTER TABLE `cl_users` ADD `premium_ex_date` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' AFTER `is_premium`;",
	"ALTER TABLE `cl_users` ADD `premium_settings` VARCHAR(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '{\"disable_native_ads\": 0,\"disable_adsense_ads\": 0}' AFTER `is_premium`;",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Blue account system status', 'blue_account_system_status', 'off', '/^(on|off)$/');",
	"INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Blue account m/price', 'blue_account_mprice', '0.00', '/^([0-9]{1,11}\\.[0-9]{1,11}|[0-9]{1,11})$/');",
	"ALTER TABLE `cl_users` ADD `is_blue` ENUM('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' AFTER `info_file`;",
	"ALTER TABLE `cl_users` ADD `blue_ex_date` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' AFTER `is_blue`;",
	"ALTER TABLE `cl_users` ADD `blue_settings` VARCHAR(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '{\"disable_native_ads\": 0,\"disable_adsense_ads\": 0}' AFTER `is_blue`;"

);

if (isset($_POST['update'])) {
	try {
		sleep(3);

		foreach ($new_sql as $query) {
			mysqli_query($mysqli, $query);
		}

		$db = $db->where('name','version');
        $up = $db->update('cl_configs',array(
            'value' => $new_version
        ));

	 	$update_stat = true;

	 	$users = $db->where("followers", 0, "<")->where("following", 0, "<", "OR")->get("cl_users");

		if (empty($users) != true) {
			foreach ($users as $user_data) {
				$db->where("id", $user_data["id"])->update("cl_users", array(
					"followers" => $db->where("following_id", $user_data["id"])->getValue("cl_connections", "COUNT(*)"),
					"following" => $db->where("follower_id", $user_data["id"])->getValue("cl_connections", "COUNT(*)")
				));
			}
		}
	} 

	catch (Exception $e) {
		$errors[] = $e->getMessage();
	} 
}

$update_changelog = array(
	"Added the ability to replenish the wallet by bank transfer",
	"Added the ability to cancel an affiliate withdrawal request",
	"Added the ability to transfer money earned through the affiliate program to the user's main wallet",
	"Added the ability to disable the API from the admin panel",
	"Added the ability to transfer money from a user's wallet to another user",
	"Added ability to disable guest page from admin",
	"Added placeholder for the video poster if there is no preview",
	"Added username restriction system from admin panel",
	"Added the ability to connect custom CSS and JS code to the site, for those who want to change something in the display",
	"Improved the login page for a new improved layout",
	"Improved the input tag of the message entry form to Textarea so that the user can type on a new line",
	"Added a new player for videos https://plyr.io/",
	"Added the ability to leave the last name field empty, that is, make it optional",
	"Added the ability to download videos. This feature can be disabled or enabled by the admin",
	"Added a new player to play audio files",
	"Added the ability to upload audio files",
	"Added the ability to upload document files",
	"Added Indian bank RazorPay",
	"Added the ability to login through other social networks like Vkontakte, Instagram, Discord, LinkedIn",
	"Added a premium system that allows you to disable ads (And many upcoming features that will be released soon) for a paid subscription",
	"Added ability for admin to delete user account from their profile page dropdown menu",
	"Added ability for admin to block user account from their profile page",
	"Added RTL languages support",
	"Fixed Bug on swift description with large text",
	"Fixed the bug with the translation of the names of months in dates",
	"Fixed bugs with sending mail that caused mail clients like Google to mark emails as spam",
	"Fixed problem with displaying links from social networks and websites",
	"Fixed Google Ads bug with dark theme"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Update JOOJ Talk Script</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<script src="<?php echo $site_url; ?>/update/assets/js/jquery-3.5.1.min.js"></script>
	<script src="<?php echo $site_url; ?>/update/assets/js/master.script.js"></script>

	<link rel="stylesheet" href="<?php echo $site_url; ?>/update/assets/css/master.style.css">
	<link rel="icon" href="<?php echo $site_url; ?>/update/assets/img/favicon.png" type="image/png">
	<link rel="icon" href="<?php echo $site_url; ?>/update/assets/img/favicon.png" type="image/x-icon">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
	<div class="container" data-section-block="script-update">	
		<div class="container__header">
			<div class="page-offset">
				<div class="page-offset__inner">
					<div class="logo">
						<img src="<?php echo $site_url; ?>/update/assets/img/talk-logo.png" alt="Logo">
					</div>

					<h1>Update Your JOOJ Talk Script to <span>v<?php echo($new_version); ?></span></h1>
					<p>
						Welcome to the new JOOJ Talk script version. The new version of JOOJ Talk will make your work more convenient, safe, high-quality and fast.
					</p>
					
				</div>
			</div>
		</div>

		<?php if ($update == true && $update_stat == false): ?>
			<div class="container__body">
				<div class="page-offset">
					<div class="page-offset__inner">
						<h5 class="updates-title">
							Changelogs of v[<?php echo($new_version); ?>] JOOJ Talk script:
						</h5>
						
						<div class="updates-list">

							<?php foreach ($update_changelog as $ind => $log): ?>
								<p>
									<strong>[<?php if($ind <= 8) {echo "0" . ($ind += 1);} else {echo ($ind += 1);} ?>]</strong> <?php echo $log; ?>
								</p>
							<?php endforeach; ?>
						</div>

						<div class="updates-alert">
							<div class="updates-alert__icon">
								<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.99L19.53 19H4.47L12 5.99M12 2L1 21h22L12 2zm1 14h-2v2h2v-2zm0-6h-2v4h2v-4z"/></svg>
							</div>
							<div class="updates-alert__body">
								<b>Attention:</b>
								Make sure that you read and follow all the steps listed in the documentation for updating scripts, otherwise the process will result in an error and new updates will not be installed correctly.
							</div>
		                </div>
						<div class="updates-ctrls">
							<form method="post" data-section-node="submit-form" action="<?php echo($site_url); ?>/update/index.php">
								<button class="updates-primary-btn" type="submit" data-section-node="update" data-status="none">
									Install updates of [v<?php echo($new_version); ?>] version
								</button>
								<input type="hidden" name="update" value="1">
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php else: ?>
			<div class="container__body">
				<div class="page-offset">
					<div class="page-offset__inner">
						<?php if (empty($errors) != true): ?>
							<div class="updates-message">
								<h5 class="col-danger">
									Update installation failed :(
								</h5>
								<h6>
									Please check the following error messages:
								</h6>
								<ol>
									<?php foreach ($errors as $err): ?>
										<li>
											<?php echo($err); ?>
										</li>
									<?php endforeach; ?>
								</ol>
							</div>
						<?php else: ?>
							<?php if ($update_stat == true): ?>
								<div class="updates-message">
									<h5 class="col-success">
										Updates installed successfully :)
									</h5>
									<p>
										Your site has been successfully updated [v<?php echo($new_version); ?>]. <br><br> Now you can go to the control panel or to your account and start using the new features of your web application.
										You can view the entire list and changelogs of updates at any time in the <strong>Changelogs</strong> page of your website admin panel
									</p>

									<a class="updates-primary-btn-wrapper" href="<?php echo $site_url; ?>">
										<div class="updates-primary-btn btn-success" type="submit" data-section-node="update" data-status="none">
											Click here to check new updates
										</div>
									</a>	
								</div>
							<?php else: ?>
								<div class="updates-message">
									<h5>
										You are up to date!
									</h5>
									<p>
										JOOJ Talk Script v<?php echo($new_version); ?>  is currently the newest version available. <br><br>

										If you have any questions about this or you do not understand something, then feel free to contact our support team at this email address: <a href="mailto:info@jooj.us">info@jooj.us</a>
									</p>

									<a class="updates-primary-btn-wrapper" href="<?php echo $site_url; ?>">
										<div class="updates-primary-btn" type="submit" data-section-node="update" data-status="none">
											Ok, Launch the application
										</div>
									</a>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</body>
</html>

