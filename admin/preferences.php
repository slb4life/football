<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	print("Authorization Failed.<br><a href='index.php'>Restart, Please</a>");
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host","$db_user","$db_password","$db_name") or die(mysqli_error());

	$season_id = $_SESSION['season_id'];
	$season_name = $_SESSION['season_name'];

	$PHP_SELF = $_SERVER['PHP_SELF'];

	if (isset($_POST['submit'])){ $submit = $_POST['submit']; }

	if (isset($submit)) {
		$team_name = trim($_POST['team_name']);
		$site_title = trim($_POST['site_title']);
		$bgcolor = $_POST['bgcolor'];
		$bgcolor1 = $_POST['bgcolor1'];
		$bgcolor2 = $_POST['bgcolor2'];
		$cellbgcolortop = $_POST['cellbgcolortop'];
		$cellbgcolorbottom = $_POST['cellbgcolorbottom'];
		$bordercolor = $_POST['bordercolor'];
		if (isset($_POST['accept_multi_language'])){ $accept_multi_language = $_POST['accept_multi_language']; }
		if (isset($_POST['show_staff'])){ $show_staff = $_POST['show_staff']; }
		if (isset($_POST['show_comments'])){ $show_comments = $_POST['show_comments']; }
		$print_date = $_POST['print_date'];
		$default_season = $_POST['default_season'];
		$default_match_type = $_POST['default_match_type'];
		$default_language = $_POST['default_language'];
		$contact = trim($_POST['contact']);
		$contact = str_replace("\r\n", '<br>', $contact);
		if (isset($_POST['show_latest_match'])){ $show_latest_match = $_POST['show_latest_match']; }
		if (isset($_POST['show_featured_player'])){ $show_featured_player = $_POST['show_featured_player']; }
		if (isset($_POST['show_search'])){ $show_search = $_POST['show_search']; }
		if (isset($_POST['show_next_match'])){ $show_next_match = $_POST['show_next_match']; }
		if (isset($_POST['show_top_scorers'])){ $show_top_scorers = $_POST['show_top_scorers']; }
		if (isset($_POST['show_top_apps'])){ $show_top_apps = $_POST['show_top_apps']; }
		if (isset($_POST['show_top_bookings'])){ $show_top_bookings = $_POST['show_top_bookings']; }
		if (isset($_POST['show_contact'])){ $show_contact = $_POST['show_contact']; }

		if (!get_magic_quotes_gpc()) {
			$team_name = addslashes($team_name);
			$site_title = addslashes($site_title);
			$contact = addslashes($contact);
		}

		if (!isset($accept_multi_language)){ $accept_multi_language = 0; }
		if (!isset($show_staff)){ $show_staff = 0; }
		if (!isset($show_comments)){ $show_comments = 0; }
		if (!isset($show_latest_match)){ $show_latest_match = 0; }
		if (!isset($show_featured_player)){ $show_featured_player = 0; }
		if (!isset($show_search)){ $show_search = 0; }
		if (!isset($show_next_match)){ $show_next_match = 0; }
		if (!isset($show_top_scorers)){ $show_top_scorers = 0; }
		if (!isset($show_top_apps)){ $show_top_apps = 0; }
		if (!isset($show_top_bookings)){ $show_top_bookings = 0; }
		if (!isset($show_contact)){ $show_contact = 0; }

		mysqli_query($db_connect, "UPDATE team_preferences SET
			team_name = '$team_name',
			site_title = '$site_title',
			bgcolor = '$bgcolor',
			bgcolor1 = '$bgcolor1',
			bgcolor2 = '$bgcolor2',
			cellbgcolortop = '$cellbgcolortop',
			cellbgcolorbottom = '$cellbgcolorbottom',
			bordercolor = '$bordercolor',
			accept_multi_language = '$accept_multi_language',
			show_staff = '$show_staff',
			show_comments = '$show_comments',
			print_date = '$print_date',
			default_season = '$default_season',
			default_match_type = '$default_match_type',
			default_language = '$default_language',
			contact = '$contact',
			show_latest_match = '$show_latest_match',
			show_featured_player = '$show_featured_player',
			show_search = '$show_search',
			show_next_match = '$show_next_match',
			show_top_scorers = '$show_top_scorers',
			show_top_apps = '$show_top_apps',
			show_top_bookings = '$show_top_bookings',
			show_contact = '$show_contact'
			WHERE ID = '1'
		") or die(mysqli_error());
	}
	$pref = mysqli_query($db_connect, "SELECT * FROM team_preferences WHERE ID = '1'") or die(mysqli_error());
	$pdata = mysqli_fetch_array($pref);
	mysqli_free_result($pref);

	$pdata['contact'] = str_replace('<br>', "\r\n", $pdata['contact']);
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');

	echo "<table align='center' width='600'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
	echo "<h1>Preferences</h1>\n";
	echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top'>Team Name:</td>\n";
	echo "<td align='left' valign='top'><input type='text' name='team_name' value='".$pdata['team_name']."'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Site Title:</td>\n";
	echo "<td align='left' valign='top'><input type='text' name='site_title' value='".$pdata['site_title']."'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Bg Color:</td>\n";
	echo "<td align='left' valign='top'>#<input type='text' name='bgcolor' value='".$pdata['bgcolor']."' size='6'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Bg Color (1) in Tables:</td>\n";
	echo "<td align='left' valign='top'>#<input type='text' name='bgcolor1' value='".$pdata['bgcolor1']."' size='6'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Bg Color (2) in Tables:</td>\n";
	echo "<td align='left' valign='top'>#<input type='text' name='bgcolor2' value='".$pdata['bgcolor2']."' size='6'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Top Cell BG Color:</td>\n";
	echo "<td align='left' valign='top'>#<input type='text' name='cellbgcolortop' value='".$pdata['cellbgcolortop']."' size='6'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Bottom Cell BG Color:</td>\n";
	echo "<td align='left' valign='top'>#<input type='text' name='cellbgcolorbottom' value='".$pdata['cellbgcolorbottom']."' size='6'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Border Color:</td>\n";
	echo "<td align='left' valign='top'>#<input type='text' name='bordercolor' value='".$pdata['bordercolor']."' size='6'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Accept Multi Language?</td>\n";
	echo "<td align='left' valign='top'>";

	if ($pdata['accept_multi_language'] == 1) {
		echo "<input type='checkbox' name='accept_multi_language' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='accept_multi_language' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Managers?</td>\n";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_staff'] == 1) {
		echo "<input type='checkbox' name='show_staff' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_staff' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Comments?</td>\n";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_comments'] == 1) {
		echo "<input type='checkbox' name='show_comments' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_comments' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Print Date:</td>";
	echo "<td align='left' valign='top'>\n";
	echo "<select name='print_date'>";

	if ($pdata['print_date'] == 1) {
		echo "<option value='1' SELECTED>dd.mm.yyyy</option>";
	} else {
		echo "<option value='1'>dd.mm.yyyy</option>";
	}
	if ($pdata['print_date'] == 2) {
		echo "<option value='2' SELECTED>mm.dd.yyyy</option>";
	} else {
		echo "<option value='2'>mm.dd.yyyy</option>";
	}
	if ($pdata['print_date'] == 3) {
		echo "<option value='3' SELECTED>month.day.year</option>";
	} else {
		echo "<option value='3'>month.date.year</option>";
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Default Season (This season):</td>\n";
	echo "<td align='left' valign='top'>";
	echo "<select name='default_season'>";
	echo "<option value='0'>All</option>\n";
	$get_seasons = mysqli_query($db_connect, "SELECT * FROM team_season_names WHERE SeasonPublish = '1' ORDER BY SeasonName") or die(mysqli_error());
	while($data = mysqli_fetch_array($get_seasons)) {
		if ($data['SeasonID'] == $pdata['default_season']) {
			echo "<option value='$data[SeasonID]' SELECTED>$data[SeasonName]</option>\n";
		} else {
			echo "<option value='$data[SeasonID]'>$data[SeasonName]</option>\n";
		}
	}
	mysqli_free_result($get_seasons);

	echo "</select>\n";
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Default Match Type:</td>\n";
	echo "<td align='left' valign='top'>";
	echo "<select name='default_match_type'>";
	echo "<option value='0'>All</option>\n";
	$get_types = mysqli_query($db_connect, "SELECT * FROM team_match_types ORDER BY MatchTypeName") or die(mysqli_error());
	while($data = mysqli_fetch_array($get_types)) {
		if ($data['MatchTypeID'] == $pdata['default_match_type']) {
			echo "<option value='$data[MatchTypeID]' SELECTED>$data[MatchTypeName]</option>\n";
		} else {
			echo "<option value='$data[MatchTypeID]'>$data[MatchTypeName]</option>\n";
		}
	}
	mysqli_free_result($get_types);

	echo "</select>\n";
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Default Language:</td>\n";
	echo "<td align='left' valign='top'>";
	echo "<select name='default_language'>";

	if ($pdata['default_language'] == 1) {
		echo "<option value='1' SELECTED>English</option>";
	} else {
		echo "<option value='1'>English</option>";
	}
	if ($pdata['default_language'] == 2) {
		echo "<option value='2' SELECTED>Portuguese</option>";
	} else {
		echo "<option value='2'>Portuguese</option>";
	}
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Contact Information:</td>\n";
	echo "<td align='left' valign='top'><textarea name='contact' cols'40' rows'15'>".$pdata['contact']."</textarea></td>\n";
	echo "</tr><tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' colspan='2'><b>Layout</b></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Latest Match?</td>\n";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_latest_match'] == 1) {
		echo "<input type='checkbox' name='show_latest_match' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_latest_match' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Featured Player?</td>\n";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_featured_player'] == 1) {
		echo "<input type='checkbox' name='show_featured_player' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_featured_player' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Search?</td>\n";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_search'] == 1) {
		echo "<input type='checkbox' name='show_search' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_search' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Next Match?</td>\n";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_next_match'] == 1) {
		echo "<input type='checkbox' name='show_next_match' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_next_match' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Top Scorers?</td>\n";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_top_scorers'] == 1) {
		echo "<input type='checkbox' name='show_top_scorers' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_top_scorers' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Top Appearances?</td>";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_top_apps'] == 1) {
		echo "<input type='checkbox' name='show_top_apps' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_top_apps' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Top Bookings?</td>";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_top_bookings'] == 1) {
		echo "<input type='checkbox' name='show_top_bookings' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_top_bookings' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Show Contact Info?</td>\n";
	echo "<td align='left' valign='top'>";

	if ($pdata['show_contact'] == 1) {
		echo "<input type='checkbox' name='show_contact' value='1' CHECKED>";
	} else {
		echo "<input type='checkbox' name='show_contact' value='1'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top' colspan='2'>";
	echo "<input type='submit' name='submit' value='Save'>";
	echo "</form>";
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top' colspan='2'><br><br>\n";
	echo "<form enctype='multipart/form-data' method='post' action='image_upload.php?session_id=".$session."'>\n";
	echo "<b>Upload Header Picture</b><br>\n";
	echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
	echo "<input name='image_file' type='file'>\n";
	echo "<input name='action' type='hidden' value='4'>\n";
	echo "<input type='submit' name='submit' value='Upload'>\n";
	echo "</form>\n";
	$image_url = "../images/header.jpg";
	$image_url2 = "../images/header.png";

	if (file_exists($image_url)) {
		echo "<img src='".$image_url."' alt=''>";
		echo "<br><a href='delete_picture.php?session_id=".$session."&amp;action=4&amp;type=jpg'>Delete header picture</a>";
	} else if (file_exists($image_url2)) {
		echo "<img src='".$image_url2."' alt=''>";
		echo"<br><a href='delete_picture.php?session_id=".$session."&amp;action=4&amp;type=png'>Delete header picture</a>";
	} else {
		echo "<img src='../images/no_image.png' alt='' width='100' height='100'>";
	}
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top' colspan='2'><br><br>\n";
	echo "<form enctype='multipart/form-data' method='post' action='image_upload.php?session_id=".$session."'>\n";
	echo "<b>Upload logo</b><br>(Max Width 60px)<br>\n";
	echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
	echo "<input name='image_file' type='file'>\n";
	echo "<input name='action' type='hidden' value='5'>\n";
	echo "<input type='submit' name='submit' value='Upload'>\n";
	echo "</form>\n";
	$image_url = "../images/team_logo.jpg";
	$image_url2 = "../images/team_logo.png";

	if (file_exists($image_url)) {
		echo "<img src='".$image_url."' alt=''>";
		echo"<br><a href='delete_picture.php?session_id=".$session."&amp;action=5&amp;type=jpg'>Delete logo</a>";
	} elseif(file_exists($image_url2)) {
		echo "<img src='".$image_url2."' alt=''>";
		echo"<br><a href='delete_picture.php?session_id=".$session."&amp;action=5&amp;type=png'>Delete logo</a>";
	} else {
		echo "<img src='../images/no_image.png' alt='' width='100' height='100'>";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>
