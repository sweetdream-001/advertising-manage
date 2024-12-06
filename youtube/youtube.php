<?php
include_once ('simple_html_dom.php');
$html = file_get_html('https://www.youtube.com/watch?v=ZOAeciQdINI');
$title=$html->find('title',0);
echo $title->plaintext;
echo "$html";

