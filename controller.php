<?php

require_once 'viewHTML.php';
require_once 'modelLogin.php';

        class  controller{
          private $view;
          private $model;
		  private $mess = "";
		  private $username;
		  private $password;
		  
          public function __construct() {
              $this->model = new modelLogin();
              $this->view = new viewHTML($this->model);
              //$this->registerView = new RegisterView();
          }
          
          public function login(){
              $this->username = $this->view->getUsername();
              $this->password = $this->view->getPassword();
              //$this->msg = "";
              
    //Om sessionen inte är satt 
            if($this->model->loginStatus() == FALSE){
    // kolla om cookies är satta
		   	if($this->view->checkCookie()){
	  //Är dom det skicka kaknamn och kaklösen vidare 
				if($this->model->checkLoginCookie($this->view->getCookieUsername(), $this->view->getCookiePassword())){
					$this->mess = "Login with cookies successfull";
				}else{
    //annars ta bort
					$this->view->removeCookies();
					$this->mess = "Cookie contains wrong information";
				}
			}
		}
			
			 if($this->view->didUserPressRegister()){
    			 
				 $this->mess = "Du har tryckt på registrera!";
				 $this->view->RegisterValidation();
    		 }  
    //Om användaren vill logga in
        
			 elseif($this->view->didUserPressLogin()){
                if($this->username != "" && $this->password != "d41d8cd98f00b204e9800998ecf8427e"){
    //Om han kryssat i "remember me"                
                  if($this->model->checkLogin($this->username, $this->password)){
                      $this->mess = "Successful login";
                      if($this->view->checkedRememberBox()){
                          $this->view->rememberUser();
                          $this->mess = "Login successful you will be remembered";
                      }
                      
                  }
					else{
						$this->model->SetFailUser($this->username);
                      	$this->mess ="Trouble logging in (Username/Password)";
                  }
               }
              }
    //Om användaren klickar logout
              if($this->view->didUserPressLogout()){
                  $this->view->removeCookies();
                  $this->model->destroySession();
                  $this->mess =  "User logged out";
              }
    //Skickar med meddelandet till echoHTML
              return $this->view->echoHTML($this->mess);
          }
        } 