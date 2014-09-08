function loadFriendsCallback(data) {
	console.log("Friends:", data.friends);
};

function loadFriends() {
	sendWithCredentials("load_friends.php", "{}", loadFriendsCallback);
};

function addFriendCallback(data) {
	console.log("Response:", data.response);
	switch (parseInt(data.response)) {
		case -1:
			alert("Friend request sent.");
			break;
		case -2:
			alert("You do not appear logged in.")
			break;
		case -3:
			alert("User not found.");
			break;
		case -4:
			alert("SQL error.");
			break;
		case -5:
			alert("You cannot add yourself as a friend.");
			break;
		case 0:
			alert("Friend request already exists.");
			break;
		case 1:
			alert("You are already friends.");
			break;
		case 2:
			alert("Friend request already exists(2).");
			break;
		default:
			alert("Unexpected response from server. Please report this incident. Error code: " + data.response);
	};
};

function addFriend() {
	var username = $("#usernamefriend").val();
	sendWithCredentials("create_friend_request.php", "{\"toUserUsername\": \"" + username + "\"}", addFriendCallback);
};