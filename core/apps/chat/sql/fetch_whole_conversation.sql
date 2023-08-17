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

SELECT * FROM `<?php echo($data['t_msgs']); ?>` 

	WHERE ((`sent_by` = <?php echo($data['user_one']); ?> AND `sent_to` = <?php echo($data['user_two']); ?> AND `deleted_fs1` = 'N') OR (`sent_to` = <?php echo($data['user_one']); ?> AND `sent_by` = <?php echo($data['user_two']); ?> AND `deleted_fs2` = 'N'))

ORDER BY `id` <?php echo($data['order']); ?>;