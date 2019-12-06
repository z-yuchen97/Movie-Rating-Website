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
		$json_str = file_get_contents('php://input');
		$json_obj = json_decode($json_str, true);
		
		$token=$json_obj['token'];
		if(!hash_equals($_SESSION['token'], $token)){
			echo json_encode(array(
				"success" => false,
				"message" => "Request forgery detected"
			));
		}
		
		//select database
		$mysqli = new mysqli('localhost', 'zyc', '19970108', 'moviewebsite');
			if($mysqli->connect_errno) {
				printf("Connection Failed: %s\n", $mysqli->connect_error);
				exit;
			}
					
		$discuss = htmlentities($json_obj['discuss']);
		$username=$_SESSION['username'];
		$moviename=$_SESSION['filmname'];
		
		if($discuss == null){
			echo json_encode(array(
				"success" => false,
				"message" => "comments cannot be null!"
			));
			exit;
		}
		
		//get the stars that user give to the movie
		$stmt = $mysqli->prepare("SELECT stars FROM rating_record where username=? and moviename=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('ss', $username,$moviename);
		$stmt->execute();
		$stmt->bind_result($point);
		$stmt->fetch();
		$html_safe_point=htmlentities($point);
		$stmt->close();
		
		if($html_safe_point==""){
			echo json_encode(array(
				"success" => false,
				"message" => "Sorry, Please rate before comment"
			));
			exit;
		}
		
		//insert the information of comment into the database 
		$stmt = $mysqli->prepare("insert into comments (moviename, username, contents, stars) values (?, ?, ?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('sssd',$moviename, $username, $discuss, $html_safe_point);
		$stmt->execute();
		if(true){
		echo json_encode(array(
			"success" => true
		));
		}
		$stmt->close();
?>