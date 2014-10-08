<?php

require_once 'uploadHTML.php';
require_once 'uploadController.php';

$uploadController = new uploadController();
$uploadBody = $uploadController->doUpload();

$body = new uploadHTML();
$body->echoBody($uploadBody);


