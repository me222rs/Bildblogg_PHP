<?php

require_once 'uploadHTML.php';
require_once 'uploadModel.php';

        class  uploadController{
          private $uploadHTML;
          private $uploadModel;
		  
          public function __construct() {
              $this->uploadModel = new uploadModel();
              $this->uploadView = new uploadHTML($this->uploadModel);
              
          }
		  public function doUpload(){
		  	
			
			
		  	//return $this->uploadHTML->echoBody($msg);
		  }
         
        } 