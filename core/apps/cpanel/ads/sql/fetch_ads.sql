SELECT 
    a.`id`, 
    a.`user_id`, 
    a.`cover`, 
    a.`company`,
    a.`target_url`, a.`timeleft`, 
    a.`views`, 
    a.`clicks`, 
    a.`budget`, 
    a.`approved`, 
    a.`time`, 
    a.`status`, 
    u.`avatar`, 
    u.`username`, 
    u.`verified`, 
    CONCAT(u.`fname`) AS name 
FROM 
    `<?php echo($data['t_ads']); ?>` a
INNER JOIN 
    `<?php echo($data['t_users']); ?>` u 
    ON a.`user_id` = u.`id`
WHERE 
    a.`status` != 'orphan' 
    AND a.`target` = 'ad' 
    AND u.`active` = '1'
ORDER BY 
    a.`id` <?php echo($data['order']); ?> 
LIMIT <?php echo($data['limit']); ?> 
OFFSET <?php echo($data['offset']); ?>;