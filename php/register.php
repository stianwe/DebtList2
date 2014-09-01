<?php
	include "utils.php";
	
	$tmp = receive();
	$data = $tmp[1];
	$data = json_decode($tmp[1]);
	$username = $data->{"username"};
	$password = $data->{"password"};
	$email = $data->{"email"};
	appendToFile("register_log.txt", "username={$username}, password={$password}, email={$email}");
	send($tmp[0], json_encode(array('response' => 1, 'data' => "you sent: {$tmp[1]}")));
?>