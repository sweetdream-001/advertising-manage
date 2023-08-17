<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                           @
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                   @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2021 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

if (empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 401,
		'data'    => array(),
		'message' => 'Unauthorized Access'
	);
}
else {
	$scope = fetch_or_get($_POST['scope'], array());
    $scope = cl_decode_array($scope);
    $ids   = array();

    if (not_empty($scope) && is_array($scope) && are_all($scope, "numeric")) {
        foreach ($scope as $id) {
            $ids[] = $id;
        }

        $db = $db->where('recipient_id', $me['id']);
        $db = $db->where('id', $ids, 'IN');
        $qr = $db->delete(T_NOTIFS);
        
        $data['data']    = array();
        $data['code']    = 200;
        $data['message'] = "Notifications deleted successfully";
    }

    else {
    	$data['code']    = 400;
        $data['message'] = "Notification IDs are missing or invalid";
        $data['data']    = array();
    }
}