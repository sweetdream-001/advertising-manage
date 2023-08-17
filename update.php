<?php
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)						        	@
# @ Author_url 1: https://jooj.us                                           @
# @ Author_url 2: http://jooj.us/twitter-clone                              @
# @ Author E-mail: sales@jooj.us                                            @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2022 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

require_once("core/settings.php");

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

require_once("core/libs/DB/vendor/autoload.php");

$query        = $mysqli->query("SET NAMES utf8");
$set_charset  = $mysqli->set_charset('utf8mb4');
$set_charset  = $mysqli->query("SET collation_connection = utf8mb4_unicode_ci");
$db           = new MysqliDb($mysqli);
$curr_version = $db->where('name', 'version')->getOne('cl_configs');
$curr_theme   = $db->where('name', 'theme')->getOne('cl_configs');
$curr_version = (empty($curr_version['value'])) ? 0 : $curr_version['value'];
$curr_theme   = (empty($curr_theme['value'])) ? 'default' : $curr_theme['value'];
$new_version  = '1.3.3';
$update       = (version_compare($curr_version, $new_version) == -1);
$errors       = array();
$update_stat  = false;
$new_sql      = array();

if (isset($_POST['update'])) {
	try {
		foreach ($new_sql as $query) {
			mysqli_query($mysqli, $query);
		}

		$db = $db->where('name', 'version');
		$up = $db->update('cl_configs', array(
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
	} catch (Exception $e) {
		$errors[] = $e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Update - JOOJ Talk</title>
	<link rel="stylesheet" href="<?php echo ("$site_url/themes/$curr_theme/statics/css/libs/bootstrap-v4.0.0.min.css"); ?>">
	<script src="<?php echo ("$site_url/themes/$curr_theme/statics/js/libs/jquery-3.5.1.min.js"); ?>"></script>
	<style>
		html {
			scroll-behavior: smooth
		}

		body {
			min-width: 340px !important;
			overflow-x: hidden;
			background: #f5f5f5;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif
		}

		div.container {
			width: 100%;
			max-width: 100%;
			min-height: 100vh;
			overflow: auto;
			display: -webkit-inline-flex;
			display: -moz-inline-flex;
			display: -ms-inline-flex;
			display: -o-inline-flex;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			overflow: hidden
		}

		div.container div.container-inner {
			margin: 100px 0;
			padding: 20px;
			background: #fff;
			border-radius: 5px;
			box-shadow: rgba(0, 0, 0, .05) 0 1px 10px 0;
			border-top: 3px solid #3ba1f3;
			min-width: 650px;
			max-width: 750px;
			overflow: hidden
		}

		div.container div.container-inner h1 {
			font-size: 24px;
			line-height: 36px;
			color: #192a32;
			padding: 0;
			margin: 0 0 5px;
			text-align: center
		}

		div.container div.container-inner p {
			font-size: 16px;
			line-height: 1.6;
			color: #616161;
			text-align: center;
			opacity: .9
		}

		div.container div.container-inner h5 {
			font-size: 14px;
			line-height: 16px;
			color: #2d2f43;
			padding: 0;
			margin: 0 0 10px
		}

		div.container div.updates-list {
			display: block;
			margin-bottom: 20px;
			border: 2px solid #1ca1f3;
			padding: 20px;
			border-radius: 5px
		}

		div.container div.updates-list p {
			width: 100%;
			display: block;
			padding: 0;
			margin: 0 0 20px 0;
			font-size: 13px;
			line-height: 18px;
			text-align-last: left;
			color: #35c85f;
			font-weight: 500
		}

		div.container div.updates-list p:last-child {
			margin-bottom: 0
		}

		div.container div.updates-list p.bug {
			color: #f6546a
		}

		div.container div.container-inner h6 {
			color: #1fb4ef;
			font-size: 12px;
			line-height: 18px;
			font-weight: 400;
			margin-top: 15px;
			border-radius: 5px;
			border: 1px solid #1fb4ef;
			padding: 15px
		}

		div.container div.container-inner div.alert {
			color: #fff;
			font-size: 12px;
			line-height: 18px;
			font-weight: 400;
			margin-top: 15px;
			border-radius: 5px;
			border: 1px solid #3ba1f3;
			padding: 15px;
			background: #3ba1f3
		}

		div.container div.container-inner div.ctrls {
			width: 100%;
			overflow: hidden;
			margin-top: 20px
		}

		div.container div.container-inner div.ctrls a {
			text-decoration: none;
			line-height: 0;
			outline: 0
		}

		div.container div.container-inner div.ctrls button {
			border: 1px solid #3ba1f3;
			background: 0 0;
			color: #3ba1f3;
			font-size: 14px;
			line-height: 14px;
			padding: 15px 40px;
			margin-right: 15px;
			border-radius: 5px
		}

		div.container div.container-inner div.ctrls button.in-line {
			background: #3ba1f3;
			color: #fff
		}

		div.container div.container-inner div.script-up-to-date h4 {
			font-size: 13px;
			color: #3ba1f3;
			margin: 0;
			padding: 15px;
			border-radius: 5px;
			border: 1px solid #3ba1f3;
			font-weight: 400
		}

		div.container div.container-inner div.update-completed h4 {
			font-size: 13px;
			color: #35c85f;
			margin: 0;
			padding: 15px;
			border-radius: 5px;
			border: 1px solid #35c85f;
			font-weight: 400
		}

		div.container div.container-inner div.update-completed h4 a {
			font-size: inherit;
			color: #003569;
			text-decoration: underline
		}

		div.container div.container-inner div.errors-list {
			width: 100%;
			overflow: hidden;
			border: 1px solid #3ba1f3;
			padding: 15px;
			border-radius: 5px;
			width: 100%;
			overflow: hidden;
			border: 1px solid #3ba1f3;
			padding: 15px;
			border-radius: 5px
		}

		div.container div.container-inner div.errors-list ol {
			padding: 0 0 0 15px
		}

		div.container div.container-inner div.errors-list ol li {
			border-bottom: none;
			border-top: none;
			color: #3ba1f3
		}
	</style>
	<script>
		jQuery(document).ready(function($) {
			$('[data-section-block="script-update"]').find('button[data-section-node="update"]').on('click', function(event) {
				event.preventDefault();
				if ($(this).data('status') == 'updating') {
					alert('Please wait for the update process to complete!');
					return false;
				} else {
					$(this).text('Updating, Please wait....');
					$(this).data('status', 'updating');
					$(this).parents().find('form[data-section-node="submit-form"]').trigger('submit');
				}
			});
		});
	</script>
</head>

<body>
	<div class="container" data-section-block="script-update">
		<div class="container-inner">
			<h1 class="text-center">Update to v<?php echo ($new_version); ?></h1>
			<p>
				Welcome to the new version!
				The new version of JOOJ Talk will make your work more<br> convenient, safe, high-quality and fast.
			</p>
			<?php if ($update == true && $update_stat == false) : ?>
				<h5>
					Change log of [<?php echo ($new_version); ?>]:
				</h5>

				<div class="updates-list">
					<p class="bug">- [Fixed] Bug on swift description with large text</p>
					<p class="bug">- [Fixed] Bug on modal windows scrollbar</p>
					<p class="bug">- [Fixed] Bug on affiliate page placeholder</p>
					<p class="bug">- [Fixed] Bug on advertising page placeholder</p>
					<p class="bug">- [Fixed] Bug on post likes display modal window placeholder text</p>
					<p class="bug">- [Fixed] Bug with a verification icon on posts in the mobile browser</p>
					<p class="bug">- [Fixed] Bug with links in messages</p>
					<p class="bug">- [Fixed] Other white minor bugs</p>
				</div>
				<div class="alert alert-danger">
					<b>Attention:</b>
					Make sure that you read and follow all the steps listed in the documentation for updating scripts, otherwise the process will result in an error and new updates will not be installed correctly.
				</div>
				<h6>
					If you are not sure that you are installing the latest updates, then click on the link below to «Download» the latest version; otherwise, click «Update» to apply new changes in the version [v<?php echo ($new_version); ?>]
				</h6>
				<div class="ctrls">
					<form method="post" data-section-node="submit-form" action="<?php echo ($site_url); ?>/update.php">

						<button type="submit" class="in-line" data-section-node="update" data-status="none">
							Update to [v<?php echo ($new_version); ?>]
						</button>
						<input type="hidden" name="update" value="1">
					</form>
				</div>
			<?php else : ?>
				<?php if (empty($errors) != true) : ?>
					<div class="errors-list">
						<ol>
							<?php foreach ($errors as $err) : ?>
								<li>
									<?php echo ($err); ?>
								</li>
							<?php endforeach; ?>
						</ol>
					</div>
				<?php else : ?>
					<?php if ($update_stat == true) : ?>
						<div class="update-completed">
							<h4>
								JOOJ Talk has been updated to version [v<?php echo ($new_version); ?>].
								<a href="<?php echo ($site_url); ?>">
									Click here to check new updates
								</a>
							</h4>
						</div>
					<?php else : ?>
						<div class="script-up-to-date">
							<h4>
								JOOJ Talk - The current version <b>[v<?php echo ($new_version); ?>]</b> is up to date!
							</h4>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</body>

</html>