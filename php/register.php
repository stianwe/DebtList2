<?php
	include "utils.php";
	include "sql.php";
	
	$tmp = receive();
	$data = json_decode($tmp[1]);
	$username = $data->{"username"};
	$password = $data->{"password"};
	$email = $data->{"email"};
	appendToFile("register_log.txt", "username={$username}, password={$password}, email={$email}");
	$id = registerUser($username, $password, $email);
	send($tmp[0], json_encode(array('id' => "{$id}")));
?>