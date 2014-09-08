<?php

	include "friends.php";

	$recvd = receive();
	$data = json_decode($recvd[1]);
	$userId = verifyUserHelper($data);
	$response = "";
	if ($userId == -1) {
		$response = "-1";
	} else {
		$response = loadFriends($userId);
	}
	send($recvd[0], json_encode(array("friends" => $response)));

?>