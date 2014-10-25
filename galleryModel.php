<?php

require_once 'modelLogin.php';
	class galleryModel {
		private $model;
		private $connectionString = "mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8";
		private $connectionUsername = "root";
		private $connectionPassword = "";
		private $db;
		private $stmt;
		private $commentID;
		private $comment;
		private $displayedGalleryImage;
		private $user;
		private $commentUser;
		private $deleteCommentButton = "";
		private $editCommentButton = "";
		private $arrayWithComments = array();
		
	
   	 		public function __construct(){
        		//$this->model = new modelLogin();
				$this->db = new PDO($this->connectionString, $this->connectionUsername, $this->connectionPassword);	
    		}
			
		public function SaveValidationMessage($validMessage){
			$_SESSION['validationMessage'] = $validMessage;
			
		}
		
		public function GetValidationMessage(){
			return $_SESSION['validationMessage'];
		}
		
		public function UnsetValidationMessage(){
			$_SESSION['validationMessage'] = NULL;
		}	
			
			public function GetUploader($imageUploader){

				
				$returnString = "";
				
				//$db = new PDO($this->connectionString, $this->connectionUsername, $this->connectionPassword);
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				
				$this->stmt = $this->db->prepare("SELECT upLoaderID FROM images WHERE imageName=?");
				$this->stmt->execute(array($imageUploader));
				
				$rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);

				
				return $rows[0]["upLoaderID"];
				
				
				 
				 
				}
			
			
			public function GetCommentToEdit($commentID){
				
				$this->stmt = $this->db->prepare("SELECT comment FROM comments WHERE commentID=:commentID");
				$this->stmt->execute(array(':commentID' => $commentID));
				$rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
				return $rows[0]['comment'];
			}
			
			public function SetEditCommentIDSession($value){
				
				$_SESSION['editCommentID'] = $value;
			}
			
			public function EditComment($commentID, $comment){
				$commentArray = $_SESSION['commentArray'];
				
				$this->commentID = $_SESSION['editCommentID'];
				$this->commentID = $commentArray[$this->commentID]['commentID'];
				$this->commentUser = $commentArray[$_SESSION['editCommentID']]['user'];

				if($this->commentUser == $_SESSION['login']){
					
									$this->comment = $comment;
				
				if($this->commentID != ""){
					$_SESSION['editCommentID'] = $this->commentID;	
				}
				 if($this->commentID != ""){
					 $_SESSION['commentUser'] == NULL;
				 }
				if($this->comment != ""){
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->stmt = $this->db->prepare("UPDATE comments SET comment=? WHERE commentID=?");
				$this->stmt->execute(array($this->comment, $this->commentID));
				$affected_rows = $this->stmt->rowCount();
				if($affected_rows != ""){
					return true;
				} 
				else{
					return false;
				}
				}
					
				}
				//Redigera kommentar man själv lagt upp
				

				
				
			}
			
			public function GetCommentsFromArray(){
				return $_SESSION['commentArray'];
			}
			public function UnsetCommentsArray(){
				$_SESSION['commentArray'] == NULL;
			}
			public function GetCommentToEditSession(){
				return $_SESSION['editCommentID'];
			}
			public function GetCommentToDeleteSession(){
				return $_SESSION['commentDeleteID'];
			}
