<?php

	function getMysqlInstance() {
		// TODO: Read info from file
		$mysqli = new mysqli("localhost", "root", "48214507", "debtlist2");
		if (mysqli_connect_errno()) {
			printf("Can't connect to SQL server. Error code %s\n", mysqli_connect_error($mysqli));
			exit;
		}
		$mysqli->query("SET NAMES 'utf8'");
		return $mysqli;
	}

	function logError($msg, $mysqli, $stmt) {
		appendToFile("sql_log.txt", $msg . " failed: (" . $mysqli->errno . ") " . $stmt->error);
	}

	function getUserId($username) {
		$mysqli = getMysqlInstance();
		$query = "SELECT id
				  FROM user
				  WHERE username=?";
		if (!($stmt = $mysqli->prepare($query))) {
			logError("Prepare", $mysqli, $stmt);
			return -1;
		}
		if (!($stmt->bind_param("s", $username))) {
			logError("Bind", $mysqli, $stmt);
			return -1;
		}
		if (!($stmt->execute())) {
			logError("Execute", $mysqli, $stmt);
			return -1;
		}
		$userId = -1;
		if (!($res = $stmt->get_result())) {
			logError("Get result", $mysqli, $stmt);
			return -1;
		}
		if ($row = $res->fetch_assoc()) {
			$userId = $row["id"];
		}
		$stmt->close();
		$mysqli->close();
		return $userId;
	}
	
?>