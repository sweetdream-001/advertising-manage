/*
@*************************************************************************@
@ Software author: JOOJ Team (JOOJ.us)                              @
@ Author_url 1: https://jooj.us                                    @
@ Author_url 2: http://jooj.us/twitter-clone                       @
@ Author E-mail: sales@jooj.us                                     @
@*************************************************************************@
@ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform    @
@ Copyright (c) 2020 - 2021 JOOJ Talk. All rights reserved.        @
@*************************************************************************@
*/

SELECT 
    posts.`id` as offset_id, 
    posts.`publication_id`, 
    posts.`type`, 
    posts.`user_id`,
    pubs.`category_id`
FROM 
    `<?php echo($data['t_posts']); ?>` posts
INNER JOIN 
    `<?php echo($data['t_pubs']); ?>` pubs ON posts.`publication_id` = pubs.`id`
WHERE 
    posts.`user_id` = <?php echo($data['user_id']); ?>
<?php if($data['media']): ?>
    AND pubs.`type` IN ('video','image','gif', 'audio')
<?php endif; ?>
<?php if($data['offset']): ?>
    AND posts.`id` < <?php echo($data['offset']); ?>
<?php endif; ?>
<?php if($data['cat_id']): ?>
    AND pubs.`category_id` = '<?php echo($data['cat_id']); ?>'
<?php endif; ?>
ORDER BY 
    posts.`id` DESC, 
    pubs.`likes_count` DESC, 
    pubs.`replys_count` DESC, 
    pubs.`reposts_count` DESC 
<?php if($data['limit']): ?>
    LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>