<?php 
if ($_SESSION['loggedin'] !== true) {
	header('Location: ' . PATH . 'login');
	exit;
}

