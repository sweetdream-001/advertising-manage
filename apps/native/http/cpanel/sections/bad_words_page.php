<?php 

$cl["app_statics"] = array(
	"scripts" => array(
		cl_static_file_path("statics/js/libs/bootstrap-select-v1.13.9.min.js"),
		cl_static_file_path("statics/js/libs/jquery-plugins/jquery.form-v4.2.2.min.js"),
	),
	"styles" => array(
		cl_static_file_path("statics/css/libs/bootstrap-select-v1.13.9.min.css")
	)
);

$cl['http_res'] = cl_template("cpanel/assets/bad_words_page/content");