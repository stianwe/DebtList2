
function send(page, data, success) {
	$.ajax({
		type		:	"GET",
		url			:	"http://192.168.0.111/php/" + page,
		data		:	"data=" + data,
		dataType	:	"jsonp",
		jsonp		:	"callback",
		crossDomain	:	true,
		success		:	success,
		error		:	function(a, b, c) { alert("ERROR! " + a + " " + b + " " + c); }
	});
}