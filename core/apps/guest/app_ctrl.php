<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)							@
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                    @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

function cl_get_slider_images() {
	global $cl;

	$images = glob(cl_full_path(cl_strf("themes/%s/statics/img/guest/default/*.jpg", $cl["config"]["theme"])));
	$img_links = array();

	if (is_array($images)) {
		foreach($images as $img_path) {
			$path_info = explode("/", $img_path);
			$path_name = end($path_info);

			array_push($img_links, cl_link(cl_strf("themes/%s/statics/img/guest/default/%s", $cl["config"]["theme"], $path_name)));
		}
	}

	return $img_links;
}