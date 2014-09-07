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
	
	function verifyUser($username, $password) {
		$mysqli = getMysqlInstance();
		$id = -1;
		
		// TODO Could be optimized, but beware of sql injection
		if ($result = $mysqli->query("SELECT * FROM user")) {
			while ($row = $result->fetch_assoc()) {
				if ($row['username'] == $username and $row['password'] == $password) {
					$verify = $row['id'];
					break;
				}
			}
		}
		
		$mysqli->close();
		return $verify;
	}
	
	function registerUser($username, $password, $email) {
		$mysqli = getMysqlInstance();
		// Check that username and/or email is  available
		$selectQuery = "SELECT id FROM user WHERE username=\"{$username}\" OR email=\"{$email}\"";
		appendToFile("sql_log.txt", "should execute query: {$selectQuery}");
		$selectResult = $mysqli->query($selectQuery);
		if ($selectResult->fetch_assoc()) {
			$mysqli->close();
			return -1;
		}
		
		$insertQuery = "INSERT INTO user (username, password, email) VALUES (\"{$username}\", \"{$password}\", \"{$email}\")";
		appendToFile("sql_log.txt", "should execute query: {$insertQuery}");
		$mysqli->query($insertQuery);
		$id = $mysqli->insert_id;
		appendToFile("sql_log.txt", "Registered user with id={$id}");
		$mysqli->close();
		return $id;
	}
	
?>