<?php
	include "utils.php";
	include "sql.php";
	
	$tmp = receive();
	$data = $tmp[1];
	$data = json_decode($tmp[1]);
	appendToFile("login_log.txt", $data);
	// Check username and password
	$id = verifyUserHelper($data);
	send($tmp[0], json_encode(array('id' => $id)));
?>