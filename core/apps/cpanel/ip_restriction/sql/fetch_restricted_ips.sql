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

SELECT ip.`id`, ip.`ip_address`, ip.`time` AS name FROM `<?php echo($data['t_restricted_ips']); ?>` ip ORDER BY ip.`id` <?php echo($data['order']) ?> LIMIT <?php echo($data['limit']) ?>;
