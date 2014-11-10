<?php 
	if ($_SESSION['loggedin'] === true) {
		header('Location: ' . PATH);
		exit;
	}

	$pagetitle = 'Login';
	require('includes/header.php');

	if (isset($_POST['name'])) {
		if (checkLogin($_POST['name'], $_POST['pass'])) {
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

