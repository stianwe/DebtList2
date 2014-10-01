<?php

	include "debt.php";

	$rcvd = receive();
	$data = json_decode($rcvd[1]);
	$userId = verifyUserHelper($data);
	$response = INVALID_USER_CREDENTIALS;
	if ($userId != -1) {
		// Valid user credentials
		$response = changeStatusOfDebt(intval($data->{"debtId"}), intval($data->{"newStatus"}), $userId);
	}
	send($rcvd[0], json_encode(array("response" => $response)));

?>