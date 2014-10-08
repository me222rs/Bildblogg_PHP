<?php

setlocale(LC_ALL, "sv_SE", "swedish");
require_once 'modelLogin.php';
require_once 'uploadView.php';
require_once 'uploadModel.php';

class viewHTML {
	private $username = '';
	private $usrValue = '';
	private $password = '';
	private $msg = '';
	private $messageArray = array();
	private $user;
    
    private $model;
	private $view;
	private $uploadView;
	private $uploadModel;
	


//TODO: FIXA
    public	function __construct(modelLogin $model) {
        $this->model = $model;
		//$this->uploadView = new uploadView();
		//$this->uploadModel = new uploadModel();
	}

//Min kod
public function RegisterValidation(){
	$UsernameOK = FALSE;
	$PasswordOK = FALSE;
	$available = FALSE;
	$DangerousUsername = FALSE;
	
	if (strpos($_POST['newUsername'],'<') !== false || strpos($_POST['newUsername'],'>') !== false){
		echo "HORA";
		$DangerousUsername = TRUE;
		echo $DangerousUsername;
	}
	
	$strippedUsername = filter_var($_POST['newUsername'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	echo $strippedUsername;
	if(isset($_POST['registerButton']) == TRUE){
		
		if(isset($_POST['newUsername']) == FALSE || strlen($strippedUsername) < 3 && $DangerousUsername == FALSE){	
			array_push($this->messageArray, "Användarnamnet måste ha minst 3 tecken");
			$this->user = $strippedUsername;
		}else{
			echo "Användarnamn stämmer";
			$UsernameOK = TRUE;
		}
		
		if(isset($_POST['newPassword']) == FALSE || strlen($_POST['newPassword']) < 6 && $DangerousUsername == FALSE){
			echo isset($_POST['newPassword']);
			echo $_POST['newPassword'];
			array_push($this->messageArray, "Lösenordet måste ha minst 6 tecken");
			$this->user = $strippedUsername;
		}else{
				if($_POST['repeatedNewPassword'] != $_POST['newPassword']){
					echo "Lösenorden är olika!";
					$this->user = $strippedUsername;
					array_push($this->messageArray, "Lösenordet stämmer inte");
				}else{
					echo "Lösenord stämmer";
					$PasswordOK = TRUE;
				}
		}
		
		if($UsernameOK == TRUE && $PasswordOK == TRUE && $DangerousUsername == FALSE){
			echo "FITTA";
			$available = $this->model->CheckIfUsernameIsAvailable($_POST['newUsername']);
			//echo $available;
			if($available == TRUE){
				$this->model->Save($strippedUsername, $_POST['newPassword']);
				header('Location: index.php');
			}
				
			else{
				$this->user = $strippedUsername;
				//echo $DangerousUsername;
					array_push($this->messageArray, "Användarnamnet är upptaget!");

				
			}
		}else{
			if($DangerousUsername == TRUE){
					array_push($this->messageArray, "Användarnamnet innehåller ogiltiga tecken!");
				}
			$this->user = $strippedUsername;
		}
		}

	}


public function GetUser(){
	
}

public function echoHTML($msg){

	
	$uservalue = $this->model->SuccessUser();
	$success = $this->model->SuccessMessage();
	
	if($uservalue != "" || $uservalue != NULL && $success != "" || $success !=  NULL){
		$this->usrValue = $uservalue;
	}
	
	$failUser = $this->model->GetFailUser();
	
	if($this->usrValue == ""){
		$this->usrValue = $failUser;
		$this->model->UnsetFailUser();
	}
	
	$User = $_SESSION['login'];
	$errorMessages = implode(" och ", $this->messageArray);
    $ret="";
    		//Clock function
		/*
		 * nl2br allows \n within variable
		 * strftime let us print date from our locale time
		 */
    $dat = nl2br(ucwords(strftime("%Aen den %d %B.\n " . "År" . " %Y.\n Klockan " . "Är" . " %X.")));
	//Min kod
			
			if($this->didUserPressUpload()){
				
			}
			
			
			if($this->didUserPressRegister()){
			return $ret ="
			<form METHOD='post'>
			<h1>
				Ej Inloggad, Registrerar användare
			</h1>
			<a href='index.php'>Tillbaka</a>
			<br>
			<p>$errorMessages</p>
			<label for='newUsername'>Användarnamn: </label>
			<br>
			<input type='text' name='newUsername' id='newUsername' value='$this->user'/>
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
        ";
	
			}
    //Om inloggningen lyckades
    if($this->model->loginStatus()){
    	
        $ret ="
			<h1>
				Laboration_2
			</h1>
			<h2>
				$User Inloggad!
			</h2>
			$msg
			$this->msg
			
			<form action='uploadHTML.php?upload' method='post'>
				<input type='submit'  name='goToUpload' value='Upload image'/>
				<br>
			</form>
			
			<form action='GalleryHTML.php?gallery' method='post'>
				<input type='submit'  name='goToUpload' value='Galleri'/>
				<br>
			</form>
			
			
			<form method='post'>
		    	<input type='submit'  name='logOut' value='Logga ut'/>
			</form>
			" . $dat . "
        ";
    }
    //Om inloggningen misslyckades
    else{
        $ret = "
        <h1>
			Laboration_2
		</h1>
		<h2>
				Ej inloggad $success
		</h2>
		<a href='index.php?register'>Register</a>
	<h3>$msg</h3>
    <h3>$this->msg</h3> 
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
		 
		
		 </div>";
        
    }
    return $ret;
}



//Om användaren klickar login och det är korrekt 
public function didUserPressLogin(){
	    $username = $_POST['username'];
	    $password = md5($_POST['password']);
		echo "test";
		var_dump($_POST['username']);
	    
	    if (isset($_POST['submit'])) {
	    
	    		
		if($password == "d41d8cd98f00b204e9800998ecf8427e" || $password == NULL){
			echo "Lösenordet är tomt";
		    $this->msg = "Password is empty.";
			//echo "helvete";
			//var_dump("1. usrValue = " . $this->usrValue);
			//var_dump("usrValue = " . $username);
		    $this->usrValue = $username;
		    //var_dump("2. usrValue = " . $this->usrValue);
		    
		}
		
	        	
	        
		if($username == "" || $username == NULL){
			echo "Användarnamnet är tomt";
		    $this->usrValue = $username;
		    $this->msg = "Username is missing.";
		}


		
		// if($username != "" && $password != ""){
			// echo "Båda fälten är tomma";
		    // $this->usrValue = $username;
// 		    
		// }
		return TRUE;
		}
		
		return FALSE;
	    
	}
	//Get funktioner
	public function getUsername(){
	    if(isset($_POST['username'])){
	        return  $_POST['username'];
	    }
	}
	public function getPassword(){
	    if(isset($_POST['password'])){
	        return  md5($_POST['password']);
	    }
	}
	
	public function getCookieUsername(){
		return $_COOKIE['cookieUsername'];
	}
	
	public function getCookiePassword(){
		return $_COOKIE['cookiePassword'];
	}
	public function checkedRememberBox(){
	    if(isset($_POST['checkSave'])){
	        return TRUE;
	    }
	    else{
	        return FALSE;
	    }
	}
	//Sätter kakor och krypterar lösenord
	public function rememberUser(){
	              setcookie('cookieUsername', $_POST['username'], time()+60*60*24*30);
				  setcookie('cookiePassword', md5($_POST['password']), time()+60*60*24*30); //Fixa så att lösenordet krypteras innan skickas till db.
				  
        		$cookieTime = time()+60*60*24*30;
        		file_put_contents('cookieTime.txt', $cookieTime);
				 $this->message ="Login successfull and you will be remembered.";
	}
	//Kollar om kakorna är satta
	public function checkCookie(){
	    if(isset($_COOKIE['cookieUsername']) && isset($_COOKIE['cookiePassword'])){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function removeCookies() {
	    setcookie ('cookieUsername', "", time() - 3600);
		setcookie ('cookiePassword', "", time() - 3600);
	}
	
	public function didUserPressLogout(){
	    if(isset($_POST['logOut'])){
	        return TRUE;
	    }else{
	        return FALSE;
	    }
	}
	//Min kod
	public function didUserPressRegister(){
		if(isset($_GET['register'])){
			echo "Tryckt på registrera!";
			return TRUE;
		}
		return FALSE;
	}
	
		public function didUserPressUpload(){
		if(isset($_GET['upload'])){
			echo "Tryckt på ladda upp!";
			return TRUE;
		}
		return FALSE;
	}
		
		public function didUserPressGallery(){
		if(isset($_GET['gallery'])){
			echo "Tryckt på galleri!";
			return TRUE;
		}
		return FALSE;
	}	

}
