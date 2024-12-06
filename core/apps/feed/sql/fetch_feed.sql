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

<?php	
if(isset($_GET['post_id']) && !empty($_GET['post_id'])) { ?>
	(
		SELECT cl_publications.*, cl_categories.name AS cat_name, cl_categories.id AS cat_id 
		FROM cl_publications 
		LEFT JOIN cl_categories ON cl_publications.category_id = cl_categories.id
		INNER JOIN cl_users ON cl_users.id = cl_publications.user_id 
		WHERE cl_publications.status = 'active'
		AND cl_publications.id >= <?php echo($_GET['post_id']) ?>
		AND cl_publications.target = 'publication'
		AND cl_publications.priv_wcs = 'everyone'
		AND cl_users.active = '1'
		LIMIT 14
	)
	UNION 
	(
		SELECT cl_publications.*, cl_categories.name AS cat_name, cl_categories.id AS cat_id 
		FROM cl_publications 
		LEFT JOIN cl_categories ON cl_publications.category_id = cl_categories.id
		INNER JOIN cl_users ON cl_users.id = cl_publications.user_id 
		WHERE cl_publications.status = 'active'
		AND cl_publications.id > <?php echo($_GET['post_id']) ?>
		AND cl_publications.target = 'publication'
		AND cl_publications.priv_wcs = 'everyone'
		AND cl_users.active = '1'
		LIMIT 15
	)
	ORDER BY id ASC;
<?php } else { ?>
	SELECT `<?php echo($data['t_pubs']); ?>`.*, cl_categories.name as cat_name, cl_categories.id as cat_id  FROM `<?php echo($data['t_pubs']); ?>` 

	left join cl_categories on cl_publications.category_id = cl_categories.id

	inner join cl_users on cl_users.id = cl_publications.user_id 

	WHERE `status` = "active"

	AND `target` = "publication"

	AND `priv_wcs` = "everyone"

	AND cl_users.active = "1"

	ORDER BY `id` ASC

	<?php if(is_posnum($data['limit'])): ?>
		LIMIT <?php echo($data['limit']); ?>

		<?php if(not_empty($data['offset'])): ?>
			OFFSET <?php echo($data['offset']); ?>
		<?php endif; ?>

	<?php endif;
}
?>