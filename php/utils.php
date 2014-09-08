<?php

	function receive() {
		if (isset($_GET["callback"])) {
			return array($_GET["callback"], $_GET["data"]);
		}
		else {
			return "";
		}
	}
	
	function send($callback, $dataToSend) {
		header('Content-Type: text/javascript; charset=UTF-8');
		echo $callback."(".$dataToSend.");";
	}
	
	function appendToFile($fileName, $data) {
		$file_handle = fopen($fileName, "a");
		fwrite($file_handle, $data);
		fwrite($file_handle, "\n");
		fclose($file_handle);
	}
	
	function readFromFile($filename) {
		echo("Reading file: {$filename}..<br />");
		return explode("\n", file_get_contents($filename));
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
	
	function verifyUser($username, $password) {
		$mysqli = getMysqlInstance();
		$id = -1;

		// TODO Could be optimized, but beware of sql injection
		if ($result = $mysqli->query("SELECT * FROM user")) {
			while ($row = $result->fetch_assoc()) {
				if ($row['username'] == $username and $row['password'] == $password) {
					$id = $row['id'];
					break;
				}
			}
		}
		
		$mysqli->close();
		return $id;
	}

	function verifyUserHelper($data) {
		return verifyUser($data->{"username"}, $data->{"password"});
	}
	
?>