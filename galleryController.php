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
		  
          public function __construct() {
          	  $this->model = new modelLogin();			
              $this->galleryModel = new galleryModel();
              $this->galleryViewHTML = new galleryViewHTML($this->uploadModel);
              $this->viewHTML = new viewHTML($this->model);
          }
		  
		  public function doShowGallery(){
		  	$msg = "";
			$images = "";
		  	if(isset($_SESSION['login'])){
		  		
			
				$imagesArray = $this->galleryModel->ShowAllImages();
		  	
				
			
			
			return $this->galleryViewHTML->echoHTML($imagesArray);
			}
		  	
			
			
		  
		  }
		  
         
        } 
