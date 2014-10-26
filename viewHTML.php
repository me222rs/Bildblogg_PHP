<?php

setlocale(LC_ALL, "sv_SE", "swedish");
require_once 'modelLogin.php';
//require_once 'uploadView.php';
require_once 'uploadModel.php';

class viewHTML {
	private $usrValue = '';
	private $message = '';
	private $messageArray = array();
	private $userToRegister;
    
    private $model;
	private $view;
	private $uploadView;
	private $uploadModel;
	
	private $UsernameOK = FALSE;
	private $PasswordOK = FALSE;
	private $available = FALSE;
	private $DangerousUsername = FALSE;
	private $requestedUsername;
	private $requestedPassword;
	private $strippedUsername;
	private $uservalue;
	private $success;
	private $errorMessages;
	private $username;
	private $password;


    public	function __construct(modelLogin $model) {
        $this->model = $model;
	}

//Min kod hela funktionen RegisterValidation
public function RegisterValidation(){
	$this->requestedUsername = $_POST['newUsername'];
	$this->requestedPassword = $_POST['newPassword'];
	
	if (strpos($this->requestedUsername,'<') !== false || strpos($this->requestedUsername,'>') !== false){
		$this->DangerousUsername = TRUE;
	}
	
	$this->strippedUsername = filter_var($this->requestedUsername, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	if(isset($_POST['registerButton']) == TRUE){
		
		if(isset($this->requestedUsername) == FALSE || strlen($this->strippedUsername) < 3 && $this->DangerousUsername == FALSE){	
			array_push($this->messageArray, "Användarnamnet måste ha minst 3 tecken");
			$this->userToRegister = $this->strippedUsername;
		}else{
			$this->UsernameOK = TRUE;
		}
		
		if(isset($this->requestedUsername) == FALSE || strlen($this->requestedPassword) < 6 && $this->DangerousUsername == FALSE){
			array_push($this->messageArray, "Lösenordet måste ha minst 6 tecken");
			$this->userToRegister= $this->strippedUsername;
		}else{
				if($_POST['repeatedNewPassword'] != $this->requestedPassword){
					$this->userToRegister = $this->strippedUsername;
					array_push($this->messageArray, "Lösenordet stämmer inte");
				}else{
					$this->PasswordOK = TRUE;
				}
		}
		
		if($this->UsernameOK == TRUE && $this->PasswordOK == TRUE && $this->DangerousUsername == FALSE){
			$this->available = $this->model->CheckIfUsernameIsAvailable($this->requestedUsername);
			if($this->available == TRUE){
				$this->model->Save($this->strippedUsername, $this->requestedPassword);
				header('Location: index.php');
			}
				
			else{
				$this->userToRegister = $this->strippedUsername;
					array_push($this->messageArray, "Användarnamnet är upptaget!");

				
			}
		}else{
			if($this->DangerousUsername == TRUE){
					array_push($this->messageArray, "Användarnamnet innehåller ogiltiga tecken!");
				}
			$this->userToRegister = $this->strippedUsername;
		}
		}

	}



public function echoHTML($msg){

	
	$this->uservalue = $this->model->SuccessUser();
	$this->success = $this->model->SuccessMessage();
	
	if($this->uservalue != "" || $this->uservalue != NULL && $this->success != "" || $this->success !=  NULL){
		$this->usrValue = $this->uservalue;
	}
	
	$failUser = $this->model->GetFailUser();
	
	if($this->usrValue == ""){
		$this->usrValue = $failUser;
		$this->model->UnsetFailUser();
	}
	
	$User = $this->model->GetLoggedInUser();
	$this->errorMessages = implode(" och ", $this->messageArray);
    $ret="";
    		
	//Källa https://github.com/jn222na/Laboration_2_Login
    $dat = nl2br(ucwords(strftime("%Aen den %d %B.\n " . "År" . " %Y.\n Klockan " . "Är" . " %X.")));
	//Min kod börjar här. 
			

			
			
			if($this->didUserPressRegister()){
			return $ret ="
			<form METHOD='post'>
			<header><h1>
				Ej Inloggad, Registrerar användare
			</h1></header>
			<div id='content'>
			<a href='index.php'>Tillbaka</a>
			<br>
			<p>$this->errorMessages</p>
			<label for='newUsername'>Användarnamn: </label>
			<br>
			<input type='text' name='newUsername' id='newUsername' value='$this->userToRegister'/>
			<br>
			<label for='newPassword'>Lösenord: </label>
			<br>
			<input type='text' name='newPassword' id='newPassword'/>
			<br>
			<label for='repeatedNewPassword'>Repetera lösenord: </label>
			<br>
			<input type='text' name='repeatedNewPassword' id='repeatedNewPassword'/>
			<br>
			<input type='submit' id='registerButton' name='registerButton' value='Registrera'/>
			</form>
			
			" . $dat . "
		
			<footer>Mickes Fotosida</footer>
				</div>
        ";
	//Min kod slutar här
			}
    //Om inloggningen lyckades
    if($this->model->loginStatus()){
    	
        $ret ="
        <div id='content'>
			<header><h1>
				Mickes Fotosida
			</h1></header>
			<h2>
				$User Inloggad!
			</h2>
			$msg
			$this->message
			
			<form action='uploadIndex.php?upload' method='post'>
				<input type='submit'  name='goToUpload' value='Upload image'/>
				<br>
			</form>
			
			<form action='galleryIndex.php?gallery' method='post'>
				<input type='submit'  name='goToUpload' value='Galleri'/>
				<br>
			</form>
			
			
			<form method='post'>
		    	<input type='submit'  name='logOut' value='Logga ut'/>
			</form>
			" . $dat . "
			<footer>Mickes Fotosida</footer>
			</div>
        ";
    }
    //Om inloggningen misslyckades
    else{
        $ret = "
        <div id='content'>
        <header><h1>
			Mickes Fotosida
		</h1></header>
		<h2>
				Ej inloggad $this->success
		</h2>
		<a href='index.php?register'>Register</a>
	<h3>$msg</h3>
    <h3>$this->message</h3> 
       <form id='login'   method='post'>
       		
    		<label for='username'>Username:</label>
    			<br>
    		<input type='text'  name='username' value='$this->usrValue' id='username'>
    			<br>
    		<label for='password'>Password:</label>
    			<br>
    		<input type='password'   name='password' id='password'>
    			<br>
    		<input type='checkbox' name='checkSave' value='remember'>Remember me
    			<br>
    		<input type='submit'  name='submit'  value='Submit'/>
	    </form>  
		 <div>
		 <p>$dat <br> </p>
		 
		</div>
		<footer>Mickes Fotosida</footer>
		 </div>";
        
    }
    return $ret;
}



//Om användaren klickar login och det är korrekt
//Källa https://github.com/jn222na/Laboration_2_Login
//Jag har ändrat på ett par ställen i denna funktionen för att lösenordet inte ska vara hårdkodat 
public function didUserPressLogin(){

	
	    $this->username = $_POST['username'];
	    $this->password = md5($_POST['password']);
	    
	    if (isset($_POST['submit'])) {
	    
	    		
		if($this->password == "d41d8cd98f00b204e9800998ecf8427e" || $this->password == NULL){
		    $this->message = "Password is empty.";
			
		    $this->usrValue = $this->username;
		    
		    
		}
		
	        	
	        
		if($this->username == "" || $this->username == NULL){
		    $this->usrValue = $this->username;
		    $this->message = "Username is missing.";
		}


		return TRUE;
		}
		
		return FALSE;
	    
	}
	//Get funktioner
	//Källa https://github.com/jn222na/Laboration_2_Login
	public function getUsername(){
	    if(isset($_POST['username'])){
	        return  $_POST['username'];
	    }
	}
	//Källa https://github.com/jn222na/Laboration_2_Login
	public function getPassword(){
	    if(isset($_POST['password'])){
	        return  md5($_POST['password']);
	    }
	}
	//Källa https://github.com/jn222na/Laboration_2_Login
	public function getCookieUsername(){
		return $_COOKIE['cookieUsername'];
	}
	//Källa https://github.com/jn222na/Laboration_2_Login
	public function getCookiePassword(){
		return $_COOKIE['cookiePassword'];
	}
	//Källa https://github.com/jn222na/Laboration_2_Login
	public function checkedRememberBox(){
	    if(isset($_POST['checkSave'])){
	        return TRUE;
	    }
	    else{
	        return FALSE;
	    }
	}
	//Sätter kakor och krypterar lösenord
	//Källa https://github.com/jn222na/Laboration_2_Login
	public function rememberUser(){
	              setcookie('cookieUsername', $_POST['username'], time()+60*60*24*30);
				  setcookie('cookiePassword', md5($_POST['password']), time()+60*60*24*30); //Fixa så att lösenordet krypteras innan skickas till db.
				  
        		$cookieTime = time()+60*60*24*30;
        		file_put_contents('cookieTime.txt', $cookieTime);
				 $this->message ="Login successfull and you will be remembered.";
	}
	//Kollar om kakorna är satta
	//Källa https://github.com/jn222na/Laboration_2_Login
	public function checkCookie(){
	    if(isset($_COOKIE['cookieUsername']) && isset($_COOKIE['cookiePassword'])){
			return true;
		}
		else{
			return false;
		}
	}
	//Källa https://github.com/jn222na/Laboration_2_Login
	public function removeCookies() {
	    setcookie ('cookieUsername', "", time() - 3600);
		setcookie ('cookiePassword', "", time() - 3600);
	}
	//Källa https://github.com/jn222na/Laboration_2_Login
	public function didUserPressLogout(){
	    if(isset($_POST['logOut'])){
	        return TRUE;
	    }else{
	        return FALSE;
	    }
	}
	//Min kod nedan
	public function didUserPressRegister(){
		if(isset($_GET['register'])){
			return TRUE;
		}
		return FALSE;
	}
	
		public function didUserPressUpload(){
		if(isset($_GET['upload'])){
			return TRUE;
		}
		return FALSE;
	}
		
		public function didUserPressGallery(){
		if(isset($_GET['gallery'])){
			return TRUE;
		}
		return FALSE;
	}	

}
