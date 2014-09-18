<?php

	include "utils.php";
	include "sql.php";

	define("SQL_ERROR", -3);
	define("NOT_PERMITTED", -2);
	define("DEBT_NOT_FOUND", -1);
	define("REQUESTED", 0);
	define("ACCEPTED", 1);
	define("DECLINED", 2);
	define("COMPLETED_BY_REQUESTING_USER", 3);
	define("COMPLETED_BY_OTHER_USER", 4);
	define("COMPLETED", 5);

	function loadDebts($userId) {
		$mysqli = getMysqlInstance();
		$query = "SELECT d.id, d.amount, d.what, d.comment, d.fromUser, d.toUser, d.requestingUser, d.status, u.username
				  FROM debt AS d, user AS u 
				  WHERE (d.fromUser=\"{$userId}\" OR d.toUser=\"{$userId}\")
				  AND ((u.id=d.fromUser OR u.id=d.toUser) AND u.id!=\"{$userId}\")";
		$debts = array();
		if ($res = $mysqli->query($query)) {
			while ($row = $res->fetch_assoc()) {
				array_push($debts, array("id" => $row["id"], "amount" => $row["amount"], "what" => $row["what"], "comment" => $row["comment"], "fromUser" => $row["fromUser"], "toUser" => $row["toUser"], "requestingUser" => $row["requestingUser"], "status" => $row["status"], "otherUsername" => $row["username"]));
			}
		} else {
			appendToFile("loadDebts_log.txt", "Error while loading debts for user with id={$userId}: " + mysqli_connect_error($mysqli));
		}

		return $debts;
	}

	function changeStatusOfDebt($debtId, $newStatus, $userId) {
		$mysql = getMysqlInstance();
		$response = 0;
		// First check that the user can change the status of the debt (and that the debt exists)
		$query = "SELECT status, fromUser, toUser, requestingUser FROM debt where id=\"{$debtId}\"";
		if ($res = $mysql->query($query)) {
			if ($row = $res->fetch_assoc()) {
				$oldStatus = $row["status"];
				$fromUser = $row["fromUser"];
				$toUser = $row["toUser"];
				$requestingUser = $row["requestingUser"];
				switch ($newStatus) {
					case ACCEPTED:
						// TODO: MERGE DEBTS
					case DECLINED:
						if (!($requestingUser != $userId && ($fromUser == $userId || $toUser == $userId))) {
							$response = NOT_PERMITTED;
						}
						break;
					case COMPLETED_BY_REQUESTING_USER:
						if ($requestingUser != $userId) {
							$response = NOT_PERMITTED;
						}
						// We need to check if other user has completed it
						else if ($oldStatus == COMPLETED_BY_OTHER_USER) {
							$status = COMPLETED;
						}
						break;
					case COMPLETED_BY_OTHER_USER:
						if (!($requestingUser != $userId && ($fromUser == $userId || $toUser == $userId))) {
							$response = NOT_PERMITTED;
						}
						// We need to check if requesting user has completed it
						else if ($status == $COMPLETED_BY_REQUESTING_USER) {
							$status = COMPLETED;
						}
				}
			} else {
				$response = DEBT_NOT_FOUND;
			}
		} else {
			$response = SQL_ERROR;
		}
		$mysql->close();
		return $response;
	}

?>