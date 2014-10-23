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
		private $displayedImage;
		private $user;
		private $commentUser;
		
	
   	 		public function __construct(){
        		$this->model = new modelLogin();
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
			
			public function GetUploader($loggedInUser){

				
				$returnString = "";
				
				//$db = new PDO($this->connectionString, $this->connectionUsername, $this->connectionPassword);
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				
				$this->stmt = $this->db->prepare("SELECT upLoaderID FROM images WHERE imageName=?");
				$this->stmt->execute(array($loggedInUser));
				
				$rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
				//var_dump($rows);
				//echo $rows[0];
				
				return $rows[0]["upLoaderID"];
				
				
				 
				 
				}
			
			
			public function GetCommentToEdit($commentID){
				//echo $commentID;
				
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->stmt = $this->db->prepare("SELECT comment FROM comments WHERE commentID=:commentID");
				$this->stmt->execute(array(':commentID' => $commentID));
				$rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
				return $rows[0]['comment'];
			}
			
			public function EditComment($commentID, $comment){
				echo "BAAAAAAJS";
				$commentArray = $_SESSION['commentArray'];
				var_dump($commentArray);
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
			
			public function DeleteComment($commentID){
				echo "Koden kommer hit";
				//Ta bort en kommentar man själv lagt upp
				$this->commentID = $commentID;
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->stmt = $this->db->prepare("DELETE FROM comments WHERE commentID=:commentID");
				$this->stmt->bindValue(':commentID', $this->commentID, PDO::PARAM_STR);
				$this->stmt->execute();
				
			}
			
			public function PostComment($displayedImage, $comment, $user){
				//Posta en kommentar till någons bild 
				$this->displayedImage = $displayedImage;
				$this->comment = $comment;
				$this->user = $user;
				if(strpos($this->comment,'<') !== false || strpos($this->comment,'>') !== false){
					return;
				}
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->stmt = $this->db->prepare("INSERT INTO comments(comment,imageName,user) VALUES(:comment,:imageName,:user)");
					$this->stmt->execute(array(':comment' => $this->comment, ':imageName' => $this->displayedImage, ':user' => $this->user));
				
				
			}
			
			public function GetCommentsFromDB($displayedImage){
				//Hämta alla kommentarer till en bild
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->displayedImage = $displayedImage;
				$rows = array();
				
				$this->stmt = $this->db->prepare("SELECT * FROM comments WHERE imageName=?");
				$this->stmt->execute(array($this->displayedImage));
				$commentArray = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$_SESSION['commentArray'] = $commentArray;
				
				$commentArrayLength = count($commentArray);
				$comment = array();
				for($i = 0; $i < $commentArrayLength; $i++){
					//Visar knappar ifall att den inloggade användaren är den som lagt upp kommentaren
					
					if($commentArray[$i]['user'] == $_SESSION['login'] || $commentArray[$i]['user'] != "Admin"){
						$deleteCommentButton = "<input type='submit' name='deleteComment" . $i . "' value='Delete'>";
						$editCommentButton = "<input type='submit' name='editComment" . $i . "' value='Edit'>";	
					}
					
					
					//Visar alla knappar för admin
					if($commentArray[$i]['user'] == "Admin" && $commentArray[$i]['user'] == $_SESSION['login']){
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
				}

				$implodedArrayComment = implode("", $comment);
				
				
				
				return $implodedArrayComment;
			}
			
			public function GetCommentSession(){
				return $_SESSION['commentArray'];
			}
			
			public function DeleteImageFromDB($displayedImage){
				//$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$this->displayedImage = $displayedImage;
				$this->stmt = $this->db->prepare("DELETE FROM images WHERE imageName=?");
				$this->stmt->execute(array($this->displayedImage));
				
				$this->stmt = $this->db->prepare("DELETE FROM comments WHERE imageName=?");
				$this->stmt->execute(array($this->displayedImage));
				
				
			}
			
			public function GetLoggedInUser(){
				return $_SESSION['login'];
			}
			
			public function DeleteImageFromFolder($displayedImage){
				$this->displayedImage = $displayedImage;
				@unlink('UploadedImages/'.$this->displayedImage);
				
				$this->DeleteImageFromDB($this->displayedImage);
				
			}
			
			public function GetAllImagesFromDB(){
				
				$connection = mysqli_connect("127.0.0.1", "root", "", "loginlabb4");
    			if (mysqli_connect_errno($connection)){
        			echo "MySql Error: " . mysqli_connect_error();
    			}
				$result = mysqli_query($connection,"SELECT imageName FROM images");
				
				$imageArray = array();
				while ($row = mysqli_fetch_assoc($result)) {
       				array_push($imageArray, $row['imageName']);
   				}
				
    			$row = mysqli_fetch_array($result);

				var_dump($row);
				

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
