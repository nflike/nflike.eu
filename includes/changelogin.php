<?php 
	$pagetitle = 'Reset password - NerdFighterLike';
	require('header.php');

	if (!empty($_POST['username'])) {
		checkCSRF();
		if (empty($_POST['username'])) {
			$_POST['username'] = $_SESSION['username'];
		}
		if ($_POST['newpwd1'] != $_POST['newpwd2']) {
			echo '<span class=error>Password do not match. Try again please.</span><br/><br/>';
		}
		$username = $db->escape_string($_POST['username']);
		$oldusername = $db->escape_string($_SESSION['username']);
		$password = hashpwd($_POST['newpwd1']);
		$oldpassword = hashpwd($_POST['oldpwd']);
		$db->query('UPDATE users SET username = "' . $username . '", `password` = "' . $password . '" WHERE username = "' . $oldusername . '" AND `password` = "' . $oldpassword . '"') or die('Database failed :( Sorry about that - try again! (Or perhaps that username is taken?)');
		if ($db->affected_rows == 1) {
			$_SESSION['username'] = $_POST['username'];
			echo '<span class=success>Password and/or username changed.</span> <a href="' . PATH . '">Return to main page.</a><br/><br/>';
		}
		else {
			echo '<span class=error>Invalid old password.</span><br/><br/>';
		}
	}
?>

<form method=POST action='<?php echo PATH; ?>changelogin'>
	<?php echoCSRFToken(); ?>
	<b>Reset password</b><br/>
	Old password: <input type=password name=oldpwd><br/>
	New username: <input name=username value="<?php echo htmlspecialchars($_SESSION['username']); ?>"><br/>
	New password: <input type=password name=newpwd1><br/>
	Repeat new password: <input type=password name=newpwd2><br/>
	<input type=submit value=Change> <input type=button value=Cancel onclick='location="<?php echo PATH; ?>";'>
</form>

<?php 
	require('footer.php');

