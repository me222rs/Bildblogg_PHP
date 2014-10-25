<?php

//require_once 'galleryViewHTML.php';
require_once 'galleryController.php';

session_start();

$galleryController = new galleryController();
$galleryBody = $galleryController->doShowGallery();
