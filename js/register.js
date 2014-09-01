function recieveCallback (data) {
	alert(data.data)
};

function register(){
	var username = $("#register-username-input-field").val();
	var password = $("#register-password-input-field").val();
	var confirmPassword = $("#confirm-password-input-field").val();
	var email = $("#email-input-field").val();
	if (password == confirmPassword) {
		send("register.php", "{username: " + username + ", password: " + password + ", email: " + email + "}", recieveCallback);
	}
	else {
		alert("Passwords does not match");
	}
};
