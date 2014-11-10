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
	$userinfo = match_db_getouruser();

	if ($userinfo['available'] != '1') {
		echo '<b>Warning:</b> You are set to unavailable, others cannot find you. You can change this in your <a href="' . PATH . 'changeprofile">profile settings</a>.<br/><br/>';
	}
	else if ($userinfo['gender'] == 0) {
		echo '<b>Warning:</b> You did not specify your gender yet, others may not be able to find you. You can set this in your <a href="' . PATH . 'changeprofile">profile settings</a>.<br/><br/>';
	}
	else if ($userinfo['yob'] == 0) {
		echo '<b>Warning:</b> You did not specify your year of birth yet, others may not be able to find you. You can set this in your <a href="' . PATH . 'changeprofile">profile settings</a>.<br/><br/>';
	}
	else if ($userinfo['lookingfor'] == 0) {
		echo 'Note: You did not specify whether you are looking for men, women or both. Showing all results. You can set this in your <a href="' . PATH . 'changeprofile">profile settings</a>.<br/><br/>';
	}

	if ($userinfo['interestarea'] > 0) {
		echo 'Note: Showing results up to ' . $userinfo['interestarea'] . 'km away. You can change this in your <a href="' . PATH . 'changeprofile">profile settings</a>.<br/><br/>';
	}

	$users = match_db_getusers($userinfo);
	list($users, $justoutside) = match_filter_distance($users, $userinfo['interestarea']);
	list($users, $justoutside) = match_filter_age($users, $userinfo['yobfrom'], $userinfo['yobto']);
	$users = match_filter_gender($users, $userinfo['lookingfor'], $userinfo['gender']);

	list($justoutside, $justoutside2) = match_filter_age($justoutside, $userinfo['yobfrom'], $userinfo['yobto']);
	$justoutside = array_merge($justoutside, $justoutside2);
	unset($justoutside2);
	$justoutside = match_filter_gender($justoutside, $userinfo['lookingfor'], $userinfo['gender']);

	shuffle($users);
	usort($users, "match_sort_distance");
	usort($users, "match_sort_age");
	usort($users, "match_sort_gender");
	
	shuffle($justoutside);
	usort($justoutside, "match_sort_distance");
	usort($justoutside, "match_sort_age");
	usort($justoutside, "match_sort_gender");

	if (count($users) > 0) {
		$first = true;
		foreach ($users as $row) {
			if ($first) {
				$first = false;
				echo '<b>A selection of awesome Nerdfighters for you:</b><br/>';
			}
			showProfile($row);
		}
	}
	if (!isset($showProfileCounter)) {
		echo '<b>We could not find any available Nerdfighters that match your requirements (or whose requirements you meet).</b><br/><br/>';
	}
	else {
		echo '<br/>';
	}

	if (count($justoutside) > 0) {
		echo '<b>Other users</b> (they are close to what you are looking for)<br/>';
		foreach ($justoutside as $row) {
			showProfile($row, false);
		}
		echo '<br/>';
	}

	$hiddenusers = match_db_gethiddenusers($userinfo);
	if (count($hiddenusers) > 0) {
		echo '<b>Hidden users</b> (you previously hid them)<br/>';
		foreach ($hiddenusers as $row) {
			showProfile($row, false, true);
		}
	}

	function showProfile($row, $canhide = true, $hidden = false) {
		global $showProfileCounter;
		if (!isset($showProfileCounter)) {
			$showProfileCounter = -1;
		}
		$showProfileCounter++;

		$distance = $row['distance'];
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
				$fulltext = '<a href="javascript:showText(' . $showProfileCounter . ');">... more</a>.';
			}
			$freetext = ' <span style="display:none;" id=freetext' . $showProfileCounter . '><i>' . htmlspecialchars($row['freetext']) . '</i></span><span id=shorttext' . $showProfileCounter . '>' . $short . $fulltext . '</span> ';
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

