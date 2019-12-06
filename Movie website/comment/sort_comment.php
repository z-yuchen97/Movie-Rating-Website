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
					
		$username=$_SESSION['username'];
		$moviename=$_SESSION['filmname'];
		
		$stmt = $mysqli->prepare("SELECT moviename, username, contents, likes, time, id, stars FROM comments where moviename=? Order By likes DESC");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $moviename);
		$stmt->execute();
		$result = $stmt->get_result();	
		$data = array();
		while($row = $result->fetch_assoc()){
			array_push($data, array(
			    "moviename" => htmlspecialchars($row['moviename']),
			    "username" => htmlspecialchars($row['username']),
			    "contents" => htmlentities($row['contents']),
				"likes" => htmlentities($row['likes']),
				"time" => htmlentities(date("d/m/y, H:i:s", strtotime($row['time']))),
				"id" => htmlentities($row['id']),
				"points" => htmlentities($row['stars'])
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