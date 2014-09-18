<?php

	include "utils.php";
	include "sql.php";

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

?>