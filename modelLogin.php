<?php


    class modelLogin{
    private $dbConnection;
	private $dbConnectionUsername = "root";
	private $dbConnectionPassword = "";
	private $dbConnectionDBName = "loginlabb4";
	private $dbConnectionHost = "127.0.0.1";
	
	private $newUsername;
	private $newPassword;
	
	private $usernameInCookie;
	private $passwordInCookie;
	
	private $usernameToCheck;
	private $passwordToCheck;
    //private $username = "Admin";
    //private $password = "dc647eb65e6711e155375218212b3964";
    
    public function __construct(){
        $this->dbConnection = mysqli_connect($this->dbConnectionHost, $this->dbConnectionUsername, $this->dbConnectionPassword, $this->dbConnectionDBName);
    }
	
	public function CheckIfUsernameIsAvailable($inputUsername){
		//$connection = mysqli_connect("127.0.0.1", "root", "", "loginlabb4");
		
		
    	if (mysqli_connect_errno($this->dbConnection)){
        	echo "MySql Error: " . mysqli_connect_error();
    	}
		$query = mysqli_query($this->dbConnection,"SELECT * FROM member WHERE username='$inputUsername'");
		$count = mysqli_num_rows($query);
    	$row = mysqli_fetch_array($query);
		
		if($count == 1){
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
	
	public function SetFailUser($failUsername){
		$_SESSION['FailUser'] = $failUsername;
	}
	
	public function GetFailUser(){
		if(isset($_SESSION['FailUser'])){
			return $_SESSION['FailUser'];
		}
		return NULL;
		
	}
	
	public function Save($newUsername, $newPassword){
		$this->newUsername = $newUsername;
		$this->newPassword = md5($newPassword);
		//$newPassword = md5($newPassword);
		//$this->username = $newUsername;
		//$this->password = $newPassword;
		$connection = mysqli_connect("127.0.0.1", "root", "", "loginlabb4");
    	if (mysqli_connect_errno($connection)){
        	echo "MySql Error: " . mysqli_connect_error();
    	}
		
    	mysqli_query($connection,"INSERT member SET Username = '$this->newUsername', Password = '$this->newPassword'");
	
		mysqli_close($connection);
		$_SESSION['successUser'] = $this->newUsername;
		$_SESSION['successMessage'] = "Registrereingen lyckades!";
	}
	
    //Lyckad inloggning sätt sessionen till webbläsaren användaren loggade in i
    public function checkLogin($usernameToCheck, $passwordToCheck) {
    	//Min kod
    	$this->usernameToCheck = $usernameToCheck;
		$this->passwordToCheck = $passwordToCheck;
    	if (mysqli_connect_errno($this->dbConnection)){
        	echo "MySql Error: " . mysqli_connect_error();
    	}

    	$query = mysqli_query($this->dbConnection,"SELECT * FROM member WHERE username='$this->usernameToCheck' && password='$this->passwordToCheck'");
    	$count = mysqli_num_rows($query);
    	$row = mysqli_fetch_array($query);

    	if ($count == 1){
    		
        	$_SESSION['login'] = $this->usernameToCheck;
	    	$_SESSION["checkBrowser"] = $_SERVER['HTTP_USER_AGENT'];
			//mysqli_close($connection);
	    	return TRUE;
    	}else{
    		//mysqli_close($connection);
       		return false;
    	}   
	mysqli_close($connection);
    
	}
       
	   public function GetLoggedInUser(){
	   		return $_SESSION['login'];
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
        
        public function checkLoginCookie($usernameInCookie,$passwordInCookie){
        	//var_dump($username);
        	//var_dump($password);
            $getCookieTime = file_get_contents('cookieTime.txt');
			$login = $this->checkLogin($usernameInCookie, $passwordInCookie);
            if ($login == TRUE && $getCookieTime > time()){
            	//var_dump($password);
				$_SESSION["login"] = $usernameInCookie;
				$_SESSION["checkBrowser"] = $_SERVER['HTTP_USER_AGENT'];
    			return TRUE;
			}
			else{
				return FALSE;
			}
        }
        
    }