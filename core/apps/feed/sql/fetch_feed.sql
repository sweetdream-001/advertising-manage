/*
@*************************************************************************@
@ Software author: JOOJ Team (JOOJ.us)							  @
@ Author_url 1: https://jooj.us                       @
@ Author_url 2: http://jooj.us/twitter-clone                      @
@ Author E-mail: sales@jooj.us                                    @
@*************************************************************************@
@ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
@ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
@*************************************************************************@
*/

SELECT `<?php echo($data['t_pubs']); ?>`.*, cl_categories.name as cat_name  FROM `<?php echo($data['t_pubs']); ?>` 

    
    left join cl_categories on cl_publications.category_id = cl_categories.id
    
	WHERE `status` = "active"

	AND `target` = "publication"

	AND `priv_wcs` = "everyone"

    ORDER BY `id` DESC
-- 	ORDER BY `likes_count` DESC, `replys_count` DESC, `reposts_count` DESC

<?php if(is_posnum($data['limit'])): ?>
	LIMIT <?php echo($data['limit']); ?>

	<?php if(not_empty($data['offset'])): ?>
		OFFSET <?php echo($data['offset']); ?>
	<?php endif; ?>

<?php endif; ?>

