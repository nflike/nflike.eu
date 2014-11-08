<?php 
switch ($_POST['action']) {
	case 'hideuser':
		$db->query('INSERT INTO hideresults VALUES(' . $_SESSION['userid'] . ', "' . intval($_POST['id']) . '")') or die('Database error 418948.');
		die('ok');
	
	case 'unhideuser':
		$db->query('DELETE FROM hideresults WHERE userid = ' . $_SESSION['userid'] . ' AND hidden = "' . intval($_POST['id']) . '"') or die('Database error 152752.');
		die('ok');
	
	default:
		header('HTTP/1.1 404 Not Found');
		die('The requested resource was not found.');
}

