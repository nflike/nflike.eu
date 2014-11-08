<?php 
	if ($_SESSION['loggedin'] === true) {
		header('Location: ' . PATH);
		exit;
	}

	$pagetitle = 'Login';
	require('includes/header.php');

	if (isset($_POST['name'])) {
		$username = $db->escape_string($_POST['name']);
		$password = hashpwd($_POST['pass']);

		$result = $db->query('SELECT id, isadmin, username, `name` FROM users WHERE username = "' . $username . '" AND `password` = "' . $password . '"') or die('Epic Database Fail :( I\'m sorry about this, please try again.');

		if ($result->num_rows == 1) {
			$row = $result->fetch_row();
			$_SESSION['userid'] = $row[0];
			$_SESSION['loggedin'] = true;
			$_SESSION['csrf'] = hash('sha256', openssl_random_pseudo_bytes(7));
			$_SESSION['isadmin'] = $row[1] == 1 ? true : false;
			$_SESSION['username'] = $row[2];
			$_SESSION['name'] = $row[3];
			header('Location: ' . PATH);
			exit;
		}
		else {
			echo "<span class=error>Invalid login</span>";
		}
	}
?>
<h3>This site is in beta.</h3>

<form method=POST action="<?php echo PATH; ?>login">
	Username: <input name=name><br/>
	Password: <input name=pass type=password><br/>
	<input type=submit value=Login>
</form>

<br/><br/>

If you forgot your username or password, contact one of the group admins.

<br/><br/>

Note that this site is invite-only; there is no registration process.
<?php 
	require('includes/footer.php');

