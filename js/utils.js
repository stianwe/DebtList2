
function send(page, data, success) {
	$.ajax({
		type		:	"GET",
		url			:	"http://192.168.0.105/php/" + page,
		data		:	"data=" + data,
		dataType	:	"jsonp",
		jsonp		:	"callback",
		crossDomain	:	true,
		success		:	success,
		error		:	function(a, b, c) { alert("ERROR! " + a + " " + b + " " + c); }
	});
};

function sendWithCredentials(page, data, success) {
	send(page, "{\"username\": \"" + window.username + "\", \"password\": \"" + window.password + "\""  + (data.length > 2 ? ", " : "") + data.substring(1, data.length), success);
};

function showFriends() {
	window.currentPage = "#view-friends-page";
	loadFriends();
	$.mobile.changePage("#view-friends-page", { transition: "flow", changeHash: true });
};

function showDebts(reverseTransition) {
	window.currentPage = "#front-page";
	loadDebts();
	$.mobile.changePage("#front-page", { transition: "flow", changeHash: true, reverse: reverseTransition });
};

function swipe(event) {
	if (event.type == "swipeleft") {
		if (window.currentPage == "#front-page") {
			showFriends();
		}
	} else if (event.type == "swiperight") {
		if (window.currentPage == "#view-friends-page") {
			showDebts(true);
		}
	} else {
		console.log("Unexpected event: " + event.type);
	}
};

function enterPostLogin(userId) {
	window.userId = userId;
	showDebts(false);
	$(document).on("swiperight swipeleft", swipe);
};