<?php 
        ini_set("session.cookie_httponly", 1);
        session_start();
		
		$previous_ua = @$_SESSION['useragent'];
		$current_ua = $_SERVER['HTTP_USER_AGENT'];
		
		if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
			die("Session hijack detected");
		}else{
			$_SESSION['useragent'] = $current_ua;
		}
		
		header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
		
		//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
		$json_str = file_get_contents('php://input');
		//This will store the data into an associative array
		$json_obj = json_decode($json_str, true);
		
		//select database
		$mysqli = new mysqli('localhost', 'zyc', '19970108', 'moviewebsite');
			if($mysqli->connect_errno) {
				printf("Connection Failed: %s\n", $mysqli->connect_error);
				exit;
			}
					
		//Variables can be accessed as such:
		$registername = htmlspecialchars($json_obj['registername']);
		$registerpassword = htmlspecialchars($json_obj['registerpassword']);
		
		//Username cannot be empty and has regular expression restrictions
		
		if($registername==null){
			echo json_encode(array(
				"success" => false,
				"message" => "Name can not be null!"
			));
			exit;
		}else if(!preg_match('/^[\w\.\s]+$/',$registername)){
			echo json_encode(array(
				"success" => false,
				"message" => "Invalid name!Do not match the regular expression!"
			));
			exit;
		  }else if(mb_strlen($registerpassword) < 8) {
		// Verify password length
			echo json_encode(array(
				"success" => false,
				"message" => "password length shouldn't less than 8!"
			));
			exit;
        }
        
        // Check if the username already exists
        $stmt = $mysqli->prepare("SELECT username FROM users WHERE username=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $registername);
		$stmt->execute();
		$stmt->bind_result($exist_name);
        $stmt->fetch();
        if (htmlspecialchars($exist_name)) {
			echo json_encode(array(
				"success" => false,
				"message" => "Username already exists!"
			));
			exit;
        }
		$stmt->close();
    
        // Create a hash of the input password
		$passwordHash = password_hash($registerpassword, PASSWORD_BCRYPT);
        if ($passwordHash === false) {
            echo json_encode(array(
            	"success" => false,
            	"message" => "Fail to hash the password!"
            ));
			exit;
        }
        
		$stmt = $mysqli->prepare("insert into users (username, hashed_password) values (?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('ss',$registername, $passwordHash);
		$stmt->execute();
		if(true){
		echo json_encode(array(
			"success" => true
		));
		}
		$stmt->close();
?>