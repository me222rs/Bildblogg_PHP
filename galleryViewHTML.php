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
				$displayedImage = $this->getImageQueryString();
				$deleteMessage = "";
				$commentArray = array();
				
				$commentArray = $this->galleryModel->GetCommentsFromDB();
				
				if($loggedInUser == $uploader && $uploader != ""){
					$deleteButton = "<form action='' method='post'><input type='submit' name='delete' value='Ta bort'><br></form>";	
				}
				
				if($this->didUSerPressDelete() && $uploader == $loggedInUser){
					$this->galleryModel->DeleteImageFromFolder($displayedImage);
					header('Location: galleryView.php?gallery');
				}elseif($this->didUSerPressDelete() && $uploader != $loggedInUser){
					$deleteMessage = "Image could not be removed, retard...";
				}
				
				if($this->didUserPressPostComment()){
					$this->galleryModel->PostComment($displayedImage);
				}
				
				$comment = "					
					<div class='CommentBox'>
						<p>Comment from: $userExample</p>
						<p>$userCommentExample</p>
						<p>$datePostedExample</p>
					</div>";
				
				
				
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
					
					
				 		$deleteButton
				 		$deleteMessage
					<img src='./UploadedImages/$image'>
					<p>Uploader: $uploader</p>
					
					<h2>Comment</h2>
					<textarea name='comment' id='comment' cols='40' rows='4'></textarea><br>
					<input type='submit' name='PostComment' value='Posta'>			
					
					<h3>Comments</h3>
					<div id='CommentBox'>
						<p>Comment from: Micke</p>
						<p>This is a comment.</p>
						<p>2014-10-14 14:30:00</p>
					</div>
				</body>
				</html>
		";
			}
		
		}
		
		public function didUserPressPostComment(){
			if(isset($_POST['PostComment'])){
				return TRUE;
			}
			return FALSE;
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
		
		public function didUSerPressDelete(){
			
			if(isset($_POST['delete'])){
				echo "Tryckt på delete!";
				return TRUE;
				
			}
			return FALSE;
			
		}
			
		}