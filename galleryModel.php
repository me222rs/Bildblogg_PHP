<?php

require_once 'modelLogin.php';
	class galleryModel {
		private $model;
		private $connectionString = "mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', ''";
		
		
	
   	 		public function __construct(){
        		$this->model = new modelLogin();	
    		}
			
			
			// public function UnsetJavascriptMessage(){
				// $_SESSION['javascriptMessage'] == "";
			// }
// 			
			// public function SetJavascriptMessage($javascriptMessage){
				// $_SESSION['javascriptMessage'] = "<script>alert('$javascriptMessage')</script>";
			// }
// 			
			// public function GetJavascriptMessage(){
				// return $_SESSION['javascriptMessage'] = "<script>alert('$javascriptMessage')</script>";
			// }
			
			
			public function GetUploader($loggedInUser){

				
				$returnString = "";
				
				$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				
				$stmt = $db->prepare("SELECT upLoaderID FROM images WHERE imageName=?");
				$stmt->execute(array($loggedInUser));
				
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				//var_dump($rows);
				//echo $rows[0];
				
				return $rows[0]["upLoaderID"];
				
				
				 
				 
				}
			
			
			public function GetCommentToEdit($commentID){
				//echo $commentID;
				
				$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$stmt = $db->prepare("SELECT comment FROM comments WHERE commentID=:commentID");
				$stmt->execute(array(':commentID' => $commentID));
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $rows[0]['comment'];
			}
			
			public function EditComment($commentID, $comment){
				//Redigera kommentar man själv lagt upp
				if($commentID != ""){
					$_SESSION['commentID'] = $commentID;	
				}
				if($comment != ""){
					echo "Kommer in i EditComment";
				echo $_SESSION['commentID'];
				echo $comment;
				$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$stmt = $db->prepare("UPDATE comments SET comment=? WHERE commentID=?");
				$stmt->execute(array($comment, $_SESSION['commentID']));
				$affected_rows = $stmt->rowCount();
				if($affected_rows != ""){
					return true;
				} 
				else{
					return false;
				}
				}
				
				
			}
			
			public function DeleteComment($commentID){
				//Ta bort en kommentar man själv lagt upp
				
				$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				$stmt = $db->prepare("DELETE FROM comments WHERE commentID=:commentID");
				$stmt->bindValue(':commentID', $commentID, PDO::PARAM_STR);
				$stmt->execute();
			}
			
			public function PostComment($displayedImage, $comment, $user){
				//Posta en kommentar till någons bild 
				
				$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				
				$stmt = $db->prepare("INSERT INTO comments(comment,imageName,user) VALUES(:comment,:imageName,:user)");
				$stmt->execute(array(':comment' => $comment, ':imageName' => $displayedImage, ':user' => $user));
				
			}
			
			public function GetCommentsFromDB($displayedImage){
				//Hämta alla kommentarer till en bild
				$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				
				$rows = array();
				
				$stmt = $db->prepare("SELECT * FROM comments WHERE imageName=?");
				$stmt->execute(array($displayedImage));
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				return $rows;
			}
			
			public function DeleteImageFromDB($displayedImage){
				$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				
				$stmt = $db->prepare("DELETE FROM images WHERE imageName=?");
				$stmt->execute(array($displayedImage));
				
				$stmt = $db->prepare("DELETE FROM comments WHERE imageName=?");
				$stmt->execute(array($displayedImage));
				
				
			}
			
			public function GetLoggedInUser(){
				return $_SESSION['login'];
			}
			
			public function DeleteImageFromFolder($displayedImage){
				
				@unlink('UploadedImages/'.$displayedImage);
				
				$this->DeleteImageFromDB($displayedImage);
				
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
