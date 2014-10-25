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
		// private $deleteCommentButton;
		// private $editCommentButton;
		//private $comment = array();
		private $galleryCommentID;
		private $galleryCommentArrayLength;
		private $commentValue;
		private $validMessage;
		private $commentArray2;
		
		
	public	function __construct() {
        $this->galleryModel = new galleryModel();
	}
		
		public function ValidateComment(){
			//TODO Fixa längden på poster
			
			//if(strlen($postedComment) > 200){
			//	echo "kommentaren är för lång";
			//	return $postedComment;
			//}
			
			if(strpos($this->postedComment,'<') !== false || strpos($this->postedComment,'>') !== false){
				
				return "The comment contains not valid characters!";
			}
			
			return "";
		}
		
		
		
		public function echoHTMLGallery($body){
			//$images = array();
			$this->loggedInUser = $this->galleryModel->GetLoggedInUser();
			
        	if(isset($this->loggedInUser) && $this->didUserPressGallery() == TRUE && $this->didUserPressImage() == FALSE){
			
			//Loopar igenom alla bilder och gör dom till klickbara länkar.

			foreach($body as $value){
				array_push($this->images, "<div class='gallerypics'><a href='galleryIndex.php?gallery&image=$value'><img src='./UploadedImages/$value'></a></div>");
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
		}
		
        public function echoHTML($body){
        	
			
			
			//if($this->didUserPressImage() == TRUE && isset($_SESSION['login']) && $this->didUserPressGallery() == TRUE){
				
				//Sparar vem som är inloggad
				$this->loggedInUser = $this->galleryModel->GetLoggedInUser();
				
				//Hämtar vem som laddat upp bilden som visas
				$this->uploader = $this->galleryModel->GetUploader($this->getImageQueryString());
				
				//Hämtar bildens namn
				$this->displayedImage = $this->getImageQueryString();
				
				//Innehåller en array med värden som ska jämföras med saker nedan för att rätt värden ska visas bland kommentarerna
				$this->commentArray = $this->galleryModel->GetCommentSession();
				
				//Kollar längden på arrayen ovanför.
				$this->galleryCommentArrayLength = count($this->commentArray);
				
				//Hämtar ut alla kommentarer från databasen som en string 
				$this->commentArray2 = $this->galleryModel->GetCommentsFromDB($this->displayedImage);
				
				
				
					// $this->deleteCommentButton = "";
					// $this->editCommentButton = "";
		
				//Visa ta bort en bild-knapp
				if($this->loggedInUser == $this->uploader && $this->uploader != "" || $this->loggedInUser == "Admin" && $this->uploader != ""){
					$this->deleteButton = "<form action='' method='post'><input type='submit' name='delete' value='Ta bort'><br></form>";	
				}
				
				//Borde byta namn på funktionen eftersom den inte returnerar true eller false utan vilken knapp som användaren tryckt på.
				$this->galleryCommentID = $this->didUserPressDeleteComment();
				
				
				//Var tvungen till att lägga header location här för att ta bort kommentar, i controllern ville den inte köra ordentligt.
				//Går endast att ta bort sina egna kommentarer även fast man ändrar hidden value. Någon annans kommentar går ej att ta bort.
				if($this->galleryCommentID != "" && $this->loggedInUser == $this->commentArray[$this->galleryCommentID]['user'] || $this->galleryCommentID != "" && $this->loggedInUser == "Admin"){
					header('Location: galleryIndex.php?gallery&image=' . $this->displayedImage);
				}
				
				
				
				
				//TODO Skapa en echo som visar det vanliga html dokumentet
				//Användaren trycker på edit så visas textbox med kommentaren ifyllt
				$this->galleryCommentID = $this->didUserPressEditComment(); 
				if($this->galleryCommentID != ""){
					$this->galleryModel->SetEditCommentIDSession($this->galleryCommentID);

					$commentToEditValue = $this->galleryModel->GetCommentToEdit($this->commentArray[$this->galleryCommentID]['commentID']);
					$editCommentTextField = "<form method='post'>
												<input type='text' name='editCommentTextField' value='$commentToEditValue'></input>
												<input type='submit' name='newComment' value='Post'></input>
											</form>";
				}
			
			
				
				
				$validateMsg = $this->galleryModel->GetValidationMessage();
				$this->galleryModel->UnsetValidationMessage();
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
					<a href='galleryIndex.php?gallery'>Tillbaka</a><br>
					
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
						
						$this->commentArray2
						
					</div>
					</div>
				</body>
				</html>
		";
			
		//}
		}

		
		public function GetEditValueFromTextbox(){
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
			
			for ($i=0; $i < $this->galleryCommentArrayLength; $i++) { 
				if(isset($_POST['editComment' .$i.''])){
					$this->galleryModel->SetCommentEditIDSession($_POST['editCommentshit' . $i . '']);
			
					return $_POST['deleteCommentshit'.$i.''];
				}
			}
		}

		public function didUserPressDeleteComment(){
			
			for ($i=0; $i < $this->galleryCommentArrayLength; $i++) { 
				if(isset($_POST['deleteComment' .$i.''])){
					$this->galleryModel->SetCommentDeleteIDSession($_POST['deleteCommentshit' . $i . '']);
				
					return $_POST['deleteCommentshit'.$i.''];
				}
			}
		}
		
		public function didUserPressPostComment(){
			if(isset($_POST['comment'])){
				return $_POST['comment'];
			}
			return "";
		}
		
		public function getImageQueryString(){
			return $_GET['image'];
		}
		
		public function didUserPressGallery(){
		if(isset($_GET['gallery'])){
			return TRUE;
		}
		return FALSE;
	}
		public function didUserPressImage(){
			if(isset($_GET['image'])){
				return TRUE;
			}
			return FALSE;
		}
		
		public function didUSerPressDelete(){
			
			if(isset($_POST['delete'])){
				return TRUE;
				
			}
			return FALSE;
			
		}
			
		}