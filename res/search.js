function showText(divid) {
	document.getElementById('freetext' + divid).style.display = 'inline';
	document.getElementById('shorttext' + divid).style.display = 'none';
}

function hideuser(id) {
	aPOST(PATH + 'async', 'action=hideuser&csrf=' + SECRET_SECURITY_TOKEN + '&id=' + id, function() { location.reload(); }, function() { alert("Error hiding the user :(. Try again?"); });
}

function unhideuser(id) {
	aPOST(PATH + 'async', 'action=unhideuser&csrf=' + SECRET_SECURITY_TOKEN + '&id=' + id, function() { location.reload(); }, function() { alert("Error hiding the user :(. Try again?"); });
}

function aPOST(uri, data, callback, errorcallback) {
	var req = new XMLHttpRequest();
	req.open("POST", uri, true);
	req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	req.setRequestHeader('Content-Length', data.length);
	req.setRequestHeader('Connection', 'close');
	req.send(data);
	req.onreadystatechange = function() {
		if (req.readyState == 4) {
			callback(req.responseText);
		}
		else if (req.readyState == 5) {
			errorcallback(req.responseText);
		}
	}
}

