function friendsLoadCallback(data) {
	console.log("Friends:", data.friends);
};

function loadFriends() {
	send("friends.php", "{\"id\": \"" + window.userId + "\"}", friendsLoadCallback);
};