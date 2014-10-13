<?php

class galleryViewHTML{
        
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
			if($this->didUserPressImage() && isset($_SESSION['login']) && $this->didUserPressGallery() == TRUE){
				
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
					<a href='index.php'>Tillbaka</a><br>
					
					
				 	<input type='submit' value='Ta bort'><br>
				 	
					<img src='./UploadedImages/$image'>
					
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