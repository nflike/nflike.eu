<?php 
require('config.php');

if ($_GET['page'] == 'login') {
	require('includes/login.php');
	exit;
}

require('includes/checklogin.php');

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
		require('includes/search.php');
}

