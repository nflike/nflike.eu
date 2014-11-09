<?php 

function match_sort_distance($a, $b) {
	if ($a['distance'] > $b['distance']) {
		return 1;
	}
	if ($a['distance'] < $b['distance']) {
		return -1;
	}
	return 0;
}

function match_sort_age($a, $b) {
	if ($a['agedifference'] > $b['agedifference']) {
		return 1;
	}
	if ($a['agedifference'] < $b['agedifference']) {
		return -1;
	}
	return 0;
}

function match_sort_gender($a, $b) {
	if ($a['gender'] == 3 && $b['gender'] == 3) {
		return 0;
	}
	if ($a['gender'] == 3) {
		return 1;
	}
	if ($b['gender'] == 3) {
		return -1;
	}
	return 0;
}

// Returns list(array $matches, array $justoutside)
// TODO: Decide whether we need to take the other user's preferences in account
function match_filter_distance($matchlist, $maxdistance) {
	if ($maxdistance == 0) {
		return [$matchlist, []]; // If we have no preference, we can't do anything here.
	}

	$results = [];
	$justoutside = [];
	foreach ($matchlist as $match) {
		if ($match['distance'] < $maxdistance) {
			$results[] = $match;
		}
		else if ($match['distance'] < $maxdistance * 1.1) {
			$justoutside[] = $match;
		}
	}
	return [$results, $justoutside];
}

// match_filter_gender($matchlist, $who is our user looking for, $what's our user's gender)
// Returns array $matches
function match_filter_gender($matchlist, $lookingfor, $gender) {
	// First decide whether we meet the other person's desire
	$results = [];
	foreach ($matchlist as $match) {
		// Is our gender what they are looking for, are they looking for 'any' or did they not fill it in yet?
		if ($match['lookingfor'] == 0 || $match['lookingfor'] == $gender || $match['lookingfor'] == 3) {
			$results[] = $match;
		}
	}

	// Now, are we even looking for anyone in particular?
	if ($lookingfor == 3 || $lookingfor == 0) {
		return $results;
	}
	
	// Yep we are. Match 'em up!
	$matchlist = $results;
	$results = [];
	foreach ($matchlist as $match) {
		// Is their gender what we are looking for or other?
		if ($lookingfor == $match['gender'] || $match['gender'] == 3) {
			$results[] = $match;
		}
	}
	return $results;
}

// Returns list(array $matches, array $justoutside)
// TODO: Decide whether we need to take the other user's preferences in account
// TODO: Decide whether it's a good idea to include ageless people in the justoutside list.
function match_filter_age($matchlist, $yobfrom, $yobto) {
	if ($yobfrom == 0 && $yobto == 0) {
		return [$matchlist, []]; // If we don't know what we are looking for, we can't look.
	}

	$results = [];
	$justoutside = [];
	foreach ($matchlist as $match) {
		if ($match['yob'] != 0 && $match['yob'] >= $yobfrom && $match['yob'] <= $yobto) {
			$results[] = $match;
		}
		else if ($match['yob'] == 0 || ($match['yob'] >= $yobfrom - 1 && $match['yob'] <= $yobto + 2)) {
			$justoutside[] = $match;
		}
	}
	return [$results, $justoutside];
}

function match_internal_usersresult2array($result, $ouruser) {
	$users = [];
	while ($row = $result->fetch_array()) {
		$row['password'] = '';

		if ($ouruser['yob'] == 0 || $row['yob'] == 0) {
			$row['agedifference'] = false;
		}
		else {
			$row['agedifference'] = abs($ouruser['yob'] - $row['yob']);
		}

		if (($ouruser['latitude'] == 0 && $ouruser['longitude'] == 0) || ($row['latitude'] == 0 && $row['longitude'] == 0)) {
			$row['distance'] = false;
		}
		else {
			$row['distance'] = coordToKmDistance($ouruser['latitude'], $ouruser['longitude'], $row['latitude'], $row['longitude']);
		}

		$users[] = $row;
	}
	return $users;
}

function match_db_getouruser() {
	global $db;

	$foruser = $db->query('SELECT * FROM users WHERE id = ' . $_SESSION['userid']) or die('Database error 53184');
	if ($foruser->num_rows != 1) {
		session_destroy();
		die('Security error 109. Please log in again.');
	}
	$foruser = $foruser->fetch_array();
	$foruser['password'] = '';
	return $foruser;
}

function match_db_getusers($ouruser) {
	global $db;

	$result = $db->query('SELECT * FROM users WHERE id NOT IN (SELECT hidden FROM hideresults WHERE userid = ' . $_SESSION['userid'] . ') AND available = 1 AND id != ' . $_SESSION['userid']) or die('Database error 14089');

	return match_internal_usersresult2array($result, $ouruser);
}

function match_db_gethiddenusers($ouruser) {
	global $db;

	$result = $db->query('SELECT * FROM users WHERE id IN (SELECT hidden FROM hideresults WHERE userid = ' . $_SESSION['userid'] . ') AND available = 1 AND id != ' . $_SESSION['userid']) or die('Database error 78198');
	return match_internal_usersresult2array($result, $ouruser);
}

