<?php

	include "utils.php";
	include "sql.php";

	function loadDebts($userId) {
		$mysqli = getMysqlInstance();
		$query = "SELECT * FROM debt WHERE fromUser=\"{$userId}\" OR toUser=\"${userId}\"";
		$debts = array();
		if ($res = $mysqli->query($query)) {
			while ($row = $res->fetch_assoc()) {
				array_push($debts, array("id" => $row["id"], "amount" => $row["amount"], "what" => $row["what"], "comment" => $row["comment"], "fromUser" => $row["fromUser"], "toUser" => $row["toUser"], "requestingUser" => $row["requestingUser"], "status" => $row["status"]));
			}
		} else {
			appendToFile("loadDebts_log.txt", "Error while loading debts for user with id={$userId}: " + mysqli_connect_error($mysqli));
		}

		return $debts;
	}

?>