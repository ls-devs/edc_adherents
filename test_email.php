<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
echo 'a';

$url = 'URL';
$data = array('email' => 'value', 'field2' => 'value');
$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
var_dump($result);

echo 'c';
?>