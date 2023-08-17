/*
@*************************************************************************@
@ Software author: JOOJ Team (JOOJ.us)							  @
@ Author_url 1: https://jooj.us                       @
@ Author_url 2: http://jooj.us/twitter-clone                      @
@ Author E-mail: sales@jooj.us                                   @
@*************************************************************************@
@ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
@ Copyright (c) 2020 - 2021 JOOJ Talk. All rights reserved.               @
@*************************************************************************@
 */

SELECT n.`id`, n.`notifier_id`, n.`recipient_id`, n.`status`, n.`subject`, n.`entry_id`, n.`json`, n.`time`, u.`username`, u.`avatar`, u.`verified`, CONCAT(u.`fname`) AS name FROM `<?php echo($data['t_notifs']); ?>` n
	
	INNER JOIN `<?php echo($data['t_users']); ?>` u ON n.`notifier_id` = u.`id`

	WHERE n.`recipient_id` = <?php echo($data['user_id']); ?>

	AND n.`notifier_id` NOT IN (SELECT b.`profile_id` FROM `<?php echo($data['t_blocks']); ?>` b WHERE b.`user_id` = <?php echo($data['user_id']); ?>)

	<?php if($data['offset']): ?>
		AND n.`id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

	<?php if($data['type'] == "notifs"): ?>
		AND n.`subject` IN ('reply','subscribe', 'like', 'repost', 'verified', 'visit', 'subscribe_request', 'subscribe_accept', 'ad_approval')
	<?php else: ?>
		AND n.`subject` = 'mention'
	<?php endif; ?>

	ORDER BY n.`id` DESC

<?php if($data['limit']): ?>
	LIMIT <?php echo($data['limit']); ?>
<?php endif; ?>