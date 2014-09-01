<?php
	include "utils.php";
	
	$tmp = receive();
	$data = json_decode($tmp[1]);
	appendToFile("testFile.txt", "{$data}");
	send($tmp[0], json_encode(array('response' => 1, 'data' => "test")));
?>