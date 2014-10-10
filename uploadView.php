<?php

require_once 'uploadViewHTML.php';
require_once 'uploadController.php';

session_start();

$uploadController = new uploadController();
$uploadBody = $uploadController->doUpload();

//$body = new uploadViewHTML();
//$body->echoHTML($uploadBody);


