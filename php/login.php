<?php
	include "utils.php";
	
	$tmp = receive();
	$data = $tmp[1];
	$data = json_decode($tmp[1]);
	$username = $data->{"username"};
	$password = $data->{"password"};
	appendToFile("login_log.txt", "username={$username}, password={$password}");
	// TODO: Check username and password
	send($tmp[0], json_encode(array('response' => 0, 'data' => "you sent: {$tmp[1]}")));
?>