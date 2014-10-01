<?php

	include "utils.php";
	include "sql.php";

	define("INVALID_USER_CREDENTIALS", -5);
	define("UNKOWN_STATUS", -4);
	define("SQL_ERROR", -3);
	define("NOT_PERMITTED", -2);
	define("DEBT_NOT_FOUND", -1);
	define("REQUESTED", 0);
	define("ACCEPTED", 1);
	define("DECLINED", 2);
	define("COMPLETED_BY_REQUESTING_USER", 3);
	define("COMPLETED_BY_OTHER_USER", 4);
	define("COMPLETED", 5);
	define("DELETED", 6);

	function loadDebts($userId) {
		$mysqli = getMysqlInstance();
		$query = "SELECT d.id, d.amount, d.what, d.comment, d.fromUser, d.toUser, d.requestingUser, d.status, u.username
				  FROM debt AS d, user AS u 
				  WHERE (d.fromUser=\"{$userId}\" OR d.toUser=\"{$userId}\")
				  AND ((u.id=d.fromUser OR u.id=d.toUser) AND u.id!=\"{$userId}\")";
		$debts = array();
		if ($res = $mysqli->query($query)) {
			while ($row = $res->fetch_assoc()) {
				if ($row["status"] == DELETED) {
					continue;
				}
				array_push($debts, array("id" => $row["id"], "amount" => $row["amount"], "what" => $row["what"], "comment" => $row["comment"], "fromUser" => $row["fromUser"], "toUser" => $row["toUser"], "requestingUser" => $row["requestingUser"], "status" => $row["status"], "otherUsername" => $row["username"]));
			}
		} else {
			appendToFile("loadDebts_log.txt", "Error while loading debts for user with id={$userId}: " + mysqli_connect_error($mysqli));
		}

		return $debts;
	}

	function verifyAcceptDecline($requestingUser, $userId, $fromUser, $toUser, $oldStatus) {
		return ($requestingUser != $userId && ($fromUser == $userId || $toUser == $userId)) && $oldStatus == REQUESTED;
	}

	/*
		Note: This function must be called before updating the status of the
		debt that is being accepted, as it assumes only one (or no) debt with status completed
		exists between the two users.
	*/
	function mergeDebts($mysql, $debtId, $amount, $what, $comment, $toUser, $fromUser) {
		// First find the debt that it should merge with (if any)
		$selectQuery = "SELECT id, amount, comment, fromUser, toUser
					    FROM debt
					    WHERE what=\"{$what}\"
					    AND status=\"" . ACCEPTED . "\"
					    AND ((toUser=\"{$toUser}\" AND fromUser=\"{$fromUser}\")
				    		 OR (toUser=\"{$fromUser}\" AND fromUser=\"{$toUser}\"))
						FOR UPDATE";
	    if ($res = $mysql->query($selectQuery)) {
	    	if ($row = $res->fetch_assoc()) {
	    		// TODO REMOVE
	    		$iidd = $row["id"];
	    		$newAmount = $amount + ($toUser == $row["toUser"] ? $row["amount"] : -$row["amount"]);
	    		$newTo = $toUser;
	    		$newFrom = $fromUser;
	    		$newStatus = ACCEPTED;
	    		$newComment = $comment . " + " . $row["comment"];
	    		// If new amount is negative, swap to/from and remove minus
	    		if ($newAmount < 0) {
	    			$newTo = $fromUser;
	    			$newFrom = $toUser;
	    			$newAmount = -newAmount;
	    		}
	    		// If new amount is negative, the debt should be completed
	    		else if ($newAmount == 0) {
	    			$newStatus = COMPLETED;
	    		}
	    		// Update the debt
	    		$updateQuery = "UPDATE debt
	    					    SET amount=\"{$newAmount}\",
	    					   	    comment=\"{$newComment}\",
	    					   	    status=\"{$newStatus}\",
	    					   	    fromUser=\"{$newFrom}\",
	    					   	    toUser=\"{$newTo}\"
	    					    WHERE id=\"{$row["id"]}\"";
	    		if ($mysql->query($updateQuery)) {
	    			// Everything went as planned -> delete the other debt
	    			$deleteQuery = "UPDATE debt
	    						    SET status=\"" . DELETED . "\"
	    						    WHERE id=\"{$debtId}\"";
					if ($mysql->query($deleteQuery)) {
						// Everything went as planned, and we are done merging the debts
						return true;
					}
					else {
						$res->free();
						throw new Exception($mysql->error);
					}
	    		}
	    		else {
					$res->free();
					throw new Exception($mysql->error);
	    		}
	    	}
	    	else {
	    		return false;
	    	}
	    }
	    else {
			$res->free();
			throw new Exception($mysql->error);
	    }
	}

	function changeStatusOfDebt($debtId, $newStatus, $userId) {
		$mysql = getMysqlInstance();
		// Start transaction
		$mysql->autocommit(false);
		$response = 0;
		$wasMerge = false;
		// First check that the user can change the status of the debt (and that the debt exists)
		$query = "SELECT * FROM debt where id=\"{$debtId}\" FOR UPDATE";
		try {
			if ($res = $mysql->query($query)) {
				if ($row = $res->fetch_assoc()) {
					$oldStatus = $row["status"];
					$fromUser = $row["fromUser"];
					$toUser = $row["toUser"];
					$requestingUser = $row["requestingUser"];
					switch ($newStatus) {
						case ACCEPTED:
							if (!verifyAcceptDecline($requestingUser, $userId, $fromUser, $toUser, $oldStatus)) {
								$response = NOT_PERMITTED;
								break;
							}
							// Merge debts
							$wasMerge = mergeDebts($mysql, $debtId, $row["amount"], $row["what"], $row["comment"], $toUser, $fromUser);
							break;
						case DECLINED:
							if (!verifyAcceptDecline($requestingUser, $userId, $fromUser, $toUser, $oldStatus)) {
								$response = NOT_PERMITTED;
							}
							break;
						case COMPLETED_BY_REQUESTING_USER:
							if ($requestingUser != $userId || !($oldStatus == ACCEPTED || $oldStatus == COMPLETED_BY_OTHER_USER)) {
								$response = NOT_PERMITTED;
							}
							// We need to check if other user has completed it
							else if ($oldStatus == COMPLETED_BY_OTHER_USER) {
								$newStatus = COMPLETED;
							}
							break;
						case COMPLETED_BY_OTHER_USER:
							if (!($requestingUser != $userId && ($fromUser == $userId || $toUser == $userId)) || 
								!($oldStatus == ACCEPTED || $oldStatus == COMPLETED_BY_REQUESTING_USER)) {
								$response = NOT_PERMITTED;
							}
							// We need to check if requesting user has completed it
							else if ($oldStatus == $COMPLETED_BY_REQUESTING_USER) {
								$newStatus = COMPLETED;
							}
							break;
						default:
							$response = UNKOWN_STATUS;
					}
					// Update status if valid so far and it wasn't a debt
					if ($response >= 0 && !$wasMerge) {
						$updateStatusQuery = "UPDATE debt
											  SET status=\"{$newStatus}\"
											  WHERE id=\"{$debtId}\"";
						$mysql->query($updateStatusQuery);
						$response = $newStatus;
					}
				} else {
					$response = DEBT_NOT_FOUND;
				}
			} else {
				$res->free();
				throw new Exception($mysql->error);
			}
			$mysql->commit();
		}
		catch (Exception $e) {
			appendToFile("debt_log.txt", "ERROR: {$e}");
			$response = SQL_ERROR;
			$mysql->rollback();
		}
		// End transaction
		$mysql->autocommit(true);
		$mysql->close();
		return $response;
	}

	function createDebt($amount, $what, $comment, $fromUser, $toUser, $requestingUser) {
		// First check that the requesting user is one of the to/from users
		if ($fromUser != $requestingUser && $toUser != $requestingUser) {
			return NOT_PERMITTED;
		}
		$query = "INSERT INTO debt " .
				 "(amount, what, comment, fromUser, toUser, requestingUser, status) " .
				 "VALUES " .
				 "(?, ?, ?, ?, ?, ?, \"" . REQUESTED . "\")";
		$mysqli = getMysqlInstance();
		if (!($stmt = $mysqli->prepare($query))) {
			logError("Prepare", $mysqli, $stmt);
			return SQL_ERROR;
		}
		if (!($stmt->bind_param("dssiii", $amount, $what, $comment, $fromUser, $toUser, $requestingUser))) {
			logError("Bind", $mysqli, $stmt);
			return SQL_ERROR;
		}
		if (!($stmt->execute())) {
			logError("Execute", $mysqli, $stmt);
			return SQL_ERROR;
		}
		$stmt->close();
		$mysqli->close();
		return REQUESTED;
	}

?>