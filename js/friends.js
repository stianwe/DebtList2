function loadFriendsCallback(data) {
	console.log("Friends:", data.friends);
	fillFriendsList(data.friends);
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

function fillFriendsListHelper(list, dividerText) {
	if (list.length > 0) {
		$("#friends-list").append("<li data-role='list-divider' data-theme='f'>" + dividerText + "</li>");
	}
	list.forEach(function (friend) {
		$("#friends-list").append("<li>" + friend.username + "<div onclick='alert(\"TEST\");' class='ui-icon ui-icon-delete' style='color:black;float:right'></div><div onclick='alert(\"TEST\");' class='ui-icon ui-icon-check' style='color:black;float:right'></div></li>");
	});
};

function fillFriendsList(friends) {
	var acceptedFriends = [];
	var incomingInvites = [];
	var outgoingInvites = [];
	for (var i = 0; i < friends.length; i++) {
		var friend = friends[i];
		switch (parseInt(friend.status)) {
			case 0: // Requested
				// Check who requested it
				if (friend.requestingUser != window.userId) {
					// This is a request which this user can reply to
					incomingInvites.push(friend);
				} else {
					// This is a friend request that this user has requested
					outgoingInvites.push(friend);
				}
				break;
			case 1: // Accepted
				acceptedFriends.push(friend);
				break;
			case 2: // Declined
				// Show nothing??
				break;
			default:
				alert("Friend request with unknown status (" + friend.status + ") detected. Please report this incident.");
		}
	}
	// Display the friends
	fillFriendsListHelper(incomingInvites, "Incoming friend requests:");
	fillFriendsListHelper(acceptedFriends, "Your friends:");
	fillFriendsListHelper(outgoingInvites, "Outgoing friend requests:");
	$("#friends-list").listview("refresh");
};