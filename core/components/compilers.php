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

function cl_template($file_path = "") {
    global $config, $cl, $me;

    $path  = cl_strf("themes/%s/apps/%s.phtml",$config['theme'],$file_path);
    $path  = cl_full_path($path);
    $cont  = "";

    if (file_exists($path) != true) {
        die("File does not exists: $path");
    } 
    
    ob_start();
	    require($path);
	    $cont = ob_get_contents();
    ob_end_clean();

    $cont = preg_replace_callback("/\{\%config\s{1}([a-z0-9_]{1,32})\%\}/", function($m) use ($config) {
        return ((isset($config[$m[1]])) ? $config[$m[1]] : "");
    }, $cont);

    return $cont;
}

function cl_js_template($file_path = "") {
    global $config, $cl, $me;

    $path = cl_strf("themes/%s/%s.js", $config['theme'], $file_path);
    $path = cl_full_path($path);
    $cont = "";

    if (file_exists($path) != true) {
        die("JS file does not exists: $path");
    } 
    
    ob_start();
        require($path);
        $cont = ob_get_contents();
    ob_end_clean();

    $cont = preg_replace_callback("/\{\%config\s{1}([a-z0-9_]{1,32})\%\}/", function($m) use ($config) {
        return ((isset($config[$m[1]])) ? $config[$m[1]] : "");
    }, $cont);

    return cl_minify_js($cont);
}

function cl_css_template($file_path = "") {
    global $config, $cl, $me;

    $path = cl_strf("themes/%s/%s.css", $config['theme'], $file_path);
    $path = cl_full_path($path);
    $cont = "";

    if (file_exists($path) != true) {
        die("CSS file does not exists: $path");
    } 
    
    ob_start();
        require($path);
        $cont = ob_get_contents();
    ob_end_clean();

    return cl_minify_css($cont);
}

function cl_html_template($file_path = "") {
    global $cl;

    $path = cl_strf("core/components/html/%s.phtml", $file_path);
    $path = cl_full_path($path);
    $cont = "";

    if (file_exists($path) != true) {
        die("HTML file does not exists: $path");
    } 
    
    ob_start();
        require($path);
        $cont = ob_get_contents();
    ob_end_clean();

    return $cont;
}

function cl_sitemap($file_path = "") {
    global $cl;
    
    $path  = cl_strf("sitemap/%s.xml", $file_path);
    $path  = cl_full_path($path);
    $cont  = "";

    if (file_exists($path) != true) {
        die("File does not exists: $path");
    } 
    
    ob_start();
        require($path);
        $cont = ob_get_contents();
    ob_end_clean();

    return $cont;
}

function cl_sqltepmlate($path = '', $data = array()){
    global $cl;
    
    $temp_path = cl_strf("core/%s.sql",$path);
    $template  = "";
    $temp_path = cl_full_path($temp_path);
    
    if (file_exists($temp_path)) {
        ob_start();
            require($temp_path);
            $template = ob_get_contents();
        ob_end_clean();
    }
    else {
        exit("$temp_path: Does not exists on the server!");
    }
    //echo $template;exit("Okay");
    return $template;
}