var OUTGOING_DEBT_COLOR = "#2ECC71";
var INCOMING_DEBT_COLOR = "#E74C3C";

function loadDebtsCallback(data) {
	console.log("friends:", data.debts);
	fillDebtList(data.debts);
};

function loadDebts() {
	sendWithCredentials("load_debts.php", "{}", loadDebtsCallback);
};

function createDebtListElement(debt) {
	return "<li style=\"background-color:" + (debt.toUser == window.userId ? INCOMING_DEBT_COLOR : OUTGOING_DEBT_COLOR) +
		   ";color:white\"><div class=\"ui-grid-a\"><div class=\"ui-block-a\">" + debt.otherUsername + "</div>" +
		   "<div align=\"right\" class=\"ui-block-c\">" + debt.amount + " " + debt.what + "</div></li>";
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
	//$("#front-page-debt-list").empty();
	var accepted = [];
	var incoming = [];
	var outgoing = [];
	var completedByUser = [];
	var completedByOther = [];
	for (var i = 0; i < debts.length; i++) {
		var debt = debts[i];
		var debtListItem = createDebtListElement(debt);
		switch (parseInt(debt.status)) {
			case 0: // Requested
				if (debt.requestingUser == window.userId) {
					outgoing.push(debtListItem);
				} else {
					incoming.push(debtListItem);
				}
				break;
			case 1: // Accepted
				accepted.push(debtListItem);
				break;
			case 2: // Declined
				// Do not display declined debts for now
				break;
			case 3: // Completed by requesting user
				completedByUser.push(debtListItem);
				break;
			case 4: // Completed by other user
				completedByOther.push(debtListItem);
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