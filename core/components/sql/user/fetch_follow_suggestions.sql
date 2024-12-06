/*
@*************************************************************************@
@ Software author: JOOJ Team (JOOJ.us)					        		  @
@ Author_url 1: https://jooj.us                                           @
@ Author_url 2: http://jooj.us/twitter-clone                              @
@ Author E-mail: sales@jooj.us                                            @
@*************************************************************************@
@ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
@ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
@*************************************************************************@
 */

SELECT `id`,`followers`,`posts`, `following`, `country_id`, `website`, `avatar`,`about`,`last_active`,`username`,`fname`,`email`,`verified`, `followers`, `follow_privacy` FROM `<?php echo($data['t_users']); ?>`
	
	WHERE `active` = '1'

	<?php if($data['user_id']): ?>
		AND `id` NOT IN (SELECT `following_id` FROM `<?php echo($data['t_conns']); ?>` WHERE `follower_id` = "<?php echo($data['user_id']); ?>" AND `status` = "active") 

		AND `id` != "<?php echo($data['user_id']); ?>"

		AND `id` NOT IN (SELECT b1.`profile_id` FROM `<?php echo($data['t_blocks']); ?>` b1 WHERE b1.`user_id` = <?php echo($data['user_id']); ?>)

		AND `id` NOT IN (SELECT b2.`user_id` FROM `<?php echo($data['t_blocks']); ?>` b2 WHERE b2.`profile_id` = <?php echo($data['user_id']); ?>)
	<?php endif; ?>

    

	ORDER BY `<?php echo($data['t_users']); ?>`.id DESC

<?php if($data['offset']): ?>
	LIMIT <?php echo($data['limit']); ?> OFFSET <?php echo($data['offset']); ?>;
<?php else: ?>
	LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>