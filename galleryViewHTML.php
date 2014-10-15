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
			
			//Loopar igenom alla bilder och gör dom till klickbara länkar.
			foreach($body as $value){
				array_push($images, "<a href='galleryView.php?gallery&image=$value'><img src='./UploadedImages/$value'></a>");
			}
			//Sätter ihop allt i arrayen för att sedan trycka ut det i html.
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
				
				$commentArray = $this->galleryModel->GetCommentsFromDB($displayedImage);

				$comment = array();
				$commentDate = array();

				$commentArrayLength = count($commentArray);
				
				
				for($i = 0; $i < $commentArrayLength; $i++){
					if($commentArray[$i]['user'] == $loggedInUser){
						$deleteCommentButton = "<input type='submit' name='deleteComment" . $i . "' value='Delete'>";	
					}
					
					array_push($comment, "<form name='comments' method='post'> 
											<input type='hidden' name='deleteCommentshit" , "$i", "' value='", "$i" , "'>"
											."  $deleteCommentButton " , "<p><b>" , $commentArray[$i]['user'] , "</b></p>", "<p>" 
					, $commentArray[$i]['comment'], "</p><br><em>", $commentArray[$i]['date'], "</em></form>");
					
					$deleteCommentButton = "";
					//array_push($commentDate, $commentArray[$i]['date']);
				}

				$implodedArrayComment = implode("", $comment);
				//$implodedArrayDate = implode("", $commentDate);
				
				if($loggedInUser == $uploader && $uploader != ""){
					$deleteButton = "<form action='' method='post'><input type='submit' name='delete' value='Ta bort'><br></form>";	
				}
				
				if($this->didUSerPressDelete() && $uploader == $loggedInUser){
					$this->galleryModel->DeleteImageFromFolder($displayedImage);
					header('Location: galleryView.php?gallery');
				}elseif($this->didUSerPressDelete() && $uploader != $loggedInUser){
					$deleteMessage = "Image could not be removed, retard...";
				}
				
				if($this->didUserPressPostComment() != ""){
					$this->galleryModel->PostComment($displayedImage, $this->didUserPressPostComment(), $loggedInUser);
					header('Location: galleryView.php?gallery&image=' . $displayedImage);
				}
				$commentID = $this->didUserPressDeleteComment($commentArrayLength);
				if($commentID != ""){
					$this->galleryModel->DeleteComment($commentArray[$commentID]['commentID']);
					header('Location: galleryView.php?gallery&image=' . $displayedImage);
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
					<a href='galleryView.php?gallery'>Tillbaka</a><br>
					
					
				 		$deleteButton
				 		$deleteMessage
					<img src='./UploadedImages/$image'>
					<p>Uploader: $uploader</p>
					
					
					
					<form method='post'>
						<h2>Comment</h2>
						<textarea name='comment' id='comment' cols='40' rows='4'></textarea><br>
						<input type='submit' name='PostComment' value='Posta'>
					</form>
								
					
					<h3>Comments</h3>
					<div id='CommentBox'>
						
						$implodedArrayComment
						
					</div>
				</body>
				</html>
		";
			}
		
		}

		public function didUserPressDeleteComment($commentArrayLength){
			for ($i=0; $i < $commentArrayLength; $i++) { 
				if(isset($_POST['deleteComment' .$i.''])){
					return $_POST['deleteCommentshit'.$i.''];
				}
			}
			// if(isset($_POST['deleteComment0'])){
				// echo "Tryckt på Ta bort kommentar!";
				// var_dump($_POST);
				// return $_POST['deleteCommentshit0'];
			// }
		}
		
		public function didUserPressPostComment(){
			if(isset($_POST['comment'])){
				echo "Tryckt på Posta";
				return $_POST['comment'];
			}
			return "";
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