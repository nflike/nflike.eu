<?php 
	$pagetitle = 'Change profile - NerdFighterLike';
	require('includes/header.php');

	if (isset($_POST['name'])) {
		$ok = true;
		if (strlen($_POST['name']) < 3) {
			echo '<span class=error>New name too short.</span>';
			$ok = false;
		}
		$_POST['yob'] = intval($_POST['yob']);
		if ($_POST['yob'] > date('Y') - 6) {
			echo '<span class=error>Your year of birth is too recent. Set it to 0 if you want to leave it blank.</span>';
			$ok = false;
		}
		if ($_POST['yob'] != 0 && $_POST['yob'] < 1870) {
			echo '<span class=error>Aren\'t you a little old for this? Or were you not born in the year ' . $_POST['yob'] . '?</span>';
			$ok = false;
		}
		if ($ok) {
			$name = $db->escape_string($_POST['name']);
			$freetext = $db->escape_string($_POST['freetext']);
			$gender = intval($_POST['gender']);
			$lookingfor = intval($_POST['lookingfor']);
			$longitude = floatval($_POST['longitude']);
			$latitude = floatval($_POST['latitude']);
			$interestarea = intval($_POST['interestarea']);
			$yob = intval($_POST['yob']);
			$agefrom = date('Y') - intval($_POST['agefrom']);
			$ageto = date('Y') - intval($_POST['ageto']);
			$available = isset($_POST['available']) ? 1 : 0;
			
			$query = 'UPDATE users SET ';
			$query .= 'name = "' . $name . '", ';
			$query .= 'gender = "' . $gender . '", ';
			$query .= 'lookingfor = "' . $lookingfor . '", ';
			$query .= 'latitude = "' . $latitude . '", ';
			$query .= 'longitude = "' . $longitude . '", ';
			$query .= 'interestarea = "' . $interestarea . '", ';
			$query .= 'available = "' . $available . '", ';
			$query .= 'freetext = "' . $freetext . '", ';
			$query .= 'yob = "' . $yob . '", ';
			$query .= 'agefrom = "' . $agefrom . '", ';
			$query .= 'ageto = "' . $ageto . '" ';
			$query .= 'WHERE id = ' . $_SESSION['userid'];
			$db->query($query) or die('Database error 518093. Please try that again.');
			echo '<span class=success>Updated!</span> <a href="' . PATH . '">Return to the main page?</a><br/><br/>';
			$_SESSION['name'] = $_POST['name'];
		}
	}

	$userinfo = $db->query('SELECT fburl, agefrom, ageto, yob, interestarea, available, freetext, gender, lookingfor, longitude, latitude '
		. 'FROM users WHERE id = ' . $_SESSION['userid']) or die('Database error 91041. Please try again.');
	if ($userinfo->num_rows != 1) {
		session_destroy();
		die('Critical error (the profile you are logged in with does not exist, you have been logged out). Please try logging in again.');
	}
	$userinfo = $userinfo->fetch_array();
	$displayname = htmlspecialchars($_SESSION['name']);
	$yob = intval($userinfo['yob']);
	$agefrom = date('Y') - intval($userinfo['agefrom']);
	$ageto = date('Y') - intval($userinfo['ageto']);
	if ($agefrom == date('Y')) {
		$agefrom = 0;
	}
	if ($ageto == date('Y')) {
		$ageto = 999;
	}
	$available = $userinfo['available'] == 1;
	$latitude = floatval($userinfo['latitude']);
	$longitude = floatval($userinfo['longitude']);
	$interestarea = intval($userinfo['interestarea']);
	$freetext = htmlspecialchars($userinfo['freetext']);
	$fburl = htmlspecialchars($userinfo['fburl']);
	$physicalfeatures = mkSelect('gender', $userinfo['gender'], true, [1 => "Male", 2 => "Female", 3 => "Other"]);
	$lookingfor = mkSelect('lookingfor', $userinfo['lookingfor'], true, [1 => "Males (and 'other')", 2 => "Females (and 'other')", 3 => "Any"]);

	if (!$available) {
		echo "<b>Warning:</b> You are marked as unavailable. This hides you from the site.<br/><br/>";
	}
?>

<form method=POST action="<?php echo PATH; ?>changeprofile">
	<?php echoCSRFToken(); ?>
	To change your username or password, use <a href='<?php echo PATH; ?>changelogin'>this page</a>.<br/><br/>
	
	Name: <input name=name value="<?php echo $displayname; ?>"> (this is not your username, but the name others see)<br/><br/>

	Available for dating: <input type=checkbox name=available <?php if ($available) { ?>checked=true<?php } ?>><br/><br/>

	Year of birth: <input name=yob size=4 maxlength=4 value="<?php echo $yob; ?>"> (to estimate your age)<br/><br/>

	You are looking for someone between <input name=agefrom maxlength=3 value=<?php echo $agefrom;?> size=2> and <input name=ageto maxlength=3 value=<?php echo $ageto;?> size=2> years old.<br/><br/>

	Your physical gender: <?php echo $physicalfeatures; ?> Please do not use "other" because it confuses the search. Only use it when necessary.<br/><br/>

	You are looking for: <?php echo $lookingfor; ?><br/><br/>

	Location: Click on the map!<br/>
	<img src="<?php echo PATH; ?>res/img/map.png" id="map"/><br/>
	lat=<input size=8 id=lat name=latitude value=<?php echo $latitude; ?>>, lon=<input id=lon name=longitude size=7 value=<?php echo $longitude;?>><br/><br/>
	<script src="<?php echo PATH; ?>res/imageposition.js"></script>
	<script>
		var NX = 2.2;
		var NY = 53.8;
		var CX = 0.02140;
		var CY = 0.01352;
		document.getElementById('map').onclick = function(ev) {
			var coords = GetCoordinates(document.getElementById('map'), ev);
			document.getElementById('lon').value = (NX + (coords[0] * CX)).toFixed(4);
			document.getElementById('lat').value = (NY - (coords[1] * CY)).toFixed(5);
		}
		// console.log(RX = (NX + (202 * CX)), RY = (NY - (44 * CY)), "http://www.openstreetmap.org/?mlat=" + RY + "&mlon=" + RX + "&layers=Q"); // Groningen
		// console.log(RX = (NX + (184 * CX)), RY = (NY - (310 * CY)), "http://www.openstreetmap.org/?mlat=" + RY + "&mlon=" + RX + "&layers=Q"); // Luxembourg
		// console.log(RX = (NX + (127 * CX)), RY = (NY - (108 * CY)), "http://www.openstreetmap.org/?mlat=" + RY + "&mlon=" + RX + "&layers=Q"); // Amsterdam
		// console.log(RX = (NX + (8 * CX)), RY = (NY - (289 * CY)), "http://www.openstreetmap.org/?mlat=" + RY + "&mlon=" + RX + "&layers=Q"); // Amiens
	</script>

	Hide people who are more than <input name=interestarea size=4 value=<?php echo $interestarea;?>> kilometers away (0 means no limit).<br/>
	Hint: From Amsterdam to Rotterdam is 60km and from Groningen to Rotterdam is 200km.<br/><br/>

	Your introduction, who you are looking for, or what others should know about you:<br/>
	<textarea name=freetext cols=80 rows=5><?php echo $freetext; ?></textarea><br/>
	Note: the first 120 characters will be shown in the search results.<br/><br/>

	Facebook profile: <input disabled=disabled size=40 value="<?php echo $fburl; ?>"><br/><br/>

	<input type=submit value=Save> <input type=button value=Cancel onclick='location="<?php echo PATH; ?>";'>
</form>

<?php 
	require('footer.php');