public function UnsetCommentDeleteSession(){
	$_SESSION['commentDeleteID'] = NULL;
}
			
			public function DeleteComment($commentID){

				//Ta bort en kommentar man själv lagt upp
				$this->commentID = $commentID;
				
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->stmt = $this->db->prepare("DELETE FROM comments WHERE commentID=:commentID");
				$this->stmt->bindValue(':commentID', $this->commentID, PDO::PARAM_STR);
				$this->stmt->execute();
				
				
			}
			
			public function PostComment($displayedGalleryImage, $comment, $user){
				
				//Posta en kommentar till någons bild 
				$this->displayedGalleryImage = $displayedGalleryImage;
				$this->comment = $comment;
				$this->user = $user;
				if(strpos($this->comment,'<') !== false || strpos($this->comment,'>') !== false){
					$_SESSION['PostMessage'] = "Posten innehåller ogiltiga tecken!";
					return;
				}
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->stmt = $this->db->prepare("INSERT INTO comments(comment,imageName,user) VALUES(:comment,:imageName,:user)");
					$this->stmt->execute(array(':comment' => $this->comment, ':imageName' => $this->displayedGalleryImage, ':user' => $this->user));
				
				
			}
			
			public function GetCommentsFromDB($displayedGalleryImage){
				//Hämta alla kommentarer till en bild
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->displayedGalleryImage = $displayedGalleryImage;
				$rows = array();
				
				$this->stmt = $this->db->prepare("SELECT * FROM comments WHERE imageName=?");
				$this->stmt->execute(array($this->displayedGalleryImage));
				$this->arrayWithComments = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$_SESSION['commentArray'] = $this->arrayWithComments;
				
				$commentArrayLength = count($this->arrayWithComments);
				$comment = array();
				for($i = 0; $i < $commentArrayLength; $i++){
					//Visar knappar ifall att den inloggade användaren är den som lagt upp kommentaren
					
					if($this->arrayWithComments[$i]['user'] == $_SESSION['login'] || $this->arrayWithComments[$i]['user'] != "Admin"){
						$this->deleteCommentButton = "<input type='submit' name='deleteComment" . $i . "' value='Delete'>";
						$this->editCommentButton = "<input type='submit' name='editComment" . $i . "' value='Edit'>";	
					}
					
					
					//Visar alla knappar för admin
					if($this->arrayWithComments[$i]['user'] == "Admin" && $this->arrayWithComments[$i]['user'] == $_SESSION['login']){
						$this->deleteCommentButton = "<input type='submit' name='deleteComment" . $i . "' value='Delete'>";
						$this->editCommentButton = "<input type='submit' name='editComment" . $i . "' value='Edit'>";	
					}
					
					//array med alla kommentarer redo för utskrift
					array_push($comment, "<form name='comments' method='post' id='comments'> 
											<input type='hidden' name='deleteCommentshit" , "$i", "' value='", "$i" , "'>"
											."  $this->deleteCommentButton $this->editCommentButton" , "<p><b>" , $this->arrayWithComments[$i]['user'] , "</b></p>", "<p>" 
					, $this->arrayWithComments[$i]['comment'], "</p><br><em>", $this->arrayWithComments[$i]['date'], "</em></form>");
					
					$this->deleteCommentButton = "";
					$this->editCommentButton = "";
				}

				$implodedArrayComment = implode("", $comment);
				
				
				
				return $implodedArrayComment;
			}
			
			public function GetCommentSession(){
				return $_SESSION['commentArray'];
			}
			
			public function DeleteImageFromDB($displayedGalleryImage){
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->displayedGalleryImage = $displayedGalleryImage;
				$this->stmt = $this->db->prepare("DELETE FROM images WHERE imageName=?");
				$this->stmt->execute(array($this->displayedGalleryImage));
				
				$this->stmt = $this->db->prepare("DELETE FROM comments WHERE imageName=?");
				$this->stmt->execute(array($this->displayedGalleryImage));
				
				
			}
			
			public function SetCommentEditIDSession($value){
				$_SESSION['commentEditID'] = $value;
			}
			
			public function SetCommentDeleteIDSession($value){
				$_SESSION['commentDeleteID'] = $value;
			}
			
			public function GetLoggedInUser(){
				return $_SESSION['login'];
			}
			
			public function DeleteImageFromFolder($displayedGalleryImage){
				$this->displayedGalleryImage = $displayedGalleryImage;
				@unlink('UploadedImages/'.$this->displayedGalleryImage);
				
				$this->DeleteImageFromDB($this->displayedGalleryImage);
				
			}
			
			public function GetAllImagesFromDB(){
				
				$connection = mysqli_connect("127.0.0.1", $this->connectionUsername, $this->connectionPassword, "loginlabb4");
    			if (mysqli_connect_errno($connection)){
        			echo "MySql Error: " . mysqli_connect_error();
    			}
				$result = mysqli_query($connection,"SELECT imageName FROM images");
				
				$imageArray = array();
				while ($row = mysqli_fetch_assoc($result)) {
       				array_push($imageArray, $row['imageName']);
   				}
				
    			$row = mysqli_fetch_array($result);

				
				

   				return $imageArray;
				
				
			}
			
			public function ShowAllImages(){
				$imagesArray = array();
				$dbArray = array();
				
				
				$dbArray = $this->GetAllImagesFromDB();
				
				$dir = "./UploadedImages/";

			
				
				
				
	foreach($dbArray as $file) {
		array_push($imagesArray, $file);
		
	}
				
				return $imagesArray;
				
				
			}
	}
