<?php
	require_once 'galleryModel.php';
class galleryViewHTML{
        private $galleryModel;
		private $images = array();
		private $postedComment;
		private $implodedArray = array();
		private $deleteButton = "";
		private $deleteMessage = "";
		private $loggedInUser;
		private $commentArray = array();
		private $uploader;
		private $displayedImage;
		private $deleteCommentButton;
		private $editCommentButton;
		private $comment = array();
		private $commentID;
		private $commentArrayLength;
		private $commentValue;
		private $validMessage;
		
		
	public	function __construct() {
        $this->galleryModel = new galleryModel();
		//$this->uploadView = new uploadView();
		//$this->uploadModel = new uploadModel();
	}
		
		public function ValidateComment(){
			//if(strlen($comment) > 200){
			//	echo "kommentaren är för lång";
			//	return $comment;
			//}
			
			if(strpos($this->postedComment,'<') !== false || strpos($this->postedComment,'>') !== false){
				echo "Innehåller otillåtna tecken";
				//$strippedComment = filter_var($comment, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
				return "The comment contains not valid characters!";
			}
			//echo "kommentaren är ok";
			return "";
		}
		
        public function echoHTML($body){
        	
			//$images = array();
			
        	if(isset($_SESSION['login']) && $this->didUserPressGallery() == TRUE && $this->didUserPressImage() == FALSE){
			
			//Loopar igenom alla bilder och gör dom till klickbara länkar.

						foreach($body as $value){
				array_push($this->images, "<div class='gallerypics'><a href='galleryView.php?gallery&image=$value'><img src='./UploadedImages/$value'></a></div>");
			}
			//Sätter ihop allt i arrayen för att sedan trycka ut det i html.
			$this->implodedArray = implode("", $this->images);
			
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
						$this->implodedArray
					</div>
					<footer><p>Mickes fotosida</p></footer>				
					</div>
				</body>
				</html>
		";
        }
			
			if($this->didUserPressImage() == TRUE && isset($_SESSION['login']) && $this->didUserPressGallery() == TRUE){
				echo $_SESSION['login'];
				
				$this->loggedInUser = $_SESSION['login'];
				$this->uploader = $this->galleryModel->GetUploader($this->getImageQueryString());
				$this->displayedImage = $this->getImageQueryString();
				
				
				
				$this->commentArray = $this->galleryModel->GetCommentsFromDB($this->displayedImage);

				$this->comment = array();
				//$commentDate = array();

				$this->commentArrayLength = count($this->commentArray);
				
				
				for($i = 0; $i < $this->commentArrayLength; $i++){
						//Visar knappar ifall att den inloggade användaren är den som lagt upp kommentaren
					
					if($this->commentArray[$i]['user'] == $this->loggedInUser || $this->commentArray[$i]['user'] != "Admin"){
						$this->deleteCommentButton = "<input type='submit' name='deleteComment" . $i . "' value='Delete'>";
						
						$this->editCommentButton = "<input type='submit' name='editComment" . $i . "' value='Edit'>";	
					}
					
					
					//Visar alla knappar för admin
					if($this->commentArray[$i]['user'] == "Admin" && $this->commentArray[$i]['user'] == $this->loggedInUser){
						$this->deleteCommentButton = "<input type='submit' name='deleteComment" . $i . "' value='Delete'>";
						
						$this->editCommentButton = "<input type='submit' name='editComment" . $i . "' value='Edit'>";	
					}
					
					//array med alla kommentarer redo för utskrift
					array_push($this->comment, "<form name='comments' method='post' id='comments'> 
											<input type='hidden' name='deleteCommentshit" , "$i", "' value='", "$i" , "'>"
											."  $this->deleteCommentButton $this->editCommentButton" , "<p><b>" , $this->commentArray[$i]['user'] , "</b></p>", "<p>" 
					, $this->commentArray[$i]['comment'], "</p><br><em>", $this->commentArray[$i]['date'], "</em></form>");
					
					$this->deleteCommentButton = "";
					$this->editCommentButton = "";
					//array_push($commentDate, $commentArray[$i]['date']);
				}

				$implodedArrayComment = implode("", $this->comment);
				//$implodedArrayDate = implode("", $commentDate);
				
				//Ta bort en bild
				if($this->loggedInUser == $this->uploader && $this->uploader != "" || $this->loggedInUser == "Admin" && $this->uploader != ""){
					$this->deleteButton = "<form action='' method='post'><input type='submit' name='delete' value='Ta bort'><br></form>";	
				}
				
				
				
				
				if($this->didUSerPressDelete() && $this->uploader == $this->loggedInUser || $this->didUSerPressDelete() && $this->loggedInUser == "Admin"){
					$this->galleryModel->DeleteImageFromFolder($this->displayedImage);
					header('Location: galleryView.php?gallery');
				}elseif($this->didUSerPressDelete() && $this->uploader != $this->loggedInUser){
					$this->deleteMessage = "Image could not be removed, retard...";
				}
				
				if($this->didUserPressPostComment() != ""){
					$this->postedComment = $this->didUserPressPostComment();
					$this->validMessage = $this->ValidateComment();
					if($this->validMessage == ""){
						//$this->galleryModel->SetJavascriptMessage($validMessage);
						$this->galleryModel->PostComment($this->displayedImage, $this->didUserPressPostComment(), $this->loggedInUser);
						header('Location: galleryView.php?gallery&image=' . $this->displayedImage);
					}
					
				}
				$this->commentID = $this->didUserPressDeleteComment();
				
				//Detta körs inte om en användare ändrat värde på hiddenfield i html koden
				if($this->commentID != "" && $_SESSION['login'] == $this->commentArray[$this->commentID]['user'] || $this->commentID != "" && $_SESSION['login'] == "Admin"){
					$this->galleryModel->DeleteComment($this->commentArray[$this->commentID]['commentID']);
					
					header('Location: galleryView.php?gallery&image=' . $this->displayedImage);
				}
				
				$this->commentID = $this->didUserPressEditComment();
				//echo "commentID=";
				//echo $commentID;
				
				//if(!isset($_SESSION['bajs'])){
					//$_SESSION['bajs'] = $commentID;
					//echo "test=";
					//echo $_SESSION['bajs']; 
				//}
				 
				if($this->commentID != ""){
					//$this->galleryModel->EditComment($commentID, $commentValue);
					$commentToEditValue = $this->galleryModel->GetCommentToEdit($this->commentArray[$this->commentID]['commentID']);
					$editCommentTextField = "<form method='post'>
												<input type='text' name='editCommentTextField' value='$commentToEditValue'></input>
												<input type='submit' name='newComment' value='Post'></input>
											</form>";
				}
			
				//if($_SESSION['commentUser'] != ""){
					//$_SESSION['commentUser'] = $commentArray[$commentID]['user'];
				//}
				
				if($_SESSION['login'] == $_SESSION['commentUser'] || $_SESSION['login'] == "Admin"){
					echo "BAJS";
					$this->postedComment = $this->didUserPressPostEditedComment();
					$this->commentValue = $this->GetEditValueFromTextbox();
					$this->validMessage = $this->ValidateComment();
					if($this->validMessage == ""){
						$success = $this->galleryModel->EditComment($this->commentArray[$this->commentID]['commentID'], $this->commentValue);
						if($success == TRUE){
							header('Location: galleryView.php?gallery&image=' . $this->displayedImage);
						}
					}
					
						
				}
				
				if($this->didUserPressPostEditedComment() == TRUE && isset($_SESSION['login']) && $_SESSION['login'] == $this->commentArray[$this->commentID]['user']){
					$_SESSION['commentUser'] = $this->commentArray[$this->commentID]['user'];
					echo "KOmmer in i edit post";
									echo ">>>>";
				echo $this->commentArray[$this->commentID]['user'];
				echo "<<<<";
					$this->commentValue = $this->GetEditValueFromTextbox();
					echo $this->commentValue;
					$this->validMessage = $this->ValidateComment($this->didUserPressPostComment());
					if($this->validMessage == ""){
						$this->galleryModel->SaveValidationMessage($this->validMessage);
						$success = $this->galleryModel->EditComment($this->commentArray[$this->commentID]['commentID'], $this->commentValue);
						if($success == TRUE){
							header('Location: galleryView.php?gallery&image=' . $this->displayedImage);
						}
						
					}
	
					
				}
				
				
				$validateMsg = $this->galleryModel->GetValidationMessage();
				$this->galleryModel->UnsetValidationMessage();
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
				 		$this->deleteButton
				 		$this->deleteMessage
						<img src='./UploadedImages/$image'>
						<p>Uploader: $this->uploader</p>
					</div>
					
					$validateMsg
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
			echo "Hämtar value från textbox";
			echo $_POST['editCommentTextField'];
			return $_POST['editCommentTextField'];
		}
		
		public function didUserPressPostEditedComment(){
			if(isset($_POST['editCommentTextField'])){
				return $_POST['editCommentTextField'];
			}
			return TRUE;
		}
		public function didUserPressEditComment2(){
			if(isset($_POST['editComment'])){
				return TRUE;
			}
			return fALSE;
		}
		public function didUserPressEditComment(){
			echo "Tryckt på edit comment";
			for ($i=0; $i < $this->commentArrayLength; $i++) { 
				if(isset($_POST['editComment' .$i.''])){
					return $_POST['deleteCommentshit'.$i.''];
				}
			}
		}

		public function didUserPressDeleteComment(){
			echo "Tryckt på delete comment";
			for ($i=0; $i < $this->commentArrayLength; $i++) { 
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