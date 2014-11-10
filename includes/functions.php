<?php 
function pwGen() {
	$data = base64_encode(openssl_random_pseudo_bytes(10));
	$out = '';
	for ($i = 0; $i < strlen($data); $i++) {
		if ((ord($data[$i]) >= ord('a')
		&& ord($data[$i]) <= ord('z'))
		|| (ord($data[$i]) >= ord('A')
		&& ord($data[$i]) <= ord('Z'))
		|| (ord($data[$i]) >= ord('0')
		&& ord($data[$i]) <= ord('9'))) {
			$out .= $data[$i];
		}
	}
	if (strlen($out) < 7) {
		return pwGen();
	}
	return $out;
}

function hashpwd($password) {
	return '0' . hash('sha256', hash('sha256', $password . $password . 'ahyob5u71o3i4j', true) . 'b76A2');
}

function checkLogin($username, $password) {
	global $db;

	$username = $db->escape_string($username);
	$password = hashpwd($password);

	$result = $db->query('SELECT id, isadmin, username, `name` FROM users WHERE username = "' . $username . '" AND `password` = "' . $password . '"') or die('Epic Database Fail :( I\'m sorry about this, please try again.');

	if ($result->num_rows == 1) {
		$row = $result->fetch_row();
		$_SESSION['userid'] = $row[0];
		$_SESSION['loggedin'] = true;
		$_SESSION['csrf'] = hash('sha256', openssl_random_pseudo_bytes(7));
		$_SESSION['isadmin'] = $row[1] == 1 ? true : false;
		$_SESSION['username'] = $row[2];
		$_SESSION['name'] = $row[3];
		return true;
	}
	return false;
}

function checkCSRF($token = -1) {
	if ($token === -1) {
		$token = $_POST['csrf'];
	}

	if (hash('sha256', $token) != hash('sha256', $_SESSION['csrf'])) {
		header('HTTP/1.1 403 Forbidden');
		die('Security error 284');
	}
}

function echoCSRFToken() {
	echo '<input type=hidden name=csrf value="' . $_SESSION['csrf'] . '"/>';
}

// mkSelect("gender", $databasevalue[gender], true, array(0 => "male", 1 => "female"));
// mkSelect("gender", $databasevalue[gender], false, array("male", "female"));
function mkSelect($name, $selectedvalue, $usevalues, $list) {
	$html = '<select name="' . htmlspecialchars($name) . '">';
	foreach ($list as $value=>$text) {
		$selected = '';
		if ($value == $selectedvalue) {
			$selected = ' selected="selected"';
		}
		if ($usevalues) {
			$html .= '<option value="' . htmlspecialchars($value) . '"' . $selected . '>';
		}
		else {
			$html .= '<option>';
		}
		$html .= htmlspecialchars($text) . '</option>';
	}
	$html .= '</select>';
	return $html;
}

function coordToKmDistance($lat1, $lon1, $lat2, $lon2) {
	// Adapted from http://stackoverflow.com/a/365853
	$R = 6372; // km
	$dLat = deg2rad($lat2-$lat1);
	$dLon = deg2rad($lon2-$lon1);
	$lat1 = deg2rad($lat1);
	$lat2 = deg2rad($lat2);

	$a = sin($dLat/2) * sin($dLat/2) + sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2); 
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
	return $R * $c;
}

