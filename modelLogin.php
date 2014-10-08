<?php


    class modelLogin{
    
	
    //private $username = "Admin";
    //private $password = "dc647eb65e6711e155375218212b3964";
    
    public function __construct(){
        
    }
	
	public function CheckIfUsernameIsAvailable($username){
		$connection = mysqli_connect("127.0.0.1", "root", "", "loginlabb4");
    	if (mysqli_connect_errno($connection)){
        	echo "MySql Error: " . mysqli_connect_error();
    	}
		$query = mysqli_query($connection,"SELECT * FROM member WHERE username='$username'");
		$count = mysqli_num_rows($query);
    	$row = mysqli_fetch_array($query);
		
		if($count == 1){
			//echo $count;
			return FALSE;
		}
		return TRUE;
	}
	
	public function SuccessUser(){
		if(isset($_SESSION['successUser'])){
			$successUser = $_SESSION['successUser'];
			return $successUser;
		}
		return false;
	}
	
	public function SuccessMessage(){
		if(isset($_SESSION['successMessage'])){
			$successMessage = $_SESSION['successMessage'];
			return $successMessage;
		}
		return false;
	}
	
	public function UnsetFailUser(){
		$_SESSION['FailUser'] = NULL;
	}
	
	public function SetFailUser($username){
		$_SESSION['FailUser'] = $username;
	}
	
	public function GetFailUser(){
		if(isset($_SESSION['FailUser'])){
			return $_SESSION['FailUser'];
		}
		return NULL;
		
	}
	
	public function Save($newUsername, $newPassword){
		$newPassword = md5($newPassword);
		//$this->username = $newUsername;
		//$this->password = $newPassword;
		$connection = mysqli_connect("127.0.0.1", "root", "", "loginlabb4");
    	if (mysqli_connect_errno($connection)){
        	echo "MySql Error: " . mysqli_connect_error();
    	}
		
    	mysqli_query($connection,"INSERT member SET Username = '$newUsername', Password = '$newPassword'");
	
		mysqli_close($connection);
		$_SESSION['successUser'] = $newUsername;
		$_SESSION['successMessage'] = "Registrereingen lyckades!";
	}
	
    //Lyckad inloggning sätt sessionen till webbläsaren användaren loggade in i
    public function checkLogin($username, $password) {
    	//Min kod
    	//$this->username = $username;
		//$this->password = md5($password);		
		//Kontrollerar lösen och användarnamn mot databas
		
		//$encryptedPassword = md5($password);
			
		$connection = mysqli_connect("127.0.0.1", "root", "", "loginlabb4");
    	if (mysqli_connect_errno($connection)){
        	echo "MySql Error: " . mysqli_connect_error();
    	}

    	$query = mysqli_query($connection,"SELECT * FROM member WHERE username='$username' && password='$password'");
    	$count = mysqli_num_rows($query);
    	$row = mysqli_fetch_array($query);

    	if ($count == 1){
    		
        	$_SESSION['login'] = $username;
	    	$_SESSION["checkBrowser"] = $_SERVER['HTTP_USER_AGENT'];
			//mysqli_close($connection);
	    	return TRUE;
    	}else{
    		//mysqli_close($connection);
       		return false;
    	}   
	mysqli_close($connection);
    
	//*****************************************			
	       // if($this->username == $username && $this->password == $password){
	           // $_SESSION['login'] = $username;
	           // $_SESSION["checkBrowser"] = $_SERVER['HTTP_USER_AGENT'];
	           // return TRUE;
	       // } 
	       // else{
	       // return FALSE;
	       // }
	}
       
        public function destroySession(){
            session_unset();
            session_destroy();
        }
        //kollar om sessionen är satt och att den är samma webbläsare som vid inloggning
        public function loginStatus(){
                 if(isset($_SESSION['checkBrowser']) && $_SESSION["checkBrowser"] === $_SERVER['HTTP_USER_AGENT']){
                     if(isset($_SESSION['login'])){
                         return TRUE;
                     }
                 }
                else{
                    return FALSE;
                }
            
        }
        
        public function checkLoginCookie($username,$password){
        	var_dump($username);
        	var_dump($password);
            $getCookieTime = file_get_contents('cookieTime.txt');
			$login = $this->checkLogin($username, $password);
            if ($login == TRUE && $getCookieTime > time()){
            	//var_dump($password);
				$_SESSION["login"] = $username;
				$_SESSION["checkBrowser"] = $_SERVER['HTTP_USER_AGENT'];
    			return TRUE;
			}
			else{
				return FALSE;
			}
        }
        
    }