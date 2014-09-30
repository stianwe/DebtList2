var OUTGOING_DEBT_COLOR = "#2ECC71";
var INCOMING_DEBT_COLOR = "#E74C3C";

function loadDebtsCallback(data) {
	console.log("Debts:", data.debts);
	fillDebtList(data.debts);
};

function loadDebts() {
	sendWithCredentials("load_debts.php", "{}", loadDebtsCallback);
};

function createDebtListElement(debt, buttonCreator) {
	return "<li style=\"background-color:" + (debt.toUser == window.userId ? INCOMING_DEBT_COLOR : OUTGOING_DEBT_COLOR) +
		   ";color:white\">" + 
		   "<div class=\"ui-grid-d\"><div class=\"ui-block-a\">" + debt.otherUsername + "</div>" +
		   "<div class=\"ui-block-b\"></div><div class=\"ui-block-c\"></div>" +
		   "<div align=\"right\" class=\"ui-block-d\">" + debt.amount + " " + debt.what + "</div>" + 
		   "<div align=\"right\" class=\"ui-block-e\">"+
		   (buttonCreator ? buttonCreator(debt) : "") + 
		   "</div></div>" + 
		   "<br />" + 
		   "<div style=\"text-decoration:none; font-weight:normal; font-style:italic;\">" + debt.comment + "</div>" + 
		   "</li>";
};

function completeButtonCreator(debt) {
	var status = parseInt(debt.requestingUser) == window.userId ? 3 : 4;
	return "<div onclick='respondToDebt(" + debt.id + ", " + status + ");' class='ui-icon ui-icon-check' style='color:black;float:right'></div>";
};

function incomingRequestButtonsCreator(debt) {
	return "<div onclick='respondToDebt(" + debt.id + ", " + 2 + ");' class='ui-icon ui-icon-delete' style='color:black;float:right'></div><div onclick='respondToDebt(" + debt.id + ", " + 1 + ");' class='ui-icon ui-icon-check' style='color:black;float:right'></div>";
};

function fillDebtListHelper(list, dividerText) {
	if (list.length > 0) {
		$("#front-page-debt-list").append("<li data-role=\"list-divider\" data-theme=\"f\">" + dividerText + "</li>");
	}
	list.forEach(function (debtListItem) {
		$("#front-page-debt-list").append(debtListItem);
	});
};

function fillDebtList(debts) {
	$("#front-page-debt-list").empty();
	var accepted = [];
	var incoming = [];
	var outgoing = [];
	var completedByUser = [];
	var completedByOther = [];
	for (var i = 0; i < debts.length; i++) {
		var debt = debts[i];
		switch (parseInt(debt.status)) {
			case 0: // Requested
				if (debt.requestingUser == window.userId) {
					outgoing.push(createDebtListElement(debt));
				} else {
					incoming.push(createDebtListElement(debt, incomingRequestButtonsCreator));
				}
				break;
			case 1: // Accepted
				accepted.push(createDebtListElement(debt, completeButtonCreator));
				break;
			case 2: // Declined
				// Do not display declined debts for now
				break;
			case 3: // Completed by requesting user
				// Check if this user is the requesting user or not
				if (parseInt(debt.requestingUser) == window.userId) {
					completedByUser.push(createDebtListElement(debt));
				} else {
					completedByOther.push(createDebtListElement(debt, completeButtonCreator));
				}
				break;
			case 4: // Completed by other user
				// Check if this user is the requesting user or not
				if (parseInt(debt.requestingUser) == window.userId) {
					completedByOther.push(createDebtListElement(debt, completeButtonCreator));
				} else {
					completedByUser.push(createDebtListElement(debt));
				}
				break;
			case 5: // Completed
				// We do not show debts that has been completed by both users for now
				break;
			default:
				console.log("Debt with unexpected status detected: ", debt);
		};
	}
	fillDebtListHelper(incoming, "Incoming debt requests:");
	fillDebtListHelper(accepted, "Accepted debts:");
	fillDebtListHelper(completedByOther, "Marked as completed by friend:");
	fillDebtListHelper(completedByUser, "Marked as completed by you:");
	fillDebtListHelper(outgoing, "Outgoing debt requests:");
	$("#front-page-debt-list").listview("refresh");
};

function respondToDebtCallback(data) {
	if (data.response < 0) {
		alert("An error occurred! Code: " + data.response);
	} else {
		$.getScript("js/utils.js", function() {
			showDebts(false, true);
		});
	}
};

function respondToDebt(debtId, response) {
	sendWithCredentials("debt_response.php", "{\"debtId\": \"" + debtId + "\", \"newStatus\": \"" + response + "\"}", respondToDebtCallback);
};

function initAddDebt() {
	// Fill the select (dropdown) with friends
	loadFriends(function (friends) {
		friends.forEach(function (friend) {
			if (parseInt(friend.status) == 1) {
				$("#add-debt-friend").append("<option value=\"" + friend.id + "\">" + friend.username + "</option>");
			}
		});
	});
};

function addDebtCallback(data) {
	console.log("DATA: ", data);
};

function addDebt() {
	var friendId = $("#add-debt-friend").val();
	if (friendId == "") {
		alert("Please select a friend.");
		return;
	}
	console.log("FriendId=", friendId);
	var toUserFromOther;
	if ($("#radio-to-user").prop("checked")) {
		toUserFromOther = true;
	} else if ($("#radio-to-other").prop("checked")) {
		toUserFromOther = false;
	} else {
		alert("Please select who should receive the payment.")
		return;
	}
	var amount = $("#add-debt-amount").val();
	if (amount == "") {
		alert("Please enter a number as amount.");
		return;
	}
	var what = $("#add-debt-what").val();
	if (what == "") {
		alert("Please enter something in the what-field, for instance \"$\", \"beer\" or \"burger\"");
	}
	var comment = $("#add-debt-comment").val();
	sendWithCredentials("create_debt.php", '{"amount": "' + amount + '", "what": "' + what + '", "comment": "' + comment + 
		'", "fromUser": "' + (toUserFromOther ? friendId : window.userId) + '", "toUser": "' + (toUserFromOther ? window.userId : friendId) + '"}',
		addDebtCallback);
};