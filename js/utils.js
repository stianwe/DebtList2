
function send(page, data, success) {
	$.ajax({
		type		:	"GET",
		url			:	"http://192.168.0.111/php/" + page,
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
}

function enterPostLogin(userId) {
	$.mobile.changePage("#addfriend", { transition: "flow", changeHash: true });
	window.userId = userId;
	loadFriends();
};