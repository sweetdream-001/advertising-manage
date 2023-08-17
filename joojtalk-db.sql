{%begin%}
  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
{%end%}

{%begin%}
  SET time_zone = "+00:00";
{%end%}

{%begin%}
  CREATE TABLE `cl_bookmarks` (
    `id` int(11) NOT NULL,
    `publication_id` int(11) NOT NULL DEFAULT '0',
    `user_id` int(11) NOT NULL DEFAULT '0',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_chats` (
    `id` int(11) NOT NULL,
    `user_one` int(11) NOT NULL DEFAULT '0',
    `user_two` int(11) NOT NULL DEFAULT '0',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_configs` (
    `id` int(11) NOT NULL,
    `title` varchar(120) NOT NULL DEFAULT '',
    `name` varchar(120) NOT NULL DEFAULT '',
    `value` varchar(30000) NOT NULL DEFAULT '',
    `regex` varchar(120) NOT NULL DEFAULT ''
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES
    (1, 'Theme', 'theme', 'default', ''),
    (2, 'Site name', 'name', 'Talk', '/^(.){0,50}$/'),
    (3, 'Site title', 'title', 'JOOJ Talk Social Media', '/^(.){0,150}$/'),
    (4, 'Site description', 'description', 'JOOJ Talk - The Ultimate Modern Social Media Sharing Platform', '/^(.){0,350}$/'),
    (5, 'SEO keywords', 'keywords', 'social,socialnetwork', ''),
    (6, 'Site logo', 'site_logo', 'statics/img/logo.png', ''),
    (7, 'Site favicon', 'site_favicon', 'statics/img/favicon.png', ''),
    (8, 'Chat wallpaper', 'chat_wp', 'statics/img/chatwp/default.png', ''),
    (9, 'Account activation', 'acc_validation', 'off', '/^(on|off)$/'),
    (10, 'Default language', 'language', 'english', ''),
    (11, 'AS3 storage', 'as3_storage', 'off', '/^(on|off)$/'),
    (12, 'E-mail address', 'email', '', ''),
    (13, 'SMTP server', 'smtp_or_mail', 'smtp', '/^(smtp|mail)$/'),
    (14, 'SMTP host', 'smtp_host', '', ''),
    (15, 'SMTP password', 'smtp_password', '', '/^(.){0,50}$/'),
    (16, 'SMTP encryption', 'smtp_encryption', 'tls', '/^(ssl|tls)$/'),
    (17, 'SMTP port', 'smtp_port', '587', '/^[0-9]{1,11}$/'),
    (18, 'SMTP username', 'smtp_username', '', ''),
    (19, 'FFMPEG binary', 'ffmpeg_binary', 'core/libs/ffmpeg/ffmpeg', '/^(.){0,550}$/'),
    (20, 'Giphy api', 'giphy_api_key', 'EEoFiCosGuyEIWlXnRuw4McTLxfjCrl1', '/^(.){0,150}$/'),
    (21, 'Google analytics', 'google_analytics', '', ''),
    (22, 'Facebook API ID', 'facebook_api_id', '', '/^(.){0,150}$/'),
    (23, 'Facebook API Key', 'facebook_api_key', '', '/^(.){0,150}$/'),
    (24, 'Twitter API ID', 'twitter_api_id', '', '/^(.){0,150}$/'),
    (25, 'Twitter API Key', 'twitter_api_key', '', '/^(.){0,150}$/'),
    (26, 'Google API ID', 'google_api_id', '', '/^(.){0,150}$/'),
    (27, 'Google API Key', 'google_api_key', '', '/^(.){0,150}$/'),
    (28, 'Script version', 'version', '1.3.4', ''),
    (29, 'Last backup', 'last_backup', '0', '');
{%end%}

{%begin%}
  CREATE TABLE `cl_connections` (
    `id` int(11) NOT NULL,
    `follower_id` int(11) NOT NULL DEFAULT '0',
    `following_id` int(11) NOT NULL DEFAULT '0',
    `time` varchar(25) NOT NULL DEFAULT '25'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_hashtags` (
    `id` int(11) NOT NULL,
    `posts` int(11) NOT NULL DEFAULT '0',
    `tag` varchar(200) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_messages` (
    `id` int(11) NOT NULL,
    `sent_by` int(11) NOT NULL DEFAULT '0',
    `sent_to` int(11) NOT NULL DEFAULT '0',
    `owner` int(11) NOT NULL DEFAULT '0',
    `message` varchar(3000) NOT NULL DEFAULT '',
    `media_file` varchar(1000) NOT NULL DEFAULT '',
    `media_type` varchar(25) NOT NULL DEFAULT 'none',
    `seen` varchar(25) NOT NULL DEFAULT '0',
    `deleted_fs1` enum('Y','N') NOT NULL DEFAULT 'N',
    `deleted_fs2` enum('Y','N') NOT NULL DEFAULT 'N',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_notifications` (
    `id` int(11) NOT NULL,
    `notifier_id` int(11) NOT NULL DEFAULT '0',
    `recipient_id` int(11) NOT NULL DEFAULT '0',
    `status` enum('0','1') NOT NULL DEFAULT '0',
    `subject` varchar(32) NOT NULL DEFAULT 'none',
    `entry_id` int(11) NOT NULL DEFAULT '0',
    `json` varchar(1200) NOT NULL DEFAULT '[]',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_posts` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL DEFAULT '0',
    `publication_id` int(11) NOT NULL DEFAULT '0',
    `type` enum('post','repost') NOT NULL DEFAULT 'post',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_publications` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL DEFAULT '0',
    `text` varchar(600) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
    `type` enum('text','video','image','gif') NOT NULL DEFAULT 'text',
    `replys_count` int(11) NOT NULL DEFAULT '0',
    `reposts_count` int(11) NOT NULL DEFAULT '0',
    `likes_count` int(11) NOT NULL DEFAULT '0',
    `status` enum('active','inactive','deleted','orphan') NOT NULL DEFAULT 'active',
    `thread_id` int(11) NOT NULL DEFAULT '0',
    `target` enum('publication','pub_reply') NOT NULL DEFAULT 'publication',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_publikes` (
    `id` int(11) NOT NULL,
    `pub_id` int(11) NOT NULL DEFAULT '0',
    `user_id` int(11) NOT NULL DEFAULT '0',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_pubmedia` (
    `id` int(11) NOT NULL,
    `pub_id` int(11) NOT NULL DEFAULT '0',
    `type` enum('image','video','gif') NOT NULL,
    `src` varchar(1200) NOT NULL DEFAULT '',
    `json_data` varchar(3000) NOT NULL DEFAULT '[]',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_sessions` (
    `id` int(11) NOT NULL,
    `session_id` varchar(120) NOT NULL DEFAULT '',
    `user_id` varchar(11) NOT NULL DEFAULT '0',
    `platform` varchar(15) NOT NULL DEFAULT 'web',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_users` (
    `id` int(11) NOT NULL,
    `username` varchar(30) NOT NULL DEFAULT '',
    `fname` varchar(30) NOT NULL DEFAULT '',
    `lname` varchar(30) NOT NULL DEFAULT '',
    `about` varchar(150) NOT NULL DEFAULT '',
    `gender` enum('M','F') NOT NULL DEFAULT 'M',
    `email` varchar(60) NOT NULL DEFAULT '',
    `em_code` varchar(100) NOT NULL DEFAULT '',
    `password` varchar(140) NOT NULL DEFAULT '',
    `joined` varchar(20) NOT NULL DEFAULT '0',
    `last_active` varchar(20) NOT NULL DEFAULT '0',
    `ip_address` varchar(140) NOT NULL DEFAULT '0.0.0.0',
    `language` varchar(32) NOT NULL DEFAULT 'default',
    `avatar` varchar(300) NOT NULL DEFAULT 'upload/default/avatar.png',
    `cover` varchar(300) NOT NULL DEFAULT 'upload/default/cover.png',
    `cover_orig` varchar(300) NOT NULL DEFAULT '',
    `active` enum('0','1') NOT NULL DEFAULT '0',
    `admin` enum('0','1') NOT NULL DEFAULT '0',
    `posts` int(11) NOT NULL DEFAULT '0',
    `followers` int(11) NOT NULL DEFAULT '0',
    `following` int(11) NOT NULL DEFAULT '0',
    `website` varchar(120) NOT NULL DEFAULT '',
    `country_id` int(3) NOT NULL DEFAULT '1',
    `last_post` int(11) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  ALTER TABLE `cl_bookmarks`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_chats`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_configs`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_connections`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_hashtags`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_messages`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_notifications`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_posts`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_publications`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_publikes`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_pubmedia`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_sessions`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_users`
    ADD PRIMARY KEY (`id`),
    ADD KEY `posts` (`posts`);
{%end%}

{%begin%}
  ALTER TABLE `cl_bookmarks`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_chats`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_configs`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_connections`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_hashtags`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_messages`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_notifications`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_posts`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_publications`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_publikes`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_pubmedia`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_sessions`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  CREATE TABLE `cl_verifications` ( `id` INT(11) NOT NULL AUTO_INCREMENT,  
    `user_id` INT(11) NOT NULL DEFAULT '0',  
    `full_name` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',  
    `text_message` VARCHAR(1200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',  
    `video_message` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',  
    `status` ENUM('pending','rejected') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pending',  
    `time` INT(25) NOT NULL DEFAULT '0',    
  PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `verified` ENUM('0','1') 
    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `active`;
{%end%}

{%begin%}
  ALTER TABLE `cl_verifications` DROP `status`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `verified` `verified` ENUM('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `profile_privacy` ENUM('everyone','followers','nobody') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'everyone' AFTER `last_post`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `contact_privacy` ENUM('everyone','followed') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'everyone' AFTER `profile_privacy`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `index_privacy` ENUM('Y','N') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Y' AFTER `contact_privacy`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Sitemap last update', 'sitemap_update', '', '');
{%end%}

{%begin%}
  ALTER TABLE `cl_messages` CHANGE `message` `message` VARCHAR(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `about` `about` VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `active` `active` ENUM('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';
{%end%}

{%begin%}
  CREATE TABLE `cl_profile_reports` ( `id` INT(11) NOT NULL AUTO_INCREMENT, 
    `user_id` INT(11) NOT NULL DEFAULT '0', 
    `profile_id` INT(11) NOT NULL DEFAULT '0', 
    `reason` INT(3) NOT NULL DEFAULT '0',  
    `seen` ENUM('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',  
    `time` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',  
    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_profile_reports` ADD `comment` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `reason`;
{%end%}

{%begin%}
  CREATE TABLE `cl_blocks` ( `id` int(11) NOT NULL, `user_id` int(11) NOT NULL DEFAULT '0', `profile_id` int(11) NOT NULL DEFAULT '0', `time` varchar(25) NOT NULL DEFAULT '0' ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  ALTER TABLE `cl_blocks` ADD PRIMARY KEY(`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_blocks` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  CREATE TABLE `cl_affiliate_payouts` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `user_id` INT(11) NOT NULL DEFAULT '0' ,  `amount` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.00' ,  `bonuses` INT(11) NOT NULL DEFAULT '0' ,  `status` ENUM('pending','paid','declined') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pending' ,  `time` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `aff_bonuses` INT(11) NOT NULL DEFAULT '0' AFTER `index_privacy`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Affiliate bonus rate', 'aff_bonus_rate', '0.10', '/^([0-9]{1,3}\\.[0-9]{1,3}|[0-9]{1,3})$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Affiliates System', 'affiliates_system', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_affiliate_payouts` ADD `email` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_id`;
{%end%}

{%begin%}
  ALTER TABLE `cl_affiliate_payouts` CHANGE `status` `status` ENUM('pending','paid') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pending';
{%end%}

{%begin%}
  CREATE TABLE `cl_ads` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` int(11) NOT NULL DEFAULT '0', `company` varchar(120) NOT NULL DEFAULT '', `target_url` varchar(1200) NOT NULL DEFAULT '', `status` enum('orphan','active','inactive') NOT NULL DEFAULT 'orphan', `audience` varchar(3000) NOT NULL DEFAULT '0', `description` varchar(600) NOT NULL DEFAULT '', `cta` varchar(300) NOT NULL DEFAULT '', `budget` varchar(15) NOT NULL DEFAULT '0.00', `clicks` int(11) NOT NULL DEFAULT '0', `views` int(11) NOT NULL DEFAULT '0', `time` varchar(25) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `last_ad` INT(11) NOT NULL DEFAULT '0' AFTER `last_post`;
{%end%}

{%begin%}
  ALTER TABLE `cl_ads` CHANGE `audience` `audience` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '[]';
{%end%}

{%begin%}
  ALTER TABLE `cl_ads` ADD `cover` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_id`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `wallet` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.00' AFTER `aff_bonuses`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'PayPal API Public key', 'paypal_api_key', '', ''), (NULL, 'PayPal API Secret key', 'paypal_api_pass', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'PayPal Payment Mode', 'paypal_mode', 'sandbox', '/^(sandbox|live)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Site currency', 'site_currency', 'usd', ' \r\n/^([a-zA-Z]){2,7}$/');
{%end%}

{%begin%}
  CREATE TABLE `cl_wallet_history` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `user_id` INT(11) NOT NULL DEFAULT '0' ,  `operation` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,  `amount` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.00' ,  `json_data` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '[]' ,  `time` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Advertising system', 'advertising_system', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Ad conversion rate', 'ad_conversion_rate', '0.05', '/^([0-9]{1,3}\\.[0-9]{1,3}|[0-9]{1,3})$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` ADD `og_data` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `target`;
{%end%}

{%begin%}
  ALTER TABLE `cl_sessions` ADD `lifespan` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `time`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `pnotif_token` VARCHAR(600) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '{\"token\": \"\",\"type\": \"android\"}' AFTER `wallet`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `refresh_token` VARCHAR(220) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `pnotif_token`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Max post length', 'max_post_len', '600', '/^[0-9]{1,11}$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `settings` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '{\"notifs\":{\"like\":1,\"subscribe\":1,\"subscribe_request\":1,\"subscribe_accept\":1,\"reply\":1,\"repost\":1,\"mention\":1}}' AFTER `refresh_token`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `profile_privacy` `profile_privacy` ENUM('everyone','followers') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'everyone';
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` ADD `priv_wcs` ENUM('everyone','followers') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'everyone' AFTER `og_data`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `follow_privacy` ENUM('everyone','approved') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'everyone' AFTER `profile_privacy`;
{%end%}

{%begin%}
  ALTER TABLE `cl_connections` ADD `status` ENUM('active','pending') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'active' AFTER `following_id`;
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` ADD `priv_wcr` ENUM('everyone','followers','mentioned') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'everyone' AFTER `priv_wcs`;
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` ADD `poll_data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `og_data`;
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` CHANGE `text` `text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` CHANGE `poll_data` `poll_data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` CHANGE `text` `text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` CHANGE `type` `type` ENUM('text','video','image','gif','poll') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'text';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `swift` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `settings`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `last_swift` VARCHAR(135) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `last_post`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `swift_update` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `swift`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `cover_orig` `cover_orig` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'upload/default/cover.png';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `info_file` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `swift_update`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Google oAuth', 'google_oauth', 'on', '/^(on|off)$/'), (NULL, 'Twitter oAuth', 'twitter_oauth', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Facebook oAuth', 'facebook_oauth', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Google ads (Horiz-banner)', 'google_ad_horiz', '', ''), (NULL, 'Google ads (Vert-banner)', 'google_ad_vert', '', '');
{%end%}

{%begin%}
  CREATE TABLE `cl_pub_reports` ( `id` INT(11) NOT NULL AUTO_INCREMENT,  
    `user_id` INT(11) NOT NULL DEFAULT '0',  
    `post_id` INT(11) NOT NULL DEFAULT '0',  
    `reason` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
    `seen` ENUM('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',  
    `comment` VARCHAR(1210) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',  
    `time` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',    
    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `start_up` VARCHAR(600) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'done' AFTER `joined`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Default country', 'country_id', '1', '/^[0-9]{1,11}$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_ads` ADD `approved` ENUM('Y','N') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'N' AFTER `status`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Firebase API key', 'firebase_api_key', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Push notifications', 'push_notifs', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` CHANGE `og_data` `og_data` VARCHAR(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '';
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Page update interval', 'page_update_interval', '30', '/^[0-9]{1,11}$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Chat update interval', 'chat_update_interval', '5', '/^[0-9]{1,11}$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Amazon S3 storage', 'as3_storage', 'off', '/^(on|off)$/'), (NULL, 'AS3 bucket name', 'as3_bucket_name', '', ''), (NULL, 'Amazon S3 API key', 'as3_api_key', '', ''), (NULL, 'Amazon S3 API secret key', 'as3_api_secret_key', '', ''), (NULL, 'AS3 bucket region', 'as3_bucket_region', 'us-east-1', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Max upload size', 'max_upload_size', '24000000', '/^[0-9]{1,11}$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Max post audio record length', 'post_arec_length', '30', '/^[0-9]{1,11}$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` CHANGE `type` `type` ENUM('text','video','image','gif','poll','audio') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'text';
{%end%}

{%begin%}
  ALTER TABLE `cl_pubmedia` CHANGE `type` `type` ENUM('image','video','gif','audio') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
{%end%}

{%begin%}
  CREATE TABLE `cl_acc_validations` ( `id` INT(11) NOT NULL AUTO_INCREMENT, `json` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, `time` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0', PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_acc_validations` ADD `hash` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `id`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `email_conf_code` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `em_code`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `display_settings` VARCHAR(1200) NOT NULL DEFAULT '{\"color_scheme\": \"default\",\"background\": \"default\"}' AFTER `settings`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Wallet topup min amount', 'wallet_min_amount', '50', '/^([0-9]{1,3}\\.[0-9]{1,3}|[0-9]{1,3})$/'), (NULL, '', '', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Currency symbol position', 'currency_symbol_pos', 'after', '/^(before|after)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Aff payout min amount', 'aff_payout_min', '50', '/^([0-9]{1,3}\\\\.[0-9]{1,3}|[0-9]{1,3})$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Default color scheme', 'default_color_scheme', 'default', ''), (NULL, 'Default BG color', 'default_bg_color', 'default', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Android app (Google play item URL)', 'android_app_url', '', ''), (NULL, 'IOS app (App store item URL)', 'ios_app_url', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'User registration system', 'user_signup', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Cookie warning popup', 'cookie_warning_popup', 'on', '/^(on|off)$/');
{%end%}
{%begin%}
INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Faq Page', 'faq_page', 'Faq', '');
{%end%}

{%begin%}
INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Terms of Use', 'terms_page', 'Terms Of Use', '');
{%end%}
{%begin%}
INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Privacy Policy', 'privacy_page', 'Privacy policy', '');
{%end%}
{%begin%}
INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Cookies Policy', 'cookies_page', 'Cookies policy', '');
{%end%}
{%begin%}
INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'About Us', 'about_page', 'About Us', '');
{%end%}
{%begin%}
  CREATE TABLE `cl_invite_links` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `code` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,  `role` SET('user','admin') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'user' ,  `mnu` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' ,  `time` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_invite_links` ADD `registered_users` INT(11) NOT NULL DEFAULT '0' AFTER `mnu`;
{%end%}

{%begin%}
  ALTER TABLE `cl_invite_links` ADD `expires_at` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `mnu`;
{%end%}

{%begin%}
  CREATE TABLE `cl_ui_langs` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `name` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,  `slug` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,  `flag` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,  `status` SET('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  INSERT INTO `cl_ui_langs` (`id`, `name`, `slug`, `flag`, `status`) VALUES (NULL, 'English', 'english', 'gb', '1'), (NULL, 'French', 'french', 'fr', '1'), (NULL, 'German', 'german', 'de', '1'), (NULL, 'Italian', 'italian', 'it', '1'), (NULL, 'Russian', 'russian', 'ru', '1'), (NULL, 'Portuguese', 'portuguese', 'pt', '1'), (NULL, 'Spanish', 'spanish', 'es', '1'), (NULL, 'Turkish', 'turkish', 'tr', '1'), (NULL, 'Dutch', 'dutch', 'nl', '1'), (NULL, 'Ukraine', 'ukraine', 'ua', '1');
{%end%}

{%begin%}
  ALTER TABLE `cl_ui_langs` ADD `is_rtl` SET('Y','N') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'N' AFTER `status`;
{%end%}

{%begin%}
  ALTER TABLE `cl_ui_langs` DROP `flag`;
{%end%}

{%begin%}
  ALTER TABLE `cl_ui_langs` CHANGE `name` `name` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
{%end%}

{%begin%}
  ALTER TABLE `cl_ui_langs` CHANGE `name` `name` VARCHAR(65) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
{%end%}

{%begin%}
  ALTER TABLE `cl_ui_langs` ADD `is_native` SET('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `is_rtl`;
{%end%}

{%begin%}
  UPDATE `cl_ui_langs` SET `is_native` = '1' WHERE `id` IN (1,2,3,4,5,6,7,8,9,10);
{%end%}

{%begin%}
  INSERT INTO `cl_ui_langs` (`id`, `name`, `slug`, `status`, `is_rtl`, `is_native`) VALUES (NULL, 'Arabic', 'arabic', '1', 'Y', '1');
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `settings` `settings` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '{\"notifs\":{\"like\":1,\"subscribe\":1,\"subscribe_request\":1,\"subscribe_accept\":1,\"reply\":1,\"repost\":1,\"mention\":1},\"enotifs\":{\"like\":0,\"subscribe\":0,\"subscribe_request\":0,\"subscribe_accept\":0,\"reply\":0,\"repost\":0,\"mention\":0}}';
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Google reCAPTCHA', 'google_recaptcha', 'off', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Google reCAPTCHA Sitekey', 'google_recap_key1', '', ''), (NULL, 'Google reCAPTCHA Secret key', 'google_recap_key2', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'E-Mail notifications', 'email_notifications', 'off', '/^(on|off)$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `avatar` `avatar` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'upload/default/avatar-1.png';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `about` `about` VARCHAR(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Swifts system status (Daily stories)', 'swift_system_status', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'PayPal Payment Status', 'paypal_method_status', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'PayStack API Public key', 'paystack_api_key', '', ''), (NULL, 'Paystack API Secret key', 'paystack_api_pass', '', ''), (NULL, 'Paystack Payment Status', 'paystack_method_status', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Stripe API Secret key', 'stripe_api_pass', '', ''), (NULL, 'Stripe API Public key', 'stripe_api_key', '', ''), (NULL, 'Stripe Payment Status', 'stripe_method_status', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'AliPay Payment Status', 'alipay_method_status', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` ADD `edited` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `time`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `city` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `country_id`;
{%end%}

{%begin%}
  UPDATE `cl_users` SET `display_settings` = '{\"color_scheme\": \"default\",\"background\": \"default\"}';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `gender` `gender` ENUM('M','F','T','O') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'M';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `cover` `cover` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'upload/default/cover-1.png', CHANGE `cover_orig` `cover_orig` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'upload/default/cover-1.png';
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Timezone', 'timezone', 'UTC', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Bank transfer gateway', 'bank_method_status', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  CREATE TABLE `cl_banktrans_requests` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `user_id` INT(11) NOT NULL ,  `amount` VARCHAR(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0.00' ,  `receipt_img` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL ,  `currency` VARCHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'USD' ,  `time` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_banktrans_requests` ADD `message` VARCHAR(1210) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `receipt_img`;
{%end%}

{%begin%}
  ALTER TABLE `cl_wallet_history` ADD `status` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'success' AFTER `time`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Bank account number', 'bt_bank_account_number', '', ''), (NULL, 'Routing code', 'bt_bank_routing_code', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Bank account name', 'bt_bank_account_name', '', ''), (NULL, 'Bank country', 'bt_bank_country_name', '1', ''), (NULL, 'Bank address', 'bt_bank_address', '', ''), (NULL, 'Bank name', 'bt_bank_name', '', ''), (NULL, 'Bank SVG Logo', 'bt_bank_svg_logo', '', '');
{%end%}

{%begin%}
  ALTER TABLE `cl_wallet_history` ADD `trans_id` VARCHAR(130) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `status`;
{%end%}

{%begin%}
  ALTER TABLE `cl_banktrans_requests` ADD `trans_id` VARCHAR(130) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `currency`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'System API status', 'system_api_status', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Guest page status', 'guest_page_status', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Username restrictions', 'username_restrictions', '', ''), (NULL, 'User email restrictions', 'useremail_restrictions', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Post video download system', 'post_video_download_system', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` CHANGE `type` `type` ENUM('text','video','image','gif','poll','audio','document') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'text';
{%end%}

{%begin%}
  ALTER TABLE `cl_pubmedia` CHANGE `type` `type` ENUM('image','video','gif','audio','document') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'RazorPay payment method status', 'rzp_method_status', 'on', '/^(on|off)$/'), (NULL, 'RazorPay API Public key', 'rzp_api_key', '', ''), (NULL, 'RazorPay API Secret key', 'rzp_api_secret', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'LinkedIN oAuth status', 'linkedin_oauth', 'on', '/^(on|off)$/'), (NULL, 'LinkedIn API ID', 'linkedin_api_id', '', ''), (NULL, 'LinkedIn API Key', 'linkedin_api_key', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Discord oAuth status', 'discord_status', 'on', '/^(on|off)$/'), (NULL, 'Discord API ID', 'discord_api_id', '', ''), (NULL, 'Discord API Key', 'discord_api_key', '', '');
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `logged_in_with` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'system' AFTER `joined`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Vkontakte oAuth status', 'vkontakte_status', 'on', '/^(on|off)$/'), (NULL, 'Vkontakte API ID', 'vkontakte_api_id', '', ''), (NULL, 'Vkontakte API Key', 'vkontakte_api_key', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Instagram oAuth status', 'instagram_status', 'on', '/^(on|off)$/'), (NULL, 'Instagram API ID', 'instagram_api_id', '', ''), (NULL, 'Instagram API Key', 'instagram_api_key', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Premium account system status', 'prem_account_system_status', 'off', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Premium account m/price', 'premium_account_mprice', '0.00', '/^([0-9]{1,11}\\.[0-9]{1,11}|[0-9]{1,11})$/');
{%end%}
{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Premium account y/price', 'premium_account_yprice', '0.00', '/^([0-9]{1,11}\\.[0-9]{1,11}|[0-9]{1,11})$/');
{%end%}
{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Blue account y/price', 'blue_account_yprice', '0.00', '/^([0-9]{1,11}\\.[0-9]{1,11}|[0-9]{1,11})$/');
{%end%}
{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Max post length blue', 'max_post_len_blue', '10000', '/^[0-9]{1,11}$/');
{%end%}
{%begin%}
  ALTER TABLE `cl_users` ADD `is_premium` ENUM('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' AFTER `info_file`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `premium_ex_date` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' AFTER `is_premium`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `premium_settings` VARCHAR(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '{\"disable_native_ads\": 0,\"disable_adsense_ads\": 0}' AFTER `is_premium`;
{%end%}

-- Blue account
{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Blue account system status', 'blue_account_system_status', 'off', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Blue account m/price', 'blue_account_mprice', '0.00', '/^([0-9]{1,11}\\.[0-9]{1,11}|[0-9]{1,11})$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `is_blue` ENUM('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' AFTER `info_file`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `blue_ex_date` VARCHAR(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' AFTER `is_blue`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `blue_settings` VARCHAR(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '{\"disable_native_ads\": 0,\"disable_adsense_ads\": 0}' AFTER `is_blue`;
{%end%}