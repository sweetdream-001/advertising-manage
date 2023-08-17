/*
 @*************************************************************************@
 @ @author JOOJ Team (JOOJ.us)						            			@
 @ @author_url 1: https://jooj.us                                          @
 @ @author_url 2: http://jooj.us/twitter-clone                             @
 @ @author_email: sales@jooj.us                                            @
 @*************************************************************************@
 @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
 @ Copyright (c) 27.07.2020 JOOJ Talk. All rights reserved.                @
 @*************************************************************************@
 */

SELECT * FROM `<?php echo($data['t_msgs']); ?>` 

	WHERE ((`sent_by` = <?php echo($data['user_one']); ?> AND `sent_to` = <?php echo($data['user_two']); ?> AND `deleted_fs1` = 'N') OR (`sent_to` = <?php echo($data['user_one']); ?> AND `sent_by` = <?php echo($data['user_two']); ?> AND `deleted_fs2` = 'N'))

	<?php if($data['offset']): ?>

		<?php if($data['offset_to'] == 'gt'): ?>
			AND `id` >  <?php echo($data['offset']); ?>
		<?php endif; ?>

		<?php if($data['offset_to'] == 'lt'): ?>
			AND `id` <  <?php echo($data['offset']); ?>
		<?php endif; ?>

	<?php endif; ?>

	<?php if(not_empty($data['query'])): ?>
		AND `message` LIKE "%<?php echo($data['query']); ?>%"
	<?php endif; ?>

	ORDER BY `id` <?php echo($data['order']); ?> 

<?php if($data['limit']): ?>
	LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>