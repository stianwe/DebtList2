var TIMEOUT = 12 * 1000;

function sendError(a, b, c) {
	if (b == "timeout") {
		alert("Network error: Cannot reach the server. Check your internet connection and try again later.");
	} else {
		alert("ERROR: " + a + ": " + b + ": " + c);
	}
}

function send(page, data, success) {
	$.ajax({
		type		:	"GET",
		url			:	"http://46.9.74.186/php/" + page,
		data		:	"data=" + data,
		dataType	:	"jsonp",
		jsonp		:	"callback",
		crossDomain	:	true,
		//beforeSend	: 	function () { console.log("Loading.."); $.mobile.loading('show'); },
		// success		:	function (d) { console.log("done"); success(d); $.mobile.loading('hide'); },
		success		: 	success,
		timeout 	: 	TIMEOUT,
		error		:	function (a, b, c) { sendError(a, b, c); },
	});
};

function sendWithCredentials(page, data, success) {
	send(page, "{\"username\": \"" + window.username + "\", \"password\": \"" + window.password + "\""  + (data.length > 2 ? ", " : "") + data.substring(1, data.length), success);
};

function showFriends() {
	showFriendsHelper();
	$.mobile.changePage("#view-friends-page", { transition: "flow", changeHash: true });
};

function showDebts(reverseTransition, isNotPageChange) {
	loadDebts();
	if (!isNotPageChange) {
		$.mobile.changePage("#front-page", { transition: "flow", changeHash: true, reverse: reverseTransition });
	}
};

function swipe(event) {
	if (event.type == "swipeleft") {
		if ($.mobile.activePage.attr("id") == "front-page") {
			showFriends();
		}
	} else if (event.type == "swiperight") {
		if ($.mobile.activePage.attr("id") == "view-friends-page") {
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
	console.log("ATTEMPTING TO SHOW LOADER");
	$.mobile.showPageLoadingMsg();
};