/*
@*************************************************************************@
@ Software author: JOOJ Team (JOOJ.us)				        			  @
@ Author_url 1: https://jooj.us                                           @
@ Author_url 2: http://jooj.us/twitter-clone                              @
@ Author E-mail: sales@jooj.us                                            @
@*************************************************************************@
@ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
@ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
@*************************************************************************@
 */

<?php	
if(isset($_GET['post_id']) && !empty($_GET['post_id'])) { ?>
	(
		SELECT posts.`id` as offset_id, posts.`publication_id`, posts.`type`, posts.`user_id` FROM `<?php echo($data['t_posts']); ?>` posts
		INNER JOIN `<?php echo($data['t_pubs']); ?>` pubs ON posts.`publication_id` = pubs.`id`
		WHERE pubs.`status` = 'active'
		AND pubs.`id` >= '`<?php echo($_GET['post_id']) ?>`'
		<?php if($data['cat_id'] != ''){ ?>
			and pubs.category_id = '`<?php echo($data['cat_id']); ?>`'   
		<?php } ?>
		AND (posts.`user_id` = <?php echo($data['user_id']); ?> OR posts.`user_id` IN (SELECT `following_id` FROM `<?php echo($data['t_conns']); ?>` WHERE `follower_id` = <?php echo($data['user_id']); ?> AND `status` = "active"))
		AND (posts.`publication_id` NOT IN (SELECT `post_id` FROM `<?php echo($data['t_reports']); ?>` WHERE `user_id` = <?php echo($data['user_id']); ?>))
		ORDER BY posts.`id` ASC
		LIMIT 14
	)
	UNION 
	(
		SELECT posts.`id` as offset_id, posts.`publication_id`, posts.`type`, posts.`user_id` FROM `<?php echo($data['t_posts']); ?>` posts
		INNER JOIN `<?php echo($data['t_pubs']); ?>` pubs ON posts.`publication_id` = pubs.`id`
		WHERE pubs.`status` = 'active'
		AND pubs.`id` < '`<?php echo($_GET['post_id']) ?>`'
		<?php if($data['cat_id'] != ''){ ?>
			and pubs.category_id = '`<?php echo($data['cat_id']); ?>`'   
		<?php } ?>
		AND (posts.`user_id` = <?php echo($data['user_id']); ?> OR posts.`user_id` IN (SELECT `following_id` FROM `<?php echo($data['t_conns']); ?>` WHERE `follower_id` = <?php echo($data['user_id']); ?> AND `status` = "active"))
		AND (posts.`publication_id` NOT IN (SELECT `post_id` FROM `<?php echo($data['t_reports']); ?>` WHERE `user_id` = <?php echo($data['user_id']); ?>))
		ORDER BY posts.`id` DESC
		LIMIT 14
	)
	ORDER BY offset_id ASC;
<?php } if(isset($data['thread'])) { ?>
    SELECT posts.`id` as offset_id, posts.`publication_id`, posts.`type`, posts.`user_id` FROM `<?php echo($data['t_posts']); ?>` posts

	INNER JOIN `<?php echo($data['t_pubs']); ?>` pubs ON posts.`publication_id` = pubs.`id`

	WHERE pubs.`status` = 'active'

	<?php if($data['cat_id'] != ''){ ?>
		and pubs.category_id = '<?php echo($data['cat_id']); ?>'   
	<?php } ?>


	ORDER BY posts.`id` DESC, pubs.`likes_count` DESC, pubs.`replys_count` DESC, pubs.`reposts_count` DESC

	<?php if($data['limit']): ?>
		LIMIT <?php echo($data['limit']); ?>

		<?php if($data['offset']): ?>
			OFFSET <?php echo($data['offset']); ?>
		<?php endif; ?>

	<?php endif; ?>
<?php } else { ?>
	SELECT posts.`id` as offset_id, posts.`publication_id`, posts.`type`, posts.`user_id` FROM `<?php echo($data['t_posts']); ?>` posts

	INNER JOIN `<?php echo($data['t_pubs']); ?>` pubs ON posts.`publication_id` = pubs.`id`

	WHERE pubs.`status` = 'active'

	<?php if($data['cat_id'] != ''){ ?>
		and pubs.category_id = '<?php echo($data['cat_id']); ?>'   
	<?php } ?>

	AND (posts.`user_id` = <?php echo($data['user_id']); ?> OR posts.`user_id` IN (SELECT `following_id` FROM `<?php echo($data['t_conns']); ?>` WHERE `follower_id` = <?php echo($data['user_id']); ?> AND `status` = "active"))

	AND (posts.`publication_id` NOT IN (SELECT `post_id` FROM `<?php echo($data['t_reports']); ?>` WHERE `user_id` = <?php echo($data['user_id']); ?>))

	ORDER BY posts.`id` DESC, pubs.`likes_count` DESC, pubs.`replys_count` DESC, pubs.`reposts_count` DESC

	<?php if($data['limit']): ?>
		LIMIT <?php echo($data['limit']); ?>

		<?php if($data['offset']): ?>
			OFFSET <?php echo($data['offset']); ?>
		<?php endif; ?>

	<?php endif;
}
?>