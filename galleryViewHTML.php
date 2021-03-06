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
		private $galleryCommentID;
		private $galleryCommentArrayLength;
		private $commentValue;
		private $validMessage;
		private $commentArray2;
		private $commentErrorMess;
		private $image;
		
		
	public	function __construct() {
        $this->galleryModel = new galleryModel();
	}
		
		
		
		
		public function echoHTMLGallery($body){
			//$images = array();
			$this->loggedInUser = $this->galleryModel->GetLoggedInUser();
			
        	if(isset($this->loggedInUser) && $this->didUserPressGallery() == TRUE && $this->didUserPressImage() == FALSE){
			
			//Loopar igenom alla bilder och gör dom till klickbara länkar.

			foreach($body as $value){
				array_push($this->images, "<div class='gallerypics'><a href='galleryIndex.php?gallery&image=$value'><img src='./UploadedImages/$value' alt='$value'></a></div>");
			}
			//Sätter ihop allt i arrayen för att sedan trycka ut det i html.
			$this->implodedArray = implode("", $this->images);
			$this->commentErrorMess = $this->galleryModel->GetCommentErrorMessage();
			$this->galleryModel->UnsetCommentErrorMessage();
			
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
					<div class='pageNav'><h2>Gallery</h2></div>
					<a href='index.php'>Tillbaka</a><br>
					$this->commentErrorMess
					
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
        	
				
				//Sparar vem som är inloggad
				$this->loggedInUser = $this->galleryModel->GetLoggedInUser();
				
				//Hämtar vem som laddat upp bilden som visas
				$this->uploader = $this->galleryModel->GetUploader($this->getImageQueryString());
				
				//Hämtar bildens namn
				$this->displayedImage = $this->getImageQueryString();
				
				//Innehåller en array med värden som ska jämföras med saker nedan för att rätt värden ska visas bland kommentarerna
				$this->commentArray = $this->galleryModel->GetCommentsFromArray();
				
				//Kollar längden på arrayen ovanför.
				$this->galleryCommentArrayLength = count($this->commentArray);
				
				//Hämtar ut alla kommentarer från databasen som en string 
				$this->commentArray2 = $this->galleryModel->GetCommentsFromDB($this->displayedImage);
				
				
		
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
				
				
				
				

				//Användaren trycker på edit så visas textbox med kommentaren ifyllt
				$this->galleryCommentID = $this->didUserPressEditComment(); 
				if($this->galleryCommentID != ""){
					$this->galleryModel->SetEditCommentIDSession($this->galleryCommentID);

					$commentToEditValue = $this->galleryModel->GetCommentToEdit($this->commentArray[$this->galleryCommentID]['commentID']);
					$editCommentTextField = "<div id='editTextBox'><form method='post'>
												<textarea name='editCommentTextField' value='' cols='40' rows='4' maxlength='500'>$commentToEditValue</textarea>
												<input type='submit' name='newComment' value='Post'></input>
											</form></div>";
				}
			
			
				
				
				$validateMsg = $this->galleryModel->GetValidationMessage();
				$this->galleryModel->UnsetValidationMessage();
				$this->image = $this->getImageQueryString();
				
				$this->commentErrorMess = $this->galleryModel->GetCommentErrorMessage();
				//$this->galleryModel->UnsetCommentErrorMessage();
				
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
					<div class='pageNav'><h2>Gallery</h2></div>
					<a href='galleryIndex.php?gallery'>Tillbaka</a><br>
	
					<div id='oneImage'>	
				 		$this->deleteButton
				 		$this->deleteMessage
						<img src='./UploadedImages/$this->image' alt='$this->image'>
						<p>Uploader: $this->uploader</p>
					</div>
					
					$validateMsg
					<form method='post' id='commentField'>
						<h2>Comment</h2>
						<p>Max 500 tecken! Ogiltiga tecken är < & ></p>
						<textarea name='comment' id='comment' cols='40' rows='4' maxlength='500'></textarea><br>
						<input type='submit' name='PostComment' value='Posta'>
					</form>
					$this->commentErrorMess
					$editCommentTextField
					
					<div class='pageNav'><h3>Comments</h3></div>
					<div id='CommentBox'>
						
						$this->commentArray2
						
					</div>
					</div>
				</body>
				</html>
		";
			$this->galleryModel->UnsetCommentErrorMessage();
		//}
		}

		//Hämtar värdet från textboxen för redigering
		public function GetEditValueFromTextbox(){
			return $_POST['editCommentTextField'];
		}
		
		public function didUserPressPostEditedComment(){
			if(isset($_POST['editCommentTextField'])){
				return $_POST['editCommentTextField'];
			}
			return TRUE;
		}
		//Kollar om användaren tryckt på redigera
		public function didUserPressEditComment2(){
			if(isset($_POST['editComment'])){
				return TRUE;
			}
			return fALSE;
		}
		//returnerar i vilket kommentarsformulär användaren tryckt på redigera
		public function didUserPressEditComment(){
			
			for ($i=0; $i < $this->galleryCommentArrayLength; $i++) { 
				if(isset($_POST['editComment' .$i.''])){
					$this->galleryModel->SetCommentEditIDSession($_POST['editCommentshit' . $i . '']);
			
					return $_POST['deleteCommentshit'.$i.''];
				}
			}
		}
		//returnerar i vilket kommentarsformulär användaren tryckt på delete
		public function didUserPressDeleteComment(){
			
			for ($i=0; $i < $this->galleryCommentArrayLength; $i++) { 
				if(isset($_POST['deleteComment' .$i.''])){
					$this->galleryModel->SetCommentDeleteIDSession($_POST['deleteCommentshit' . $i . '']);
				
					return $_POST['deleteCommentshit'.$i.''];
				}
			}
		}
		// HAr användaren postat en kommentar?
		public function didUserPressPostComment2(){
			if(isset($_POST['comment'])){
				return TRUE;
			}
			return FALSE;
		}
		//returnerar den postade kommentaren
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