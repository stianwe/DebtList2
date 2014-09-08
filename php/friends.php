<?php

	include "utils.php";
	include "sql.php";

	define("FRIEND_REQUEST_CREATED", -1);
	define("INVALID_USER_CREDENTIALS", -2);
	define("USER_NOT_FOUND", -3);
	define("SQL_ERROR", -4);
	define("SELF_ADD_ERROR", -5);

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
			}
		}
		$mysqli->close();
		return $friends;
	}

	function createFriendRequest($fromUserId, $toUserId) {
		if ($toUserId == -1) {
			return USER_NOT_FOUND;
		}
		else if ($toUserId == $fromUserId) {
			return SELF_ADD_ERROR;
		}
		$mysqli = getMysqlInstance();
		// Check that the users don't already have a friend request between them
		$alreadyFriendsQuery = "SELECT status 
								FROM friendRequest
								WHERE (requestingUser=\"{$fromUserId}\" AND otherUser=\"{$toUserId}\")
								   OR (requestingUser=\"{$toUserId}\" AND otherUser=\"{$fromUserId}\")";
		if ($res = $mysqli->query($alreadyFriendsQuery)) {
			if ($row = $res->fetch_assoc()) {
				// Request already exists - give some error message
				$status = $row["status"];
				$mysqli->close();
				return $status;
			} else {
				// Request doesn't already exist - create it!
				$insertQuery = "INSERT INTO friendRequest
								(requestingUser, otherUser, status)
								VALUES (\"{$fromUserId}\", \"{$toUserId}\", \"0\")";
				$mysqli->query($insertQuery);
				return FRIEND_REQUEST_CREATED;
			}
		} else {
			// TODO
			return SQL_ERROR;
		}
	}

?>