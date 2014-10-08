<?php
require_once 'controller.php';
require_once 'htmlView.php';
session_start();
		  $loginCntrl = new controller();
		  $htmlBody = $loginCntrl->login();
		  
		  $view = new htmlView();
		  $view->echoHtml($htmlBody);
