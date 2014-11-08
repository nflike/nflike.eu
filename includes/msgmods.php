<?php 
	if (isset($_POST['message'])) {
		checkCSRF();
		mail('nflike-msgform@lucb1e.com', 'NFL-MSGFORM Notification', ':)', 'From: site-automailer@lucb1e.com') or die('Failed to send your message (error 1094) :(');
		$db->query('INSERT INTO adminmsgs VALUES(' . $_SESSION['userid'] . ', ' . time() . ', "' . $db->escape_string($_POST['message']) . '")') or die('Failed to send your message (error 8419) :(');
		echo '<span class=success>Your message has been sent.</span> <a href="' . PATH . '">Return to site?</a><br/><br/>';
	}
?>
<b>Message the admins</b><br/>
Have a question about the site? Want to report a bug? Report a profile? You can do that here.<br/>
<br/>
Note: If you want us to reply, let us know how to contact you. (Via Facebook is an option, email could also work.)<br/>
<br/>
<form method=POST action="<?php echo PATH; ?>msgmods">
	<?php echoCSRFToken(); ?>
	Your message:<br/>
	<textarea name=message cols=80 rows=6 maxlength=61000></textarea><br/>
	<br/>
	<input type=submit value=Send> <input type=button value=Cancel onclick='location="<?php echo PATH; ?>";'>
</form>

