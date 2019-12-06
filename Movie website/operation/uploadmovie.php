<?php
session_start();

$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];
		
if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
	die("Session hijack detected");
}else{
	$_SESSION['useragent'] = $current_ua;
}

if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

$moviename = $_POST['moviename'];
if($moviename==null){
	header("Location: addmovie.html");
	exit;
}

$type = $_POST['type'];
if($type==null){
	header("Location: addmovie.html");
	exit;
}

$introduction = $_POST['introduction'];
if($introduction==null){
	header("Location: addmovie.html");
	exit;
}

$filename = basename($_FILES['uploadedfile']['name']);
$full_path = sprintf("/var/www/html/moviewebsite/moviepicture/%s", $filename);
move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $full_path);
header("Location: addmovie.html");
?>