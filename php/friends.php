<?php

	include "utils.php";
	include "sql.php";

	define("FRIEND_REQUEST_CREATED", -1);
	define("INVALID_USER_CREDENTIALS", -2);
	define("USER_NOT_FOUND", -3);
	define("SQL_ERROR", -4);
	define("SELF_ADD_ERROR", -5);
	define("WRONG_USER_ERROR", -6);
	define("INVALID_STATUS_CODE", -7);

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

	function respondToFriendRequest($friendRequestId, $response, $respondingUser) {
		if ($response != 1 && $response != 2) {
			// Only options are to accept or decline a friend request
			appendToFile("friend_response_log.txt", "User with id={$respondingUser} attempted to respond to friend request with id={$friendRequestId} with response={$response}");
			return INVALID_STATUS_CODE;
		}
		$mysqli = getMysqlInstance();
		$query = "UPDATE friendRequest SET status=\"" . ($response == 1 ? 1 : 0) . "\" WHERE id=\"{$friendRequestId}\"";
		// Check that the friend request is actually to the user trying to respond to it
		$verifyQuery = "SELECT otherUser FROM friendRequest WHERE id=\"{$friendRequestId}\"";
		if ($verifyRes = $mysqli->query($verifyQuery)) {
			if ($row = $verifyRes->fetch_assoc()) {
				$otherUserId = $row["otherUser"];
				if ($otherUserId != $respondingUser) {
					return WRONG_USER_ERROR;
				} else {
					$mysqli->query($query);
					appendToFile("friend_response_log.txt", $query . " from user with id={$respondingUser}");
					return 0;
				}
			} else {
				return USER_NOT_FOUND;
			}
		} else {
			return SQL_ERROR;
		}
		return 0;
	}

?>