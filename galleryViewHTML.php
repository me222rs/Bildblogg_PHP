<?php
	require_once 'galleryModel.php';
class galleryViewHTML{
        private $galleryModel;
		
	public	function __construct() {
        $this->galleryModel = new galleryModel();
		//$this->uploadView = new uploadView();
		//$this->uploadModel = new uploadModel();
	}
		
		public function ValidateComment($comment){
			//if(strlen($comment) > 200){
			//	echo "kommentaren är för lång";
			//	return $comment;
			//}
			
			if(strpos($comment,'<') !== false || strpos($comment,'>') !== false){
				//$strippedComment = filter_var($comment, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
				return "The comment contains not valid characters!";
			}
			//echo "kommentaren är ok";
			return "";
		}
		
        public function echoHTML($body){
        	
			$images = array();
			
        	if(isset($_SESSION['login']) && $this->didUserPressGallery() == TRUE && $this->didUserPressImage() == FALSE){
			
			//Loopar igenom alla bilder och gör dom till klickbara länkar.
			foreach($body as $value){
				array_push($images, "<div class='gallerypics'><a href='galleryView.php?gallery&image=$value'><img src='./UploadedImages/$value'></a></div>");
			}
			//Sätter ihop allt i arrayen för att sedan trycka ut det i html.
			$implodedArray = implode("", $images);
			
            echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<link rel='stylesheet' type='text/css' href='Css/styles1.css'>
					<title>Gallery</title>
				</head>
				<body>
				<div id='content'>
				<header><h1>Mickes fotosida</h1></header>
					<div id='pageNav'><h2>Gallery</h2></div>
					<a href='index.php'>Tillbaka</a><br>
					
					
				 	<div id='galleryDiv'>
						$implodedArray
					</div>
					<footer><p>Mickes fotosida</p></footer>				
					</div>
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
						//Visar knappar ifall att den inloggade användaren är den som lagt upp kommentaren
					
					if($commentArray[$i]['user'] == $loggedInUser || $commentArray[$i]['user'] != "Admin"){
						$deleteCommentButton = "<input type='submit' name='deleteComment" . $i . "' value='Delete'>";
						
						$editCommentButton = "<input type='submit' name='editComment" . $i . "' value='Edit'>";	
					}
					
					
					//Visar alla knappar för admin
					if($commentArray[$i]['user'] == "Admin" && $commentArray[$i]['user'] == $loggedInUser){
						$deleteCommentButton = "<input type='submit' name='deleteComment" . $i . "' value='Delete'>";
						
						$editCommentButton = "<input type='submit' name='editComment" . $i . "' value='Edit'>";	
					}
					
					//array med alla kommentarer redo för utskrift
					array_push($comment, "<form name='comments' method='post' id='comments'> 
											<input type='hidden' name='deleteCommentshit" , "$i", "' value='", "$i" , "'>"
											."  $deleteCommentButton $editCommentButton" , "<p><b>" , $commentArray[$i]['user'] , "</b></p>", "<p>" 
					, $commentArray[$i]['comment'], "</p><br><em>", $commentArray[$i]['date'], "</em></form>");
					
					$deleteCommentButton = "";
					$editCommentButton = "";
					//array_push($commentDate, $commentArray[$i]['date']);
				}

				$implodedArrayComment = implode("", $comment);
				//$implodedArrayDate = implode("", $commentDate);
				
				//Ta bort en bild
				if($loggedInUser == $uploader && $uploader != "" || $loggedInUser == "Admin" && $uploader != ""){
					$deleteButton = "<form action='' method='post'><input type='submit' name='delete' value='Ta bort'><br></form>";	
				}
				
				
				
				
				if($this->didUSerPressDelete() && $uploader == $loggedInUser || $this->didUSerPressDelete() && $loggedInUser == "Admin"){
					$this->galleryModel->DeleteImageFromFolder($displayedImage);
					header('Location: galleryView.php?gallery');
				}elseif($this->didUSerPressDelete() && $uploader != $loggedInUser){
					$deleteMessage = "Image could not be removed, retard...";
				}
				
				if($this->didUserPressPostComment() != ""){
					$validMessage = $this->ValidateComment($this->didUserPressPostComment());
					if($validMessage == ""){
						//$this->galleryModel->SetJavascriptMessage($validMessage);
						$this->galleryModel->PostComment($displayedImage, $this->didUserPressPostComment(), $loggedInUser);
						header('Location: galleryView.php?gallery&image=' . $displayedImage);
					}
					
				}
				$commentID = $this->didUserPressDeleteComment($commentArrayLength);
				
				//Detta körs inte om en användare ändrat värde på hiddenfield i html koden
				if($commentID != "" && $_SESSION['login'] == $commentArray[$commentID]['user'] || $commentID != "" && $_SESSION['login'] == "Admin"){
					$this->galleryModel->DeleteComment($commentArray[$commentID]['commentID']);
					
					header('Location: galleryView.php?gallery&image=' . $displayedImage);
				}
				
				$commentID = $this->didUserPressEditComment($commentArrayLength);
				//echo "commentID=";
				//echo $commentID;
				
				//if(!isset($_SESSION['bajs'])){
					//$_SESSION['bajs'] = $commentID;
					//echo "test=";
					//echo $_SESSION['bajs']; 
				//}
				 
				if($commentID != ""){
					//$this->galleryModel->EditComment($commentID, $commentValue);
					$commentToEditValue = $this->galleryModel->GetCommentToEdit($commentArray[$commentID]['commentID']);
					$editCommentTextField = "<form method='post'>
												<input type='text' name='editCommentTextField' value='$commentToEditValue'></input>
												<input type='submit' name='newComment' value='Post'></input>
											</form>";
				}
				
				
				if($this->didUserPressPostEditedComment() == TRUE && isset($_SESSION['login']) && $_SESSION['login'] == $commentArray[$commentID]['user']){
					
					$commentValue = $this->GetEditValueFromTextbox();
					$validMessage = $this->ValidateComment($this->didUserPressPostComment());
					if($validMessage == ""){
						$success = $this->galleryModel->EditComment($commentArray[$commentID]['commentID'], $commentValue);
						if($success == TRUE){
							header('Location: galleryView.php?gallery&image=' . $displayedImage);
						}
					}
					//$commentID = 
					//echo $commentValue;
					//echo "kommer in i bajs";
					//echo $commentID;
					//echo $commentArray[$commentID]['commentID'];
					
					//header('Location: galleryView.php?gallery&image=' . $displayedImage);
					//unset($_SESSION['bajs']);
					//if($this->didUserPressPostEditedComment() == TRUE){
						//unset($_SESSION['bajs']);
					//}
					//header('Location: galleryView.php?gallery&image=' . $displayedImage);
					
					
					//if($this->didUserPressEditComment2() == TRUE){
						//unset($_SESSION['bajs']);
				//}
					
				}
				
				
				
				//$jsMessage = $this->galleryModel->GetJavascriptMessage();
				//$this->galleryModel->UnsetJavascriptMessage();
				$image = $this->getImageQueryString();
				
				echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<link rel='stylesheet' type='text/css' href='Css/styles1.css'>
					<title>Gallery</title>
				</head>
				<body>
				<div id='content'>
					<header><h1>Mickes fotosida</h1></header>
					<div id='pageNav'><h2>Gallery</h2></div>
					<a href='galleryView.php?gallery'>Tillbaka</a><br>
					
					<div id='oneImage'>	
				 		$deleteButton
				 		$deleteMessage
						<img src='./UploadedImages/$image'>
						<p>Uploader: $uploader</p>
					</div>
					
					
					<form method='post' id='commentField'>
						<h2>Comment</h2>
						<textarea name='comment' id='comment' cols='40' rows='4'></textarea><br>
						<input type='submit' name='PostComment' value='Posta'>
					</form>
					$editCommentTextField
					
					<div id='pageNav'><h3>Comments</h3></div>
					<div id='CommentBox'>
						
						$implodedArrayComment
						
					</div>
					</div>
				</body>
				</html>
		";
			}
		
		}

		
		public function GetEditValueFromTextbox(){
			return $_POST['editCommentTextField'];
		}
		
		public function didUserPressPostEditedComment(){
			if(isset($_POST['newComment'])){
				return TRUE;
			}
			return TRUE;
		}
		public function didUserPressEditComment2(){
			if(isset($_POST['editComment'])){
				return TRUE;
			}
			return fALSE;
		}
		public function didUserPressEditComment($commentArrayLength){
			echo "Tryckt på edit comment";
			for ($i=0; $i < $commentArrayLength; $i++) { 
				if(isset($_POST['editComment' .$i.''])){
					return $_POST['deleteCommentshit'.$i.''];
				}
			}
		}

		public function didUserPressDeleteComment($commentArrayLength){
			echo "Tryckt på delete comment";
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