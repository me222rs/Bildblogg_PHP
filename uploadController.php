<?php

require_once 'uploadViewHTML.php';
require_once 'uploadModel.php';
require_once 'modelLogin.php';
require_once 'viewHTML.php';

        class  uploadController{
          private $uploadViewHTML;
          private $uploadModel;
		  private $viewHTML;
		  private $model;
		  
          public function __construct() {
          	  $this->model = new modelLogin();			
              $this->uploadModel = new uploadModel();
              $this->uploadViewHTML = new uploadViewHTML($this->uploadModel);
              $this->viewHTML = new viewHTML($this->model);
          }
		  
		  public function doUpload(){
			$msg = "";
		  	if(isset($_SESSION['login'])){
		  		
				$filename = $this->uploadModel->GetFilename();
				$filesize = $this->uploadModel->GetFileSize();
				echo $filesize;
				$msg = $this->uploadViewHTML->ValidateFilesize($filesize);
				
				if($msg == ""){
					$msg = $this->uploadViewHTML->Validate($filename);
				}
				//echo ">>>" . $msg . "<<<";
				
				if($msg == "" && $msg != "Filen är för stor!" || $msg == "" && $msg != "Filnamnet är för långt!"){
					echo "Bilden laddades upp";
					$msg = $this->uploadModel->SaveImageToFolder();
				}
				
		  	

			
			
			
			}
		  	return $this->uploadViewHTML->echoHTML($msg);
			
			
		  }
         
        } 