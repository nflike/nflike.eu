<?php 
require('config.php');

if ($_GET['page'] == 'login') {
	require('includes/login.php');
	exit;
}

if ($_GET['autologin']) {
	if (checkLogin($_GET['autologin'], $_GET['pwd'])) {
		require('includes/firstlogin.php');
		exit;
	}
	else {
		die('Your username or password were not correct. Did you copy the whole URL? Alternatively, try logging in <a href="' . PATH . 'login">here</a>.');
	}
}

require('includes/checklogin.php');

if (isset($_GET['firstuse'])) {
	require('includes/firstlogin.php');
	exit;
}

switch ($_GET['page']) {
	case 'admin':
		if ($_SESSION['isadmin'] !== true) {
			die('Unauthorized');
		}
		require('includes/admin.php');
		break;

	case 'changelogin':
		require('includes/changelogin.php');
		break;

	case 'changeprofile':
		require('includes/changeprofile.php');
		break;

	case 'msgmods':
		require('includes/msgmods.php');
		break;

	case 'async':
		checkCSRF();
		require('includes/async.php');
		break;

	case 'logout':
		session_destroy();
		header('Location: ' . PATH);
		break;

	case 'search':
	default:
		require('includes/matchfunctions.php');
		require('includes/search.php');
}

