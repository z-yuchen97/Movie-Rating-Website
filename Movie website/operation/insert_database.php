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
					
		//Variables can be accessed as such:
		$moviename = htmlspecialchars($json_obj['moviename']);
		$introduction = htmlentities($json_obj['introduction']);
		$type = htmlentities($json_obj['type']);
		$filmurl = htmlspecialchars($json_obj['filmurl']);
		$url=sprintf("moviepicture/%s",$filmurl);
		
		if($moviename==null){
			echo json_encode(array(
				"success" => false,
				"message" => "movie name cannot be null!"
			));
			exit;
		}
		
		if($type==null){
			echo json_encode(array(
				"success" => false,
				"message" => "You must choose a type!"
			));
			exit;
		}
		
		if($introduction==null){
			echo json_encode(array(
				"success" => false,
				"message" => "Movie's introduction cannot be null!"
			));
			exit;
		}
		
		// Check if the pciture name already exists
		$stmt = $mysqli->prepare("SELECT picture_url FROM movie_information WHERE picture_url=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $url);
		$stmt->execute();
		$stmt->bind_result($old_url);
		$stmt->fetch();
		$html_safe_old_url=htmlspecialchars($old_url);
		if (htmlspecialchars($html_safe_old_url)) {
			echo json_encode(array(
				"success" => false,
				"message" => "picture name already exists!"
			));
			exit;
		}
		$stmt->close();
		
		// Check if the movie name already exists
		$stmt = $mysqli->prepare("SELECT movie_name FROM movie_information WHERE movie_name=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $moviename);
		$stmt->execute();
		$stmt->bind_result($movie_name);
		$stmt->fetch();
		$html_safe_movie_name=htmlspecialchars($movie_name);
		if (htmlspecialchars($html_safe_movie_name)) {
			echo json_encode(array(
				"success" => false,
				"message" => "movie has already exists!"
			));
			exit;
		}
		$stmt->close();
		
		$stmt = $mysqli->prepare("insert into movie_information (movie_name, picture_url, movie_introduction, category) values (?, ?, ?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('ssss',$moviename, $url, $introduction, $type);
		$stmt->execute();
		if(true){
		echo json_encode(array(
			"success" => true
		));
		}
		$stmt->close();
?>