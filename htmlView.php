<?php

    class htmlView{
        
        public function echoHtml($body){
            echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<link rel='stylesheet' type='text/css' href='Css/styles1.css'>
					<title>Laboration_Login</title>
				</head>
				<body>
					$body
				</body>
				</html>
		";
        }
        
    }