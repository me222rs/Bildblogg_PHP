<?php

require_once 'viewHTML.php';
require_once 'modelLogin.php';

        class  controller{
          private $view;
          private $model;
		  private $registerView;
          public function __construct() {
              $this->model = new modelLogin();
              $this->view = new viewHTML($this->model);
              //$this->registerView = new RegisterView();
          }
          
          public function login(){
              $username = $this->view->getUsername();
              $password = $this->view->getPassword();
              $msg = "";
              
    //Om sessionen inte är satt 
            if($this->model->loginStatus() == FALSE){
    // kolla om cookies är satta
		   	if($this->view->checkCookie()){
	  //Är dom det skicka kaknamn och kaklösen vidare 
				if($this->model->checkLoginCookie($this->view->getCookieUsername(), $this->view->getCookiePassword())){
					$msg = "Login with cookies successfull";
				}else{
    //annars ta bort
					$this->view->removeCookies();
					$msg = "Cookie contains wrong information";
				}
			}
		}
			
			 if($this->view->didUserPressRegister()){
    			 
				 $msg = "Du har tryckt på registrera!";
				 $this->view->RegisterValidation();
    		 }  
    //Om användaren vill logga in
        
			 elseif($this->view->didUserPressLogin()){
                if($username != "" && $password != "d41d8cd98f00b204e9800998ecf8427e"){
    //Om han kryssat i "remember me"                
                  if($this->model->checkLogin($username, $password)){
                      $msg = "Successful login";
                      if($this->view->checkedRememberBox()){
                          $this->view->rememberUser();
                          $msg = "Login successful you will be remembered";
                      }
                      
                  }
					else{
						$this->model->SetFailUser($username);
                      	$msg ="Trouble logging in (Username/Password)";
                  }
               }
              }
    //Om användaren klickar logout
              if($this->view->didUserPressLogout()){
                  $this->view->removeCookies();
                  $this->model->destroySession();
                  $msg =  "User logged out";
              }
    //Skickar med meddelandet till echoHTML
              return $this->view->echoHTML($msg);
          }
        } 