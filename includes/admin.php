<?php 
	if (isset($_POST['createusername'])) {
		checkCSRF();
		$displaypwd = pwGen();
		$password = hashpwd($displaypwd);
		$username = $db->escape_string($_POST['createusername']);
		$fburl = $db->escape_string($_POST['fburl']);

		$result = $db->query('INSERT INTO users (username, `password`, fburl) VALUES("' . $username . '", "' . $password . '", "' . $fburl . '")') or die('Database query error 89123. Does the username already exist?');
		echo '<span class=success>Created. Password: ' . $displaypwd . '</span><br/><br/>';
	}

	if (isset($_POST['resetpassword'])) {
		checkCSRF();
		$fburl = $db->escape_string($_POST['resetpassword']);
		$result = $db->query('SELECT id FROM users WHERE fburl = "' . $fburl . '"') or die('Database query error 38915');
		if ($result->num_rows != 1) {
			echo '<span class=error>Profile not found.</span><br/><br/>';
		}
		else {
			$row = $result->fetch_row();
			$displaypwd = pwGen();
			$password = hashpwd($displaypwd);
			$db->query('UPDATE users SET `password` = "' . $password . '" WHERE id = ' . $row[0]) or die('Database query error 85178');
			echo '<span class=success>Reset succesful. Password: ' . $displaypwd . '</span><br/><br/>';
		}
	}

	$pagetitle = 'Admin - NFLike';
	require('includes/header.php');
?>
<input type=button value='Return to site' onclick='location="<?php echo PATH; ?>";'><br/><br/>

<form method=POST action="<?php echo PATH; ?>admin">
	<?php echoCSRFToken(); ?>
	<b>Create a new account</b><br/>
	Username: <input name=createusername><br/>
	Facebook profile: <input name=fburl size=40 value="https://www.facebook.com/..." onfocus="value = value.replace('...', '');"><br/>
	<input type=submit value=Create>
</form>

<br/><br/>

<form method=POST action="<?php echo PATH; ?>admin">
	<?php echoCSRFToken(); ?>
	<b>Password reset</b><br/>
	This will immediately reset the password. The user will not be able to log in anymore until you give them their new password.<br/>
	Facebook profile: <input name=resetpassword size=40 value="https://www.facebook.com/..." onfocus="value = value.replace('...', '');"><br/>
	<input type=submit value="Get new password">
</form>

<br/><br/>

<b>Messages to the admins:</b><br/>
<?php 
	$result = $db->query('SELECT am.id, name, username, userid, `datetime`, message FROM adminmsgs am INNER JOIN users u on u.id = am.userid ORDER BY id DESC LIMIT 25') or die('Database error 134859');
	if ($result->num_rows == 0) {
		echo 'None.';
	}
	else {
		while ($row = $result->fetch_array()) {
			echo 'From ' . htmlspecialchars($row['name']) . ' (loginname: ' . htmlspecialchars($row['username']) . ') at ' . date('r', $row['datetime']) . ':<br/>'
				. '<i>' . nl2br($row['message']) . '</i><br/><hr/><br/>';
		}
	}

	require('footer.php');

