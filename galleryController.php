<?php

require_once 'galleryViewHTML.php';
require_once 'galleryModel.php';
require_once 'modelLogin.php';
require_once 'viewHTML.php';

        class  galleryController{
          private $galleryViewHTML;
          private $galleryModel;
		  private $viewHTML;
		  private $model;
		  private $msg = "";
		  private $imagesArray = array();
		  
          public function __construct() {
          	  $this->model = new modelLogin();			
              $this->galleryModel = new galleryModel();
              $this->galleryViewHTML = new galleryViewHTML($this->uploadModel);
              $this->viewHTML = new viewHTML($this->model);
          }
		  
		  public function doShowGallery(){
		  	//$msg = "";
		  	$loggedInUser = $this->galleryModel->GetLoggedInUser();
			//$images = "";
		  	if(isset($loggedInUser)){
		  		
			
				$this->imagesArray = $this->galleryModel->ShowAllImages();
		  	
				
			
			
			return $this->galleryViewHTML->echoHTML($this->imagesArray);
			}
		  	
			
			
		  
		  }
		  
         
        } 
