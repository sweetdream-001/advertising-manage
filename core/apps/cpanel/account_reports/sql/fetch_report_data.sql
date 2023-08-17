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

SELECT r.`id`, r.`profile_id`, r.`comment`, r.`user_id`, r.`reason`, r.`seen`, r.`time`, CONCAT(u.`fname`) AS name, u.`email`, u.`username`, u.`verified`, u.`avatar` FROM `<?php echo($data['t_reps']);?>` r
	
	INNER JOIN `<?php echo($data['t_users']);?>` u ON r.`user_id` = u.`id`

	WHERE r.`id` = <?php echo($data['rep_id']); ?>

LIMIT 1;