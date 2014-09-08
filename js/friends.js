function loadFriendsCallback(data) {
	console.log("Friends:", data.friends);
};

function loadFriends() {
	sendWithCredentials("load_friends.php", "{}", loadFriendsCallback);
};

function addFriendCallback(data) {
	console.log("Response:", data.response);
};

function addFriend() {
	var username = $("#usernamefriend").val();
	sendWithCredentials("create_friend_request.php", "{\"toUserUsername\": \"" + username + "\"}", addFriendCallback);
};