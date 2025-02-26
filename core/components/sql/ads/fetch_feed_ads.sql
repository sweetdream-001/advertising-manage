/*
@*************************************************************************@
@ Software author: JOOJ Team (JOOJ.us)						           	  @
@ Author_url 1: https://jooj.us                                           @
@ Author_url 2: http://jooj.us/twitter-clone                              @
@ Author E-mail: sales@jooj.us                                            @
@*************************************************************************@
@ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
@ Copyright (c) 2020 - 2021 JOOJ Talk. All rights reserved.               @
@*************************************************************************@
*/
      
SELECT a.`id`, a.`user_id`, a.`cover`, a.`company`, a.`target_url`, a.`timeleft`, a.`views`, a.`clicks`, a.`budget`, a.`link_src`, a.`og_data`, a.`likes_count`, a.`reposts_count`, a.`replys_count`, a.`time`,  u.`avatar`, a.`description`, a.`cta`, u.`username`, u.`verified`, CONCAT(u.`fname`) AS name FROM `<?php echo($data['t_ads']); ?>` a
	 
	INNER JOIN `<?php echo($data['t_users']); ?>` u ON a.`user_id` = u.`id`

	<?php if(not_empty($data['ad_id'])): ?>

		WHERE a.`status` != 'orphan' AND a.`id` = <?php echo($data['ad_id']); ?>
		
	<?php else: ?>

		WHERE a.`status` = 'active'

		AND a.`approved` = 'Y'

		AND a.`target` = 'ad'

		AND u.`active` = '1'

		AND u.`wallet` > 0

		<?php if(not_empty($data['udata'])): ?>
			AND (a.`audience` LIKE '%<?php echo($data["udata"]["country_id"]); ?>%')
		<?php endif; ?>
		
	<?php endif; ?>

ORDER BY RAND() LIMIT 1;
