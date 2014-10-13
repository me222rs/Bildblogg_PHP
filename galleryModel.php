<?php

require_once 'modelLogin.php';
	class galleryModel {
		private $model;
		
	
   	 		public function __construct(){
        		$this->model = new modelLogin();	
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
