<?php

    class uploadViewHTML{
        
        public function echoHTML($body){
        	
        	if(isset($_SESSION['login']) && $this->didUserPressUpload() == TRUE){
			
			
            echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<link rel='stylesheet' type='text/css' href='Css/styles1.css'>
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
		
		public function ValidateFilesize($filesize){
			if($filesize > 26214400){
				echo "filen är för stor";
				return "Filen är för stor!";
			}
			return "";
		}
		
		public function Validate($filename){
			
			if(strlen($filename) > 40){
				echo $filename;
				return "Filnamnet är för långt!";
			}else{
				return "";
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
		
        
    