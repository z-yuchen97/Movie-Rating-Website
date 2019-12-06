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
		
		$token=$json_obj['token'];
		if(!hash_equals($_SESSION['token'], $token)){
			echo json_encode(array(
				"success" => false,
				"message" => "Request forgery detected"
			));
		}
		
		$point= htmlentities($json_obj['points']);
		if($point<1){
			echo json_encode(array(
				"success" => false,
				"message" => "Please pick a point!"
			));
			exit;
		}
		$username = $_SESSION['username'];
		$moviename=$_SESSION['filmname'];
		
		//select database
		$mysqli = new mysqli('localhost', 'zyc', '19970108', 'moviewebsite');
			if($mysqli->connect_errno) {
				printf("Connection Failed: %s\n", $mysqli->connect_error);
				exit;
			}
		
		//judge if user has already rate for this movie		
		$stmt = $mysqli->prepare("SELECT username FROM rating_record where username=? and moviename=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('ss', $username,$moviename);
		$stmt->execute();
		$stmt->bind_result($user_name);
		$stmt->fetch();
		if (htmlspecialchars($user_name)) {
			echo json_encode(array(
				"success" => false,
				"message" => "You have already rated this movie!"
			));
			exit;
		}
		$stmt->close();
		
		//if the user hasn't rated this movie before, save the rating record
		$stmt = $mysqli->prepare("insert into rating_record (username, moviename, stars) values (?, ?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('ssd',$username, $moviename, $point);
		$stmt->execute();
		$stmt->close();
		
		//calculate the movie points
		$stmt = $mysqli->prepare("SELECT stars, tick FROM movie_information where movie_name=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $moviename);
		$stmt->execute();
		$stmt->bind_result($stars,$tick);
		$stmt->fetch();
		
		$html_safe_stars=htmlentities($stars);
		$html_safe_tick=htmlentities($tick);
		
		$before_rating=$html_safe_stars*$html_safe_tick;
		$new_tick=$html_safe_tick+1;
		$new_rating=($before_rating+$point)/($new_tick);
		$format_num = sprintf("%.1f",$new_rating);
		$stmt->close();
		
		//update the stars in the comments database if user comments before rating
		$stmt = $mysqli->prepare("UPDATE comments set stars=? where username=? and moviename=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('dss', $point, $username, $moviename);
		$stmt->execute();
		$stmt->close();
		
		//update the stars and tick of the movie
		$stmt = $mysqli->prepare("UPDATE movie_information set stars=?, tick=? where movie_name=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('sss', $format_num, $new_tick, $moviename);
		$stmt->execute();
		echo json_encode(array(
			"success" => true,
			"message" => "Thank you for your rating!"
		));
		$stmt->close();
?>