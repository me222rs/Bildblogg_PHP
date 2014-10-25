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
		  private $loggedInUser;
		  private $image;
		  private $commentArray = array();
		  private $postedComment;
		  
          public function __construct() {
          	  $this->model = new modelLogin();			
              $this->galleryModel = new galleryModel();
              $this->galleryViewHTML = new galleryViewHTML($this->uploadModel);
              $this->viewHTML = new viewHTML($this->model);
          }
		  
		  public function doShowGallery(){
		  	
		  	$this->loggedInUser = $this->galleryModel->GetLoggedInUser();

			//Visar alla bilder
		  	if(isset($this->loggedInUser) && $this->galleryViewHTML->didUserPressGallery() == TRUE && $this->galleryViewHTML->didUserPressImage() == FALSE){
		  		
			
				$this->imagesArray = $this->galleryModel->ShowAllImages();
		  	
				
			
			
			return $this->galleryViewHTML->echoHTMLGallery($this->imagesArray);
			
			}
			
			//Visar Enskild bild
			
			if($this->galleryViewHTML->didUserPressImage() == TRUE && $this->galleryViewHTML->didUserPressGallery() == TRUE && isset($this->loggedInUser)){
				$this->image = $this->galleryViewHTML->getImageQueryString();
				
				
				//Om användaren postar en kommentar
				if($this->galleryViewHTML->didUserPressPostComment() != ""){
				
					$this->postedComment = $this->galleryViewHTML->didUserPressPostComment();
					$validMessage = "";
					if($validMessage == ""){
						
						$this->galleryModel->PostComment($this->image, $this->galleryViewHTML->didUserPressPostComment(), $this->loggedInUser);
						header('Location: galleryIndex.php?gallery&image=' . $this->image);
					}
					
				}
				
				
				//Ta bort den bild som visas
				$uploader = $this->galleryModel->GetUploader($this->galleryViewHTML->getImageQueryString());
				
				if($this->galleryViewHTML->didUSerPressDelete() && $uploader == $this->loggedInUser || $this->galleryViewHTML->didUSerPressDelete() && $this->loggedInUser == "Admin"){
					$this->galleryModel->DeleteImageFromFolder($this->image);
					header('Location: galleryIndex.php?gallery');
				}
				
				
				
				//Ta bort kommentaren du tryckte på
				$commentDeleteID = $this->galleryModel->GetCommentToDeleteSession();
				if(isset($commentDeleteID)){
					$this->commentArray = $this->galleryModel->GetCommentsFromArray();

							
				//Detta körs inte om en användare ändrat värde på hiddenfield i html koden
				if($commentDeleteID != "" && $this->loggedInUser == $this->commentArray[$commentDeleteID]['user'] || $commentDeleteID != "" && $this->loggedInUser == "Admin"){
						
					$this->galleryModel->DeleteComment($this->commentArray[$commentDeleteID]['commentID']);
					//$_SESSION['commentDeleteID'] = NULL;
					//$this->galleryModel->UnsetCommentsArray();
					$this->galleryModel->UnsetCommentDeleteSession();
					
					
				}
					
					
					
				}
				
				
				//Om en användare tryckt på redigera så kommer det läggas till en textbox på sidan som är ifylld med kommentarens värde.
				if($this->galleryViewHTML->didUserPressPostEditedComment() == TRUE|| $this->loggedInUser == "Admin" && $this->galleryViewHTML->didUserPressPostEditedComment() == TRUE){
					
					$this->commentArray = $this->galleryModel->GetCommentsFromArray();
					$this->editCommentID = $this->galleryModel->GetCommentToEditSession();
					
					$this->postedComment = $this->galleryViewHTML->didUserPressPostEditedComment();
					$commentValue = $this->galleryViewHTML->GetEditValueFromTextbox();
					$validMessage = $this->galleryViewHTML->ValidateComment();
					
					if($validMessage == ""){
						$success = $this->galleryModel->EditComment($this->commentArray[$this->editCommentID], $commentValue);
						if($success == TRUE){
							header('Location: galleryIndex.php?gallery&image=' . $this->image);
						}
					}
					
						
				}
				
				
				
				
				
				return $this->galleryViewHTML->echoHTML($this->imagesArray);
			}
			if(!isset($this->loggedInUser)){
				header('Location: index.php');
			}
			
			
			
			
		  	
			
			
		  
		  }
		  
         
        } 
