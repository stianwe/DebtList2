<?php

	include "friends.php";

	$rcvd = receive();
	$data = json_decode($rcvd[1]);
	$userId = verifyUserHelper($data);
	$response = -1;
	if ($userId != -1) {
		// Valid user credentials
		$response = respondToFriendRequest(intval($data->{"friendRequestId"}), intval($data->{"response"}), $userId);
	}
	send($rcvd[0], json_encode(array("response" => $response)));

?>