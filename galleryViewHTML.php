<?php
	require_once 'galleryModel.php';
class galleryViewHTML{
        private $galleryModel;
		
	public	function __construct() {
        $this->galleryModel = new galleryModel();
		//$this->uploadView = new uploadView();
		//$this->uploadModel = new uploadModel();
	}
		
        public function echoHTML($body){
        	
			$images = array();
			
        	if(isset($_SESSION['login']) && $this->didUserPressGallery() == TRUE && $this->didUserPressImage() == FALSE){
			//<a href="http://www.hyperlinkcode.com"><img src="http://hyperlinkcode.com/images/sample-image.gif"></a>
			//$images = implode("<a href='http://www.mikaeledberg.se'><img src='$body'></a>");
			
			foreach($body as $value){
				array_push($images, "<a href='galleryView.php?gallery&image=$value'><img src='./UploadedImages/$value'></a>");
			}
			
			$implodedArray = implode("", $images);
			
            echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<title>Gallery</title>
				</head>
				<body>
					<h2>Gallery</h2>
					<a href='index.php'>Tillbaka</a><br>
					
					
				 
					$implodedArray
								
					
				</body>
				</html>
		";
        }
			if($this->didUserPressImage() == TRUE && isset($_SESSION['login']) && $this->didUserPressGallery() == TRUE){
				echo $_SESSION['login'];
				$deleteButton = "";
				$loggedInUser = $_SESSION['login'];
				$uploader = $this->galleryModel->GetUploader($this->getImageQueryString());
				
				if($loggedInUser == $uploader && $uploader != ""){
					$deleteButton = "<input type='submit' value='Ta bort'><br>";	
				}
				
				
				
				
				
				$image = $this->getImageQueryString();
				
				echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<title>Gallery</title>
				</head>
				<body>
					<h2>Gallery</h2>
					<a href='index.php?gallery'>Tillbaka</a><br>
					
					<form method='post'>
				 		$deleteButton
				 	</form>
					<img src='./UploadedImages/$image'>
					<p>Uploader: $uploader</p>
					<h2>Kommentarer</h2>
					Inga kommentarer<br>
					<textarea name='comment' id='comment' cols='40' rows='4'></textarea><br>
					<input type='submit' value='Posta'>			
					
				</body>
				</html>
		";
			}
		
		}
		
		public function getImageQueryString(){
			return $_GET['image'];
		}
		
		public function didUserPressGallery(){
		if(isset($_GET['gallery'])){
			echo "Tryckt på galleri!";
			return TRUE;
		}
		return FALSE;
	}
		public function didUserPressImage(){
			if(isset($_GET['image'])){
				echo "Tryckt på bild!";
				return TRUE;
			}
			return FALSE;
		}
			
		}