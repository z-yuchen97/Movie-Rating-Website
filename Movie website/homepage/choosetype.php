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
		
		$username = $_SESSION['username'];
		$type=htmlentities($json_obj['types']);
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
					
		$stmt = $mysqli->prepare("SELECT movie_name, picture_url, stars FROM movie_information where category=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $type);
		$stmt->execute();
		$result = $stmt->get_result();	
		$data = array();
		while($row = $result->fetch_assoc()){
			array_push($data, array(
			    "movie_name" => htmlspecialchars($row['movie_name']),
			    "picture_url" => htmlspecialchars($row['picture_url']),
			    "stars" => htmlentities($row['stars']),
				"adjudge" => $judge
			));
		}
		if(true){
		echo json_encode(array(
			"success" => true,
			"datas" => $data
		));
		exit;
		}
		$stmt->close();
?>