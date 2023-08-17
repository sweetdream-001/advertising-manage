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

SELECT b.`id`, b.`user_id`, b.`profile_id`, u.`id` AS profile_id, u.`username`, u.`fname`, u.`verified`, u.`country_id`, u.`following`, u.`followers`, u.`about`, u.`website`, u.`posts`, u.`avatar` FROM `<?php echo($data['t_blocks']); ?>` b

	INNER JOIN `<?php echo($data['t_users']); ?>` u ON b.`profile_id` = u.`id`

	WHERE b.`user_id` = <?php echo($data['user_id']); ?>

	AND u.`active` IN ('1', '2')

LIMIT 1000;