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

SELECT u.`id`, u.`username`, u.`fname`,  u.`avatar`, u.`swift` FROM `<?php echo($data['t_users']); ?>` u
	
	WHERE u.`swift_update` > <?php echo time(); ?>

	AND (u.`id` = <?php echo($data['user_id']); ?> OR u.`id` IN (SELECT `following_id` FROM `<?php echo($data['t_conns']); ?>` WHERE `follower_id` = <?php echo($data['user_id']); ?> AND `status` = "active"))

ORDER BY u.`swift_update` DESC;