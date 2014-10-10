<?php

    class uploadViewHTML{
        
        public function echoHTML($body){
        	
        	if(isset($_SESSION['login']) && $this->didUserPressUpload() == TRUE){
			
			
            echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<title>Upload some shit</title>
				</head>
				<body>
					<h2>Upload images</h2>
					<a href='index.php'>Tillbaka</a>
					<form method='post' enctype='multipart/form-data'>
					
					<label for='file'>File:</label>
					<input type='file' name='filename'><br>
					<input type='submit' name='upload' value='Upload'>
					</form>
				
					$body
				</body>
				</html>
		";
        }
		
		}
		
		public function didUserPressUpload(){
		if(isset($_GET['upload'])){
			echo "Tryckt på ladda upp!";
			return TRUE;
		}
		return FALSE;
	}
			
		}
		
        
    