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

SELECT l.`id` AS offset_id, u.`id`, u.`about`, u.`followers`, u.`following`, u.`posts`, u.`website`, u.`country_id`, u.`posts`, u.`avatar`, u.`last_active`, u.`username`, u.`fname`,  u.`email`, u.`verified`, u.`follow_privacy` FROM `<?php echo($data['t_likes']); ?>` l
	
	INNER JOIN `<?php echo($data['t_users']); ?>` u ON l.`user_id` = u.`id`

	WHERE l.`pub_id` = "<?php echo($data['post_id']); ?>"

	AND u.`active` IN ('1', '2')

	<?php if(not_empty($data['offset'])): ?>
		AND l.`id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

	ORDER BY l.`id` DESC, u.`followers` DESC, u.`posts` DESC

<?php if(not_empty($data['limit'])): ?>
	LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>