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
		  private $msg = "";
		  private $loggedInUser;
		  
          public function __construct() {
          	  //$this->model = new modelLogin();			
              $this->uploadModel = new uploadModel();
              $this->uploadViewHTML = new uploadViewHTML($this->uploadModel);
              //$this->viewHTML = new viewHTML($this->model);
          }
		  
		  public function doUpload(){
			//$msg = "";
			$this->loggedInUser = $this->uploadModel->GetLoggedInUser();
		  	if(isset($this->loggedInUser) && $this->uploadViewHTML->didUserPressUploadImageButton() == TRUE){
				$filename = $this->uploadModel->GetFilename();
				$filesize = $this->uploadModel->GetFileSize();
				
				$this->msg = $this->uploadModel->ValidateFilesize($filesize);
				
				if($this->msg == ""){
					$this->msg = $this->uploadModel->Validate($filename);
				}
				
				
				if($this->msg == "" && $this->msg != "Filen är för stor!" || $this->msg == "" && $this->msg != "Filnamnet är för långt!"){
					
					$this->msg = $this->uploadModel->SaveImageToFolder();
				}
				
		  	

			
			
			
			}
		  	return $this->uploadViewHTML->echoHTML($this->msg);
			
			
		  }
         
        } 