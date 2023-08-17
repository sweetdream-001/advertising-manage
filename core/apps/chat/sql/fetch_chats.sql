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

SELECT 

	user.`id` as user_id,
	user.`username`,
	CONCAT(user.`fname`) as name,
	user.`avatar`,
	user.`verified`,

	chat.`id` as chat_id, 
	chat.`time`, 

	/* Select last message from  chat conversation */

		(SELECT  message.`message`  FROM `<?php echo($data['t_msgs']); ?>` message  WHERE 

			(message.`sent_by` = chat.`user_one` AND message.`sent_to` = chat.`user_two` AND message.`deleted_fs1` = 'N') OR

			(message.`sent_by` = chat.`user_two` AND message.`sent_to` = chat.`user_one` AND message.`deleted_fs2` = 'N')

		ORDER BY message.`id` DESC LIMIT 1) AS last_message,


	/* Select_ unseen messages total from_ user conversation */

		(SELECT  COUNT(m.`id`) FROM `<?php echo($data['t_msgs']); ?>` m WHERE (m.`sent_to` = <?php echo($data['user_id']); ?> AND m.`sent_by` = chat.`user_two`) AND m.`seen` = 0) AS new_messages


FROM `<?php echo($data['t_chats']); ?>` chat INNER JOIN `<?php echo($data['t_users']); ?>` user ON chat.`user_two` = user.`id`

	WHERE chat.`user_one` = <?php echo($data['user_id']); ?>

	AND chat.`user_two` NOT IN (SELECT b1.`profile_id` FROM `<?php echo($data['t_blocks']); ?>` b1 WHERE b1.`user_id` = <?php echo($data['user_id']); ?>)

	AND chat.`user_two` NOT IN (SELECT b2.`user_id` FROM `<?php echo($data['t_blocks']); ?>` b2 WHERE b2.`profile_id` = <?php echo($data['user_id']); ?>)

	<?php if($data['offset']): ?>
		AND chat.`id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

  	ORDER BY chat.`time` DESC 

<?php if($data['limit']): ?>
	LIMIT <?php echo($data['limit']); ?>
<?php endif; ?>