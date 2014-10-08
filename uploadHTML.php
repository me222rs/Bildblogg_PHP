<?php

    class uploadHTML{
        
        public function echoBody($body){
            echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<title>Upload some shit</title>
				</head>
				<body>
					$body
				</body>
				</html>
		";
        }
        
    }