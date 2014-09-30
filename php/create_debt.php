<?php

	include "debt.php";

	$rcvd = receive();
	$data = json_decode($rcvd[1]);
	$userId = verifyUserHelper($data);
	$response = -1;
	if ($userId != -1) {
		// Valid user credentials
		$response = createDebt(floatval($data->{"amount"}), $data->{"what"}, $data->{"comment"}, intval($data->{"fromUser"}), intval($data->{"toUser"}), $userId);
	}
	send($rcvd[0], json_encode(array("response" => $response)));

?>