<?php
	require_once 'uploadModel.php';
    class uploadViewHTML{
    	private $uploadModel;
        
		public function __construct() {		
              $this->uploadModel = new uploadModel();
        }
		
		
        public function echoHTML($body){
        	
		$loggedIn = $this->uploadModel->GetLoggedInUser();
			
			
        	if(isset($loggedIn) && $this->didUserPressUpload() == TRUE){
			
			
            echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<link rel='stylesheet' type='text/css' href='Css/styles1.css'>
					<title>Upload some shit</title>
				</head>
				<body>
				<div id='content'>
				<header><h1>Mickes Fotosida</h1></header>
				
					<h2>Upload images</h2>
					<a href='index.php'>Tillbaka</a>
					<p>Filformatet måste vara av typen .jpg, inte vara större än 25mb och inte ha ett namn som är över 40 tecken.</p>
					<form method='post' enctype='multipart/form-data'>
					
					<label for='file'>File:</label>
					<input type='file' name='filename'><br>
					
					<input type='submit' name='upload' value='Upload'>
					</form>
				
					$body
					<footer>Mickes Fotosida</footer>
					</div>
				</body>
				</html>
		";
        }
		
		}
		

		
		public function didUserPressUpload(){
			if(isset($_GET['upload'])){
				return TRUE;
		}
			return FALSE;
		}
	
		public function didUserPressUploadImageButton(){
			if(isset($_POST['upload'])){
				return TRUE;
			}
			return FALSE;
		}
			
		}
		
        
    