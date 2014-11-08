<?php 
	$pagetitle = 'NerdfighterLike';
	require('includes/header.php');

	$name = empty($_SESSION['name']) ? $_SESSION['username'] : $_SESSION['name'];
?>

Hi <?php echo htmlspecialchars($name); ?>!<br/>
	<a href='<?php echo PATH; ?>changeprofile'>Change profile</a> |
	<a href='<?php echo PATH; ?>changelogin'>Change login</a> |
	<a href='<?php echo PATH; ?>msgmods'>Message the admins</a> |
	<?php if ($_SESSION['isadmin'] === true) { ?>
		<a href='<?php echo PATH; ?>admin'>Admin panel</a> |
	<?php } ?>
	<a href='<?php echo PATH; ?>logout'>Log out</a>
<br/>
<br/>

<?php 
	$userinfo = $db->query('SELECT interestarea, available, lookingfor, gender, latitude, longitude, agefrom, ageto FROM users WHERE id = ' . $_SESSION['userid']) or die('Database error 74857. Please try again.');
	if ($userinfo->num_rows != 1) {
		die('Something weird happened. You have been logged out for security reasons. Please <a href="' . PATH . 'login">log in</a> again.');
	}
	$userinfo = $userinfo->fetch_array();

	if (isset($_GET['maxdist'])) {
		$userinfo['interestarea'] = $_GET['maxdist'];
	}

	if ($userinfo['available'] != '1') {
		echo '<b>Warning:</b> You are set to unavailable, others cannot find you. You can change this in your <a href="' . PATH . 'changeprofile">profile settings</a>.<br/><br/>';
	}
	else if ($userinfo['gender'] == 0) {
		echo '<b>Warning:</b> You did not specify your gender yet, others may not be able to find you. You can set this in your <a href="' . PATH . 'changeprofile">profile settings</a>.<br/><br/>';
	}
	else if ($userinfo['lookingfor'] == 0) {
		echo 'Note: You did not specify whether you are looking for men, women or both. Showing all results. You can set this in your <a href="' . PATH . 'changeprofile">profile settings</a>.<br/><br/>';
	}

	if ($userinfo['interestarea'] > 0) {
		echo 'Note: Showing results up to ' . $userinfo['interestarea'] . 'km away. You can change this in your <a href="' . PATH . 'changeprofile">profile settings</a>.<br/><br/>';
	}

	$whereclause = '';
	if ($userinfo['lookingfor'] == 1) {
		$whereclause .= '(gender = 1 OR gender = 3) AND ';
	}
	if ($userinfo['lookingfor'] == 2) {
		$whereclause .= '(gender = 2 OR gender = 3) AND ';
	}

	if ($userinfo['agefrom'] > 0) {
		$whereclause .= '(yob <= ' . $userinfo['agefrom'] . ' + 2 AND yob != 0) AND ';
	}

	if ($userinfo['ageto'] > 0) {
		$whereclause .= '(yob >= ' . $userinfo['ageto'] . ' - 1 AND yob != 0) AND ';
	}

	if ($userinfo['yob'] > 0) {
		$whereclause .= '(' . $userinfo['yob'] . ' >= agefrom - 1 AND agefrom != 0) AND ';
		$whereclause .= '(' . $userinfo['yob'] . ' <= ageto + 2 AND ageto != 0) AND ';
	}

	switch ($userinfo['gender']) {
		case 1: // male
			$whereclause .= '(lookingfor = 0 OR lookingfor = 1 OR lookingfor = 3) AND ';
			break;
		case 2: // female
			$whereclause .= '(lookingfor = 0 OR lookingfor = 2 OR lookingfor = 3) AND ';
			break;
	}

	$whereclause .= 'id NOT IN (SELECT hidden FROM hideresults WHERE userid = ' . $_SESSION['userid'] . ') AND ';

	$result = $db->query('SELECT id, name, fburl, latitude, longitude, gender, yob, freetext '
		. 'FROM users WHERE ' . $whereclause . ' available = 1 AND id != ' . $_SESSION['userid'] . ' ORDER BY RAND()') or die('Database error 598128');
	if ($result->num_rows > 0) {
		$first = true;
		while ($row = $result->fetch_array()) {
			if (($row['latitude'] == 0 && $row['longitude'] == 0) || ($userinfo['latitude'] == 0 && $userinfo['longitude'] == 0)) {
				$distance = false;
			}
			else {
				$distance = coordToKmDistance($row['latitude'], $row['longitude'], $userinfo['latitude'], $userinfo['longitude']);
			}

			if ($row['yob'] > $userinfo['agefrom'] || $row['yob'] < $userinfo['ageto']) {
				$hiddenusers[] = [$row, $distance];
			}
			else if ($distance === false || ($userinfo['interestarea'] != 0 && $distance <= $userinfo['interestarea'])) {
				if ($first) {
					$first = false;
					echo '<b>A selection of awesome Nerdfighters for you:</b><br/>';
				}
				showProfile($row, $distance);
			}
			else {
				$hiddenusers[] = [$row, $distance];
			}
		}
	}
	if (!isset($showProfileCounter)) {
		echo '<b>We could not find any available Nerdfighters that match your requirements (or whose requirements you meet).</b><br/><br/>';
	}

	$result = $db->query('SELECT id, name, fburl, gender, yob, freetext FROM hideresults hr INNER JOIN users u ON u.id = hr.hidden WHERE hr.userid = ' . $_SESSION['userid']) or die('Database error 7184492');
	if ($result->num_rows > 0 || count($hiddenusers) > 0) {
		echo '<b>Hidden users</b> (you previously hid them, they are outside your distance limit or their age is just outside what you are looking for)<br/>';
		if (count($hiddenusers) > 0) {
			foreach ($hiddenusers as $i) {
				showProfile($i[0], $i[1], false);
			}
		}
		while ($row = $result->fetch_array()) {
			showProfile($row, false, false, true);
		}
	}

	function showProfile($row, $distance, $canhide = true, $hidden = false) {
		global $showProfileCounter;
		if (!isset($showProfileCounter)) {
			$showProfileCounter = -1;
		}
		$showProfileCounter++;

		if ($distance !== false) {
			$distance = round($distance);
			$distance = ", ~${distance}km";
		}
		else {
			$distance = '';
		}

		$unhide = '';
		if ($hidden) {
			$unhide = '<a href="javascript:unhideuser(' . $row['id'] . ');"><img src="' . PATH . 'res/img/restore.png" border="0" alt="Unhide user" title="Restore result"/></a> ';
		}

		$hide = '';
		if ($canhide) {
			$hide = '<a href="javascript:hideuser(' . $row['id'] . ');"><img src="' . PATH . 'res/img/delete.png" alt="Hide user" title="Hide this result (you can later restore it)" width=16 height=16 border="0"/></a> ';
		}

		$freetext = '';
		if (!empty($row['freetext'])) {
			$short = 'Personal text: <i>' . htmlspecialchars(substr($row['freetext'], 0, 120)) . '</i>';
			if (strlen($row['freetext']) > 120) {
				$fulltext = '<a href="javascript:showText(' . $showProfileCounter . ', \'<i>' . htmlspecialchars($row['freetext']) . '</i>\');">... more</a>.';
			}
			$freetext = ' <span id=freetext' . $showProfileCounter . '>' . $short . $fulltext . '</span> ';
		}

		$gender = ', ';
		switch ($row['gender']) {
			case 1:
				$gender .= 'male';
				break;
			case 2:
				$gender .= 'female';
				break;
			default:
				$gender = '';
		}

		$facebook = '<a href="' . htmlspecialchars($row['fburl']) . '"><img src="' . PATH . 'res/img/fb.png" height=18 width=18 alt="Facebook profile" title="Go to Facebook profile" border="0"/></a> ';

		echo htmlspecialchars($row['name']) . $gender . ', ' . (date('Y') - $row['yob']) . $distance . '. ' . $facebook . $hide . $unhide . $freetext . '<br/>';
	}

	echo '<script src="' . PATH . 'res/search.js"></script><script>SECRET_SECURITY_TOKEN="' . $_SESSION['csrf'] . '", PATH="' . PATH . '";</script>';

	require('includes/footer.php');

