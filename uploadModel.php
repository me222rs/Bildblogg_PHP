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
		private $maxWidth = 200; //Ändra bilden storlek här
		private $maxHeight = 200; //Ändra bilden storlek här
		private $newWidth = 0;
		private $newHeight = 0;
		private $ratio;
		private $fileWithoutExtention;
		private $uploadfile;
		private $image;
		private $scaled;
	
		//TODO Fixa en connection string och PDO
	
   	 	public function __construct(){
        $this->model = new modelLogin();	
    	}
		
		public function GetLoggedInUser(){
			return $_SESSION['login'];
		}
		
		
		public function ValidateFilesize($filesize){
			if($filesize > 26214400){ //25mb
				return "Filen är för stor!";
			}
			return "";
		}
		
		public function Validate($filename){
			
			if(strlen($filename) > 40){
				return "Filnamnet är för långt!";
			}else{
				return "";
			}

			
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
	    		return TRUE;
    		}else{
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
		
		//returnerar filens storlek i bytes
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
				
				
				//data är en array som innehåller info om bilden. På plats 0 och 1 ligger bredd och höjd
				$this->data = getimagesize($_FILES['filename']['tmp_name']);
				
				$this->imageWidth = $this->data[0];	
				$this->imageHeight = $this->data[1]; 
				$this->ratio = $this->imageWidth / $this->imageHeight;


			
				$i = 1;
				while(file_exists("./UploadedImages/" . $this->filename)){
					$this->fileWithoutExtention = basename($_FILES['filename']['name'],".jpg");
					$this->filename = $this->fileWithoutExtention . $i . ".jpg";
					
					
					$i++;
					
				}
				//Räknar ut den nya storleken på bilden. Aspect ratio behålls.
				if($this->imageWidth > $this->maxWidth){	
					$this->imageWidth = $this->maxWidth;	
					$this->imageHeight = $this->maxWidth / $this->ratio; 
				}

				
				if($this->imageHeight > $this->maxHeight){
					$this->imageHeight = $this->maxHeight;
					$this->imageWidth = $this->maxHeight * $this->ratio;
				}
				
				
				
				$this->uploadfile = $this->folder . basename($this->filename);	
						
				$this->image = imagecreatefromjpeg($_FILES['filename']['tmp_name']);
				$this->scaled = imagescale($this->image, $this->imageWidth, $this->imageHeight,  IMG_BICUBIC_FIXED);
				imagejpeg($this->scaled, $this->uploadfile);
				
				//Add image to database
				$this->AddImageNameToDatabase($this->filename);
				
				
				imagedestroy($this->image);
				imagedestroy($this->scaled);

				if (file_exists($this->uploadfile)) {
					$_SESSION['UploadSuccess'] = TRUE;
					return "Upload success!";
				}
				
				

				}
			}
		}
	
        
    }