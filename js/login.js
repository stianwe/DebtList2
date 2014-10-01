var USERNAME_COOKIE_NAME = "debtlist2_username";
var PASSWORD_COOKIE_NAME = "debtlist2_password";

function loginRecieveCallback (data) {
	if (data.id != -1) {
		console.log("Logged in with id", data.id);
		// Set cookies to allow auto-login if login was successful
		$.cookie(USERNAME_COOKIE_NAME, window.username);
		$.cookie(PASSWORD_COOKIE_NAME, window.password);
		enterPostLogin(data.id);
	}
	else {
		alert("Login fail");
	}
};

function login(auto) {
	if (!auto) {
		window.username = $("#username-input-field").val();
		window.password = $("#password-input-field").val();
		console.log("Manual login");
	} else {
		console.log("Auto-login");
	}
	sendWithCredentials("login.php", "{}", loginRecieveCallback);
};

function tryAutoLogin() {
	if ($.cookie(USERNAME_COOKIE_NAME) != undefined && $.cookie(PASSWORD_COOKIE_NAME) != undefined) {
		window.username = $.cookie(USERNAME_COOKIE_NAME);
		window.password = $.cookie(PASSWORD_COOKIE_NAME);
		login(true);
	}
};