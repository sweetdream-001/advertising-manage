/*
@*************************************************************************@
@ Software author: JOOJ Team (JOOJ.us)							  @
@ Author_url 1: https://jooj.us                       @
@ Author_url 2: http://jooj.us/twitter-clone                      @
@ Author E-mail: sales@jooj.us                                    @
@*************************************************************************@
@ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
@ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
@*************************************************************************@
*/

SELECT `<?php echo($data['t_pubs']); ?>`.*, cl_categories.name as cat_name, cl_categories.id as cat_id FROM `<?php echo($data['t_pubs']); ?>` 

left join cl_categories on cl_publications.category_id = cl_categories.id
inner join cl_users on cl_users.id = cl_publications.user_id
	WHERE `status` = "active"
and cl_users.active = "1"
	AND `target` = "publication"

	<?php if(empty($cl["is_admin"])): ?>
		<?php if(not_empty($data['user_id'])): ?>
			AND (`priv_wcs` = "everyone" OR  `user_id` = <?php echo($data['user_id']); ?> OR `user_id` IN (SELECT `following_id` FROM `<?php echo($data['t_conns']); ?>` WHERE `follower_id` = <?php echo($data['user_id']); ?> AND `status` = "active"))
		<?php else: ?>
			AND `priv_wcs` = "everyone"
		<?php endif; ?>
	<?php endif; ?>

	<?php if(not_empty($data['keyword'])): ?>
		AND (`text` LIKE "%<?php echo($data['keyword']); ?>%"

		<?php if($data['htag']): ?>
			OR `text` LIKE "%{#id:<?php echo($data['htag']); ?>#}%"
		<?php endif; ?>)
	<?php endif; ?>


	<?php if(not_empty($data['user_id'])): ?>
		AND `user_id` NOT IN (SELECT b1.`profile_id` FROM `<?php echo($data['t_blocks']); ?>` b1 WHERE b1.`user_id` = <?php echo($data['user_id']); ?>)

		AND `user_id` NOT IN (SELECT b2.`user_id` FROM `<?php echo($data['t_blocks']); ?>` b2 WHERE b2.`profile_id` = <?php echo($data['user_id']); ?>)

		AND (`<?php echo($data['t_pubs']); ?>`.`id` NOT IN (SELECT `post_id` FROM `<?php echo($data['t_reports']); ?>` WHERE `user_id` = <?php echo($data['user_id']); ?>))
	<?php endif; ?>

	<?php if(not_empty($data['post_id'])): ?>
		AND <?php echo($data['t_pubs']); ?>.id NOT IN (<?php echo($data['post_id']); ?>)
	<?php endif; ?>

    ORDER BY `<?php echo($data['t_pubs']); ?>`.`id` ASC
-- 	ORDER BY `likes_count` DESC, `replys_count` DESC, `reposts_count` DESC

<?php if(is_posnum($data['limit'])): ?>
	LIMIT <?php echo($data['limit']); ?>

	<?php if(not_empty($data['offset'])): ?>
		OFFSET <?php echo($data['offset']); ?>
	<?php endif; ?>
<?php endif; ?>
