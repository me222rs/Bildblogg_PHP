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
		  		
			
				$msg = $this->uploadModel->SaveImageToFolder();
		  	

			
			
			
			}
		  	return $this->uploadViewHTML->echoHTML($msg);
			
			
		  }
         
        } 