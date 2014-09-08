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
		var username = $("#username-input-field").val();
		var password = $("#password-input-field").val();
		send("login.php", "{\"username\": \"" + username + "\", \"password\": \"" + password + "\"}", loginRecieveCallback);
	};
