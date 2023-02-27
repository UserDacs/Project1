<?php
include 'lib/autoload.php';
include 'lib/constant.php';

$url = '';
$url = isset($_GET['url']) ? $_GET['url'] : "";	
$route = new route($url);

?>