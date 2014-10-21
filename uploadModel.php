<?php

require_once 'modelLogin.php';
    class uploadModel{
    	private $folder = "./UploadedImages/";
		private $model;
		private $allowed = array();
		private	$filename = "";
		private	$ext = "";
		private $data = array();
		private $imageWidth;
		private	$imageHeight;
		private $maxWidth = 200;
		private $maxHeight = 200;
		private $newWidth = 0;
		private $newHeight = 0;
		private $ratio;
		private $fileWithoutExtention;
		private $uploadfile;
	
   	 	public function __construct(){
        $this->model = new modelLogin();	
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
		
		public function GetFileName(){
			return $_FILES['filename']['name'];
		}
		
		public function GetFileSize(){
			return filesize($_FILES['filename']['tmp_name']);
		}
		
		public function SaveImageToFolder(){
			if($this->model->loginStatus()){
				
				$this->allowed =  array('gif','png' ,'jpg');
				$this->filename = $_FILES['filename']['name'];
				$this->ext = pathinfo($this->filename, PATHINFO_EXTENSION);
				
				
				if(!in_array($this->ext,$this->allowed) && count($_FILES) > 0) {
    				return "Upload fail";
				

			}elseif(!empty($this->filename)){
				
				//******************Ej strängberoende hit*******************
				
				$this->data = getimagesize($_FILES['filename']['tmp_name']);
				
				$this->imageWidth = $this->data[0];	//1920
				$this->imageHeight = $this->data[1]; //1080
				//$maxWidth = 200;
				//$maxHeight = 200;
				//$newWidth = 0;
				//$newHeight = 0;
				$this->ratio = $this->imageWidth / $this->imageHeight;


				//$shittyFileName = $_FILES['filename']['name'];
				//var_dump("./UploadedImages/" . $this->filename);
				//$imageExists = $this->CheckIfImageExistsInDataBase($_FILES['filename']['name']);
				$i = 1;
				while(file_exists("./UploadedImages/" . $this->filename)){
					$this->fileWithoutExtention = basename($_FILES['filename']['name'],".jpg");
					$this->filename = $this->fileWithoutExtention . $i . ".jpg";
					
					
					$i++;
					var_dump($this->filename);
				}
				
				if($this->imageWidth > $this->maxWidth){	
					$this->imageWidth = $this->maxWidth;	
					$this->imageHeight = $this->maxWidth / $this->ratio; 
				}

				
				if($this->imageHeight > $this->maxHeight){
					$this->imageHeight = $this->maxHeight;
					$this->imageWidth = $this->maxHeight * $this->ratio;
				}
				
				
				
				$this->uploadfile = $this->folder . basename($this->filename);	
						
				$image = imagecreatefromjpeg($_FILES['filename']['tmp_name']);
				$scaled = imagescale($image, $this->imageWidth, $this->imageHeight,  IMG_BICUBIC_FIXED);
				imagejpeg($scaled, $this->uploadfile);
				
				//Add image to database
				$this->AddImageNameToDatabase($this->filename);
				
				
				imagedestroy($image);
				imagedestroy($scaled);

				if (file_exists($this->uploadfile)) {
					$_SESSION['UploadSuccess'] = TRUE;
					return "Upload success!";
				}
				
						// if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile)) {

				}
			}
		}
	
        
    }