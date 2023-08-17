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

SELECT bm.`id` AS `bookmark_id`, bm.`publication_id`, bm.`user_id`, bm.`time` FROM `<?php echo($data['t_notes']); ?>` bm
	
	INNER JOIN `<?php echo($data['t_posts']); ?>` p ON bm.`publication_id` = p.`id`

	WHERE bm.`user_id` = <?php echo($data['user_id']); ?>

	AND p.`user_id` NOT IN (SELECT b.`profile_id` FROM `<?php echo($data['t_blocks']); ?>` b WHERE b.`user_id` = <?php echo($data['user_id']); ?>)

	<?php if(not_empty($data['offset'])): ?>
		AND bm.`id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

	ORDER BY bm.`id` DESC 

LIMIT <?php echo($data['limit']); ?>;