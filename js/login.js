function loginRecieveCallback (data) {
	if (data.id != -1) {
		console.log("Logged in with id", data.id);
		enterPostLogin(data.id);
	}
	else {
		alert("Login fail");
	}
};

function login(){
	window.username = $("#username-input-field").val();
	window.password = $("#password-input-field").val();
	sendWithCredentials("login.php", "{}", loginRecieveCallback);
};
