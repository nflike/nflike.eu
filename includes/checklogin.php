<?php 
if ($_SESSION['loggedin'] !== true) {
	header('Location: ' . PATH . 'login');
	exit;
}

$db->query('UPDATE users SET lastonline = ' . time() . ' WHERE id = ' . $_SESSION['userid']) or die('Database error 519843');

