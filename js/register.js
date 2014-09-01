function registerRecieveCallback (data) {
	alert("Register " + (data.response == 1 ? "OK" : "failed") + ".");
};

function register(){
	var username = $("#register-username-input-field").val();
	var password = $("#register-password-input-field").val();
	var confirmPassword = $("#confirm-password-input-field").val();
	var email = $("#email-input-field").val();
	if (password == confirmPassword) {
		var msg = "{\"username\": \"" + username + "\", \"password\": \"" + password + "\", \"email\": \"" + email + "\"}";
		send("register.php", msg, registerRecieveCallback);
	}
	else {
		alert("Passwords does not match");
	}
};
