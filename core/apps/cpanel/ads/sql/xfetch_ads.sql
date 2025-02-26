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

SELECT a.`id`, a.`user_id`, a.`cover`, a.`target_url`, a.`timeleft`, a.`views`, a.`clicks`, a.`budget`, a.`approved`, a.`time`, a.`status`, u.`avatar`, u.`username`, u.`verified`, CONCAT(u.`fname`) AS name FROM `<?php echo($data['t_ads']); ?>` a
	
	INNER JOIN `<?php echo($data['t_users']); ?>` u ON a.`user_id` = u.`id`

	WHERE a.`status` != 'orphan'
	
	AND a.`target` = 'ad'

	AND u.`active` = '1'

	<?php if($data['offset']): ?>
		<?php if($data['offset_to'] == 'gt'): ?>
			AND a.`id` > <?php echo($data['offset']) ?>
		<?php else: ?>
			AND a.`id` < <?php echo($data['offset']) ?>
		<?php endif; ?>	
	<?php endif; ?>

ORDER BY a.`id` <?php echo($data['order']) ?> LIMIT <?php echo($data['limit']) ?>;
