<?php

require_once 'modelLogin.php';
	class galleryModel {
		private $model;
		
	
   	 		public function __construct(){
        		$this->model = new modelLogin();	
    		}
			
			public function GetUploader($loggedInUser){
				/*
				$connection = mysqli_connect("127.0.0.1", "root", "", "loginlabb4");
    			if (mysqli_connect_errno($connection)){
        			echo "MySql Error: " . mysqli_connect_error();
    			}
				
				$result = mysqli_query($connection,"SELECT upLoaderID FROM images WHERE upLoaderID = $loggedInUser");
				
				echo $loggedInUser;
				var_dump($result);
				echo ">>> $result <<<";
				return $result; 
				*/
				
				$returnString = "";
				
				$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				
				$stmt = $db->prepare("SELECT upLoaderID FROM images WHERE imageName=?");
				$stmt->execute(array($loggedInUser));
				
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				var_dump($rows);
				echo $rows[0];
				
				return $rows[0]["upLoaderID"];
				
				
				 
				 
				}
			
			
			public function EditComment(){
				//Redigera kommentar man själv lagt upp
			}
			
			public function DeleteComment(){
				//Ta bort en kommentar man själv lagt upp
			}
			
			public function PostComment($displayedImage){
				//Posta en kommentar till någons bild 
			}
			
			public function GetCommentsFromDB($displayedImage){
				//Hämta alla kommentarer till en bild
			}
			
			public function DeleteImageFromDB($displayedImage){
				$db = new PDO('mysql:host=127.0.0.1;dbname=loginlabb4;charset=utf8', 'root', '');
				
				$stmt = $db->prepare("DELETE FROM images WHERE imageName=?");
				$stmt->execute(array($displayedImage));
				
				//$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
				//echo $result;
				//$count = mysqli_num_rows($result);
				//echo $count;
				
				//$row = array();
				//$row = mysqli_fetch_all($result,MYSQLI_ASSOC);
				$imageArray = array();
				while ($row = mysqli_fetch_assoc($result)) {
       				array_push($imageArray, $row['imageName']);
   				}
				
    			$row = mysqli_fetch_array($result);

				var_dump($row);
				

   				return $imageArray;
				
				//return $row;
			}
			
			public function ShowAllImages(){
				$imagesArray = array();
				$dbArray = array();
				
				
				$dbArray = $this->GetAllImagesFromDB();
				//echo $dbArray[0];
				$dir = "./UploadedImages/";

				//var_dump($dbArray);
				
				
	foreach($dbArray as $file) {
		array_push($imagesArray, $file);
		//echo "filename: $file : filetype: " . filetype($file) . "<br />";
		echo "bajs";
	}
				
				return $imagesArray;
				
				//var_dump($imagesArray);
			}
	}
