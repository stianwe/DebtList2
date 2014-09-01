function recieveCallback (data) {
	if (data.response == 1) {
		alert("Login");
	}
	else {
		alert("Login fail");
	}
};

function login(){
	var username = $("#username-input-field").val();
	var password = $("#password-input-field").val();
	send("login.php", "{\"username\": \"" + username + "\", \"password\": \"" + password + "\"}", recieveCallback);
};
