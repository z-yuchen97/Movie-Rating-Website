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
		
		$token=$json_obj['token'];
		if(!hash_equals($_SESSION['token'], $token)){
			echo json_encode(array(
				"success" => false,
				"message" => "Request forgery detected"
			));
		}
		
		$filmname = htmlspecialchars($json_obj['filmname']);
		$filmurl = htmlspecialchars($json_obj['filmurl']);
		$full_path = sprintf("/var/www/html/moviewebsite/%s",$filmurl);
		if($_SESSION['administer'] == "yes"){
			$judge="ok";
		}else{
			$judge="false";
		}
		//select database
		$mysqli = new mysqli('localhost', 'zyc', '19970108', 'moviewebsite');
			if($mysqli->connect_errno) {
				printf("Connection Failed: %s\n", $mysqli->connect_error);
				exit;
			}
		
		//Firstly, delete the user's comments
		$stmt = $mysqli->prepare("DELETE FROM comments WHERE moviename=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $filmname);
		$stmt->execute();
		$stmt->close();
		
		//Secondly, delete the user's rating records
		$stmt = $mysqli->prepare("DELETE FROM rating_record WHERE moviename=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $filmname);
		$stmt->execute();
		$stmt->close();
					
		//Finally, delete the user's events datas
		$stmt = $mysqli->prepare("DELETE FROM movie_information WHERE movie_name=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $filmname);
		$stmt->execute();
		echo json_encode(array(
			"success" => true
		));
		$stmt->close();
		$case=unlink($full_path); 
?>