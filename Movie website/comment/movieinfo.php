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
		
		header("Content-Type: application/json"); 
		$json_str = file_get_contents('php://input');
		$json_obj = json_decode($json_str, true);
		
		$token = $_SESSION['token'];
		$username = $_SESSION['username'];
		$filmname = $_SESSION['filmname'];
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
					
		$stmt = $mysqli->prepare("SELECT movie_name, picture_url, stars, movie_introduction FROM movie_information where movie_name=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $filmname);
		$stmt->execute();
		$result = $stmt->get_result();	
		$data = array();
		while($row = $result->fetch_assoc()){
			array_push($data, array(
			    "movie_name" => htmlspecialchars($row['movie_name']),
			    "picture_url" => htmlspecialchars($row['picture_url']),
			    "stars" => htmlentities($row['stars']),
				"introduction" => htmlentities($row['movie_introduction']),
				"adjudge" => $judge
			));
		}
		if(true){
		echo json_encode(array(
			"success" => true,
			"token" => $token,
			"datas" => $data
		));
		exit;
		}
		$stmt->close();
?>