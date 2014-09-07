<?php

	include "utils.php";
	include "sql.php";

	function loadFriends($userId) {
		$mysqli = getMysqlInstance();
		$query = "SELECT fr.id, fr.requestingUser, fr.otherUser, fr.status, u.username 
				  FROM friendRequest AS fr, user AS u 
				  WHERE (fr.requestingUser=\"{$userId}\" OR fr.otherUser=\"{$userId}\") 
				  AND (u.id=fr.requestingUser OR u.id=fr.otherUser)
				  AND u.id!=\"{$userId}\"";
		$friends = array();
		if ($res = $mysqli->query($query)) {
			while ($row = $res->fetch_assoc()) {
				$tmp = array("id" => $row["id"], "requestingUser" => $row["requestingUser"], "otherUser" => $row["otherUser"], "status" => $row["status"], "username" => $row["username"]);
				array_push($friends, $tmp);
				$tmp2 = json_encode($tmp);
			}
		}
		$mysqli->close();
		return $friends;
	}

	$recvd = receive();
	$data = json_decode($recvd[1]);
	$userId = $data->{"id"};
	send($recvd[0], json_encode(array("friends" => loadFriends($userId))));

?>