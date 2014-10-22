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
			
			
			//Visar alla bilder
		  	if(isset($loggedInUser) && $this->galleryViewHTML->didUserPressGallery() == TRUE && $this->galleryViewHTML->didUserPressImage() == FALSE){
		  		
			
				$this->imagesArray = $this->galleryModel->ShowAllImages();
		  	
				
			
			
			return $this->galleryViewHTML->echoHTMLGallery($this->imagesArray);
			}
			
			//Visar Enskild bild
			
			if($this->galleryViewHTML->didUserPressImage() == TRUE && $this->galleryViewHTML->didUserPressGallery() == TRUE && isset($loggedInUser)){
				$image = $this->galleryViewHTML->getImageQueryString();
				
				
				//Om användaren postar en kommentar
				if($this->galleryViewHTML->didUserPressPostComment() != ""){
				
					$postedComment = $this->galleryViewHTML->didUserPressPostComment();
					$validMessage = $this->galleryViewHTML->ValidateComment();
					if($validMessage == ""){
						
						$this->galleryModel->PostComment($image, $this->galleryViewHTML->didUserPressPostComment(), $loggedInUser);
						header('Location: galleryView.php?gallery&image=' . $image);
					}
					
				}
				
				
				//Ta bort den bild som visas
				$uploader = $this->galleryModel->GetUploader($this->galleryViewHTML->getImageQueryString());
				
				if($this->galleryViewHTML->didUSerPressDelete() && $uploader == $loggedInUser || $this->galleryViewHTML->didUSerPressDelete() && $loggedInUser == "Admin"){
					$this->galleryModel->DeleteImageFromFolder($image);
					header('Location: galleryView.php?gallery');
				}
				
				// //Ta bort kommentaren du tryckte på
				// if($this->galleryViewHTML->didUserPressDeleteComment() == ""){
// 					
					// $commentID = 0;
				// echo">>>>";
				// echo $commentID;
				// $commentArray = array();
				// $commentArray = $this->galleryModel->GetCommentsFromDB($image);
// 				
				// //Detta körs inte om en användare ändrat värde på hiddenfield i html koden
				// if($commentID != "" && $loggedInUser == $commentArray[$commentID]['user'] || $commentID != "" && $loggedInUser == "Admin"){
					// $this->galleryModel->DeleteComment($commentArray[$commentID]['commentID']);
// 					
					// header('Location: galleryView.php?gallery&image=' . $image);
				// }
// 					
// 					
// 					
				// }
				
				
				
				
				
				return $this->galleryViewHTML->echoHTML($this->imagesArray);
			}
			
			
			
			
		  	
			
			
		  
		  }
		  
         
        } 
