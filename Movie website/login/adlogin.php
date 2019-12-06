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

$_SESSION['login?'] = 0;

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
$adusername = htmlspecialchars($json_obj['adusername']);
$adpassword = htmlspecialchars($json_obj['adpassword']);
//This is equivalent to what you previously did with $_POST['username'] and $_POST['password']
$stmt = $mysqli->prepare("SELECT COUNT(*), username, hashed_password FROM users WHERE username=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	// Bind the parameter
	$stmt->bind_param('s', $adusername);
	$stmt->execute();
			
	// Bind the results
	$stmt->bind_result($cnt, $aduser_name, $adpwd_hash);
	$stmt->fetch();
	
	$html_safe_cnt= htmlentities($cnt);
	$html_safe_aduser_name= htmlspecialchars($aduser_name);
	$html_safe_adpwd_hash= htmlspecialchars($adpwd_hash);
	
// Check to see if the username and password are valid.  (You learned how to do this in Module 3.)
if( $html_safe_cnt == 1 && password_verify($adpassword, $html_safe_adpwd_hash) && $html_safe_aduser_name=="zyc"){
	$_SESSION['username'] = $html_safe_aduser_name;
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 
	$_SESSION['administer'] = "yes";
	$_SESSION['filmname'] = "none";
	echo json_encode(array(
		"success" => true,
	));
	exit;
}else{
	echo json_encode(array(
		"success" => false,
		"message" => "Incorrect Username or Password"
	));
	exit;
}
?>