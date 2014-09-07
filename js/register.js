function registerRecieveCallback (data) {
	alert("Register " + (data.id != -1 ? "OK" : "failed") + ".");
	if (data.id != -1) {
		$.mobile.changePage("#front-page", { transition: "flow", changeHash: true });
		console.log("Registered user with id", data.id);
		window.userId = data.id;
	}
};

function register() {
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
