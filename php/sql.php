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

	function getUserId($username) {
		$mysqli = getMysqlInstance();
		$query = "SELECT id
				  FROM user
				  WHERE username=\"{$username}\"";
		$userId = -1;
		if ($res = $mysqli->query($query)) {
			if ($row = $res->fetch_assoc()) {
				$userId = $row["id"];
			}
		}
		$mysqli->close();
		return $userId;
	}
	
?>