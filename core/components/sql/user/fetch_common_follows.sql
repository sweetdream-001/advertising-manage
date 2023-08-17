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

SELECT u.`avatar`, u.`fname`,  u.`username` FROM `<?php echo($data['t_conns']); ?>` c1

	INNER JOIN `<?php echo($data['t_users']); ?>` u ON c1.`following_id` = u.`id`
	
	WHERE c1.`follower_id` = "<?php echo($data['myid']); ?>"

	AND c1.`following_id` IN (SELECT c2.`follower_id` FROM `<?php echo($data['t_conns']); ?>` c2 WHERE c2.`following_id` = "<?php echo($data['user_id']); ?>" AND c2.`status` = "active")

	AND c1.`status` = "active"
	
	ORDER BY c1.`id`

LIMIT 5000;