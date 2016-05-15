<?php

session_start();

if(!(isset($_POST['identifier']) && isset($_POST['password']))) {
	echo 'too little information to log in';
	exit();
}
//connecting to the database and picking the table to read from
require_once('connect.php');
// preparing the sql statement
$stmt = mysqli_prepare($conn, 'SELECT * FROM agenda.users WHERE username=? OR email =? LIMIT 1;');
// binding the values in the statement to the post values
mysqli_stmt_bind_param($stmt, 'ss', $_POST['identifier'], $_POST['identifier']);
// executing the statement
mysqli_stmt_execute($stmt);
// storing the result of the executed statement
$result = mysqli_stmt_get_result($stmt);
// creating an array from the result
$array = mysqli_fetch_array($result);
// verifying the result and setting the appropriate response
if(empty($array)) {
    $response = 'username/email not found';
} else if(password_verify($_POST['password'], $array['hash'])) {
	// destroying the current session if it is already being used
	if(isset($_SESSION['username']) || isset($_SESSION['password'])) {
		session_destroy();
		session_start();
	}
	// sets the session variables if the user entered the right credentials
	$_SESSION['username'] = $array['username'];
	$_SESSION['password'] = $_POST['password'];
    $response = 'success';
} else {
	$response = 'username/email and password do not match';
}
// closing the statement and the connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
// replying the status of the request
echo $response;

?>
