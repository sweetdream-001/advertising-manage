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

SELECT * FROM `<?php echo($data['t_ads']); ?>`
	
	WHERE `user_id` = <?php echo($data['user_id']); ?>

	AND `status` IN ('active', 'inactive')
	
	AND `target` = 'ad'

	<?php if($data['type'] == 'active'): ?>
		AND `status` = 'active' AND `approved` = 'Y'
	<?php elseif($data['type'] == 'inactive'): ?>
		AND `status` = 'inactive' AND `approved` = 'Y'
	<?php elseif($data['type'] == 'pending'): ?>
		AND `status` IN ('inactive', 'active') AND `approved` = 'N'
	<?php endif; ?>

	ORDER BY `clicks` DESC, `views` DESC

<?php if($data['offset']): ?>
	LIMIT <?php echo($data['offset']); ?>, <?php echo($data['offset'] + 10); ?>;
<?php else: ?>
	LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>