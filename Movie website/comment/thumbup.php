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
   
	//select database
    $mysqli = new mysqli('localhost', 'zyc', '19970108', 'moviewebsite');
	if($mysqli->connect_errno) {
		printf("Connection Failed: %s\n", $mysqli->connect_error);
		exit;
	}
	
	$comment_id=htmlentities($json_obj['comment_id']);
	
	$stmt = $mysqli->prepare("SELECT likes FROM comments WHERE id=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('i', $comment_id);
	$stmt->execute();
	$stmt->bind_result($likes);
	$stmt->fetch();
	$html_safe_likes=htmlentities($likes);
	$newlikes=++$html_safe_likes;
	$stmt->close();
	
	//you can thumb up
	$stmt = $mysqli->prepare("UPDATE comments set likes=? where id=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('ii', $newlikes, $comment_id);
	$stmt->execute();
	echo json_encode(array(
		"success" => true,
	));
	$stmt->close();
?>