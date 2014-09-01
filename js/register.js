function recieveCallback (data) {
	alert(data.data)
};

function register(){
	var username = $("#register-username-input-field").val();
	var password = $("#register-password-input-field").val();
	var confirmPassword = $("#confirm-password-input-field").val();
	var email = $("#email-input-field").val();
	if (password == confirmPassword) {
		var msg = "{\"username\": \"" + username + "\", \"password\": \"" + password + "\", \"email\": \"" + email + "\"}";
		console.log("Sending:", msg);
		send("register.php", msg, recieveCallback);
	}
	else {
		alert("Passwords does not match");
	}
};
