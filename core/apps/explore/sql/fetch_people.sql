/*
@*************************************************************************@
@ Software author: JOOJ Team (JOOJ.us)				           			  @
@ Author_url 1: https://jooj.us                                           @
@ Author_url 2: http://jooj.us/twitter-clone                              @
@ Author E-mail: sales@jooj.us                                            @
@*************************************************************************@
@ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
@ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
@*************************************************************************@
 */

SELECT `id`, `about`, `followers`, `following`, `posts`, `website`, `country_id`, `avatar`, `last_active`, `username`, `fname`, `email`, `verified`, `follow_privacy` FROM `<?php echo($data['t_users']); ?>`
	
	WHERE `active` = '1'

	<?php if($data['user_id']): ?>
		AND `id` != <?php echo($data['user_id']); ?>

		AND `id` NOT IN (SELECT b1.`profile_id` FROM `<?php echo($data['t_blocks']); ?>` b1 WHERE b1.`user_id` = <?php echo($data['user_id']); ?>)

		AND `id` NOT IN (SELECT b2.`user_id` FROM `<?php echo($data['t_blocks']); ?>` b2 WHERE b2.`profile_id` = <?php echo($data['user_id']); ?>)
	<?php endif; ?>

	<?php if($data['keyword']): ?>
		AND (`username` LIKE "%<?php echo($data['keyword']); ?>%" OR `fname` LIKE "%<?php echo($data['keyword']); ?>%" OR `about` LIKE "%<?php echo($data['keyword']); ?>%" OR CONCAT(`fname`) LIKE "%<?php echo($data['keyword']); ?>%")
	<?php endif; ?>
    
    <?php if($data['t_users'] == 'cl_users'){ ?>
    	ORDER BY `cl_users`.`id` DESC
    <?php }else { ?>
    	ORDER BY `followers` DESC, `posts` DESC
    <?php } ?>

<?php if(is_posnum($data['limit'])): ?>
	LIMIT <?php echo($data['limit']); ?>


	<?php if($data['offset']): ?>
		OFFSET <?php echo($data['offset']); ?>
	<?php endif; ?>

<?php endif; ?>