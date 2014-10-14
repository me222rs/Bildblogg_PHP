<?php

require_once 'modelLogin.php';
    class uploadModel{
    	private $folder = "./UploadedImages/";
		private $model;
		
	
   	 	public function __construct(){
        $this->model = new modelLogin();	
    	}
		
		public function Scale($imageWidth, $imageHeight){
			
			$imageFile = imagecreatefromjpeg($_FILES['filename']['tmp_name']);
			
			
			
				$maxWidth = 200;
				$maxHeight = 200;
				$newWidth = 0;
				$newHeight = 0;
			
				if($imageWidth > $maxWidth){
					$imageWidth = $maxWidth;
					$maxHeight = $maxWidth * ($imageWidth/$imageHeight);
				}
				
				if($imageHeight > $maxHeight){
					$imageHeight = $maxHeight;
					$maxWidth = $maxHeight * ($imageWidth/$imageHeight);
				}
				
				$size = array($newWidth, $newHeight);
				return $size;
		 
		 
		}
		
		public function CheckIfImageExistsInDataBase($imageName){
			$connection = mysqli_connect("127.0.0.1", "root", "", "loginlabb4");
    			if (mysqli_connect_errno($connection)){
        			echo "MySql Error: " . mysqli_connect_error();
    			}

    		$query = mysqli_query($connection,"SELECT * FROM images WHERE imageName='$imageName'");
    		$count = mysqli_num_rows($query);
    		$row = mysqli_fetch_array($query);

    		if ($count == 1){
    			echo "Bilden finns redan.";
	    		return TRUE;
    		}else{
    			echo "Bilden finns ej.";
       			return false;
    		}   
		}
		
		public function AddImageNameToDatabase($imageName){
			$upLoaderID = $_SESSION['login'];
			$connection = mysqli_connect("127.0.0.1", "root", "", "loginlabb4");
			
    			if (mysqli_connect_errno($connection)){
        			echo "MySql Error: " . mysqli_connect_error();
    			}
		
    		mysqli_query($connection,"INSERT images SET imageName = '$imageName', upLoaderID = '$upLoaderID'");
		}
		
		
		public function SaveImageToFolder(){
			if($this->model->loginStatus()){
				
				$allowed =  array('gif','png' ,'jpg');
				$filename = $_FILES['filename']['name'];
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				
				
				if(!in_array($ext,$allowed) && count($_FILES) > 0) {
    				return "Upload fail";
				

			}elseif(!empty($filename)){
				
				
				
				$data = getimagesize($_FILES['filename']['tmp_name']);
				
				$imageWidth = $data[0];	//1920
				$imageHeight = $data[1]; //1080
				$maxWidth = 200;
				$maxHeight = 200;
				$newWidth = 0;
				$newHeight = 0;
				$ratio = $imageWidth / $imageHeight;


				$shittyFileName = $_FILES['filename']['name'];
				var_dump("./UploadedImages/" . $shittyFileName);
				//$imageExists = $this->CheckIfImageExistsInDataBase($_FILES['filename']['name']);
				$i = 1;
				while(file_exists("./UploadedImages/" . $shittyFileName)){
					$fileWithoutExtention = basename($_FILES['filename']['name'],".jpg");
					$shittyFileName = $fileWithoutExtention . $i . ".jpg";
					
					
					$i++;
					var_dump($shittyFileName);
				}
				
				if($imageWidth > $maxWidth){	
					$imageWidth = $maxWidth;	
					$imageHeight = $maxWidth / $ratio; 
				}

				
				if($imageHeight > $maxHeight){
					$imageHeight = $maxHeight;
					$imageWidth = $maxHeight * $ratio;
				}
				
				
				
				$uploadfile = $this->folder . basename($shittyFileName);	
						
				$image = imagecreatefromjpeg($_FILES['filename']['tmp_name']);
				$scaled = imagescale($image, $imageWidth, $imageHeight,  IMG_BICUBIC_FIXED);
				imagejpeg($scaled, $uploadfile);
				
				//Add image to database
				$this->AddImageNameToDatabase($shittyFileName);
				
				
				imagedestroy($image);
				imagedestroy($scaled);

				if (file_exists($uploadfile)) {
					$_SESSION['UploadSuccess'] = TRUE;
					return "Upload success!";
				}
				
						// if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile)) {

				}
			}
		}
	
        
    }