<?php
	include "utils.php";
	include "sql.php";
	
	$tmp = receive();
	$data = $tmp[1];
	$data = json_decode($tmp[1]);
	$username = $data->{"username"};
	$password = $data->{"password"};
	appendToFile("login_log.txt", "username={$username}, password={$password}");
	// Check username and password
	$id = verifyUser($username, $password);
	send($tmp[0], json_encode(array('id' => $id)));
?>