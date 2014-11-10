<?php 
	require('includes/header.php');
?>
<b>Hello <?php echo $_SESSION['username']; ?>!</b><br/>
<br/>
<?php 
	$yob = '';

	if (isset($_POST['gender'])) {
		if ($_POST['newpwd1'] != $_POST['newpwd2']) {
			echo '<span class=error>Passwords do not match.</span><br/><br/>';
		}
		else {
			$gender = max(0, min(3, intval($_POST['gender'])));
			$lookingfor = max(0, min(3, intval($_POST['lookingfor'])));
			$yob = max(0, min(date('Y'), intval($_POST['yob'])));
			if ($yob < 1850) {
				$yob = 0;
			}
			if (!empty($_POST['newpwd1'])) {
				$pwd = hashpwd($_POST['newpwd1']);
			}
			else {
				$pwd = hashpwd($_POST['oldpwd']);
			}

			$db->query('UPDATE users SET gender = ' . $gender .', lookingfor = ' . $lookingfor . ', `password` = "' . $pwd . '", yob = ' . $yob . ' '
				. 'WHERE id = ' . $_SESSION['userid'] . ' AND `password` = "' . hashpwd($_POST['oldpwd']) . '"') or die('Database error 7159. Sorry about that :(. Perhaps trying again works?');

			header('Location: ' . PATH . 'changeprofile');
			exit;
		}
	}
	
	$gender = mkSelect('gender', $_POST['gender'], true, [0 => 'Select', 1 => 'Male', 2 => 'Female', 3 => 'Other']);
	$lookingfor = mkSelect('lookingfor', $_POST['lookingfor'], true, [0 => 'Select', 1 => 'Males', 2 => 'Females', 3 => 'Any']);
?>
<form method=POST action="<?php echo PATH; ?>?firstuse">
	Welcome to this website! We will match you to other Nerdfighters, but to do that we need three things:
	<ol>
		<li>What is your gender? <?php echo $gender; ?></li>
		<li>Who are you looking for? <?php echo $lookingfor; ?></li>
		<li>What year were you born? <input type=number value="<?php echo $yob; ?>" maxlength=4 size=4 name=yob placeholder="19.."></li>
	</ol>
	<br/>

	It's also a good idea to change your password (but it is optional).<br/>
	Note that your password is stored in encrypted form ("hashed"), nobody cannot read it. If you ever loose it, we can reset it, but never tell you your old password.<br/><br/>

	<?php 
		if (isset($_GET['pwd']) || isset($_POST['oldpwd'])) {
			echo '<input type=hidden name=oldpwd value="' . htmlspecialchars($_GET['pwd'] . $_POST['oldpwd']) . '"/>';
		}
		else {
			echo 'Old password: <input typpe=password name=oldpwd><br/>';
		}
	?>
	New password: <input type=password name=newpwd1><br/>
	Repeat to verify: <input type=password name=newpwd2><br/><br/>

	There are more settings on the profile page. It is recommended to select what age range you are looking for and set your approximate location so we can estimate distances!<br/><br/>

	<input type=submit value=Continue>
</form>
<?php 
	require('includes/footer.php');

