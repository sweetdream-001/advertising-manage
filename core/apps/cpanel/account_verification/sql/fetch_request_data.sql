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

SELECT r.`id`, u.`email`, u.`avatar`, u.`last_active`, u.`verified`, u.`username`, r.`full_name`, r.`text_message`, r.`video_message`, r.`time`

	FROM `<?php echo($data['t_reqs']) ?>` r

	INNER JOIN `<?php echo($data['t_users']); ?>` u ON r.`user_id` = u.`id`

	WHERE r.`id` = <?php echo($data['req_id']); ?>

LIMIT 1;