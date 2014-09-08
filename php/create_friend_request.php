<?php
	include "friends.php";

	$rcvd = receive();
	$response = "-1";
	$data = json_decode($rcvd[1]);
	// Verify user before doing anything
	$userId = verifyUserHelper($data);
	if ($userId == -1) {
		// Invalid user credentials
		$response = INVALID_USER_CREDENTIALS;
	} else {
		$response = createFriendRequest($userId, getUserId($data->{"toUserUsername"}));
	}
	appendToFile("createFriendRequest_log.txt", $data);
	send($rcvd[0], json_encode(array("response" => $response)));
?>