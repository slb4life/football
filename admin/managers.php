<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host","$db_user","$db_password", "$db_name") or die(mysqli_error());

	$season_id = $_SESSION['season_id'];
	$season_name = $_SESSION['season_name'];

	$PHP_SELF = $_SERVER['PHP_SELF'];
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];

	if (isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
	if (isset($_REQUEST['manager_id'])){ $manager_id = $_REQUEST['manager_id']; }
	if (isset($_POST['add_submit'])){ $add_submit = $_POST['add_submit']; }
	if (isset($_POST['delete_submit'])){ $delete_submit = $_POST['delete_submit']; }
	if (isset($_POST['modify_submit'])){ $modify_submit = $_POST['modify_submit']; }
	if (isset($_POST['copy_season_submit'])){ $copy_season_submit = $_POST['copy_season_submit']; }
	if (isset($_POST['remove_season_submit'])){ $remove_season_submit = $_POST['remove_season_submit']; }
	if (isset($_POST['add_timeline'])){ $add_timeline = $_POST['add_timeline']; }
	if (isset($_POST['remove_timeline'])){ $remove_timeline = $_POST['remove_timeline']; }

	if (isset($add_submit)) {
		$manager_first_name = trim($_POST['manager_first_name']);
		$manager_last_name = trim($_POST['manager_last_name']);
		$manager_pob = trim($_POST['manager_pob']);
		$manager_dob_date = $_POST['dob_year']."-".$_POST['dob_month']."-".$_POST['dob_day'];
		$manager_player_id = trim($_POST['manager_player_id']);
		$manager_profile = str_replace("\r\n","<br>", trim($_POST['manager_profile']));
		$manager_pc = str_replace("\r\n","<br>", trim($_POST['manager_pc']));
		$publish = $_POST['publish'];

		if (!get_magic_quotes_gpc()) {
			$manager_first_name = addslashes($manager_first_name);
			$manager_last_name = addslashes($manager_last_name);
			$manager_pob = addslashes($manager_pob);
			$manager_profile = addslashes($manager_profile);
			$manager_pc = addslashes($manager_pc);
		}
		if (!isset($publish)) { $publish = 0; }

		if ($manager_first_name != '') {
			mysqli_query($db_connect, "INSERT INTO team_managers SET
				ManagerFirstName = '$manager_first_name',
				ManagerLastName = '$manager_last_name',
				ManagerDOB = '$manager_dob_date',
				ManagerPOB = '$manager_pob',
				ManagerProfile = '$manager_profile',
				ManagerPC = '$manager_pc',
				ManagerPublish = '$publish',
				ManagerPlayerID = '$manager_player_id'
			") or die(mysqli_error());
			$manager_id = mysqli_insert_id($db_connect);
			mysqli_query($db_connect, "INSERT INTO team_seasons SET SeasonID = '$season_id', SeasonManagerID = '$manager_id'") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");
		
	} else if (isset($modify_submit)) {
		$manager_first_name = trim($_POST['manager_first_name']);
		$manager_last_name = trim($_POST['manager_last_name']);
		$manager_pob = trim($_POST['manager_pob']);
		$manager_dob_date = $_POST['dob_year']."-".$_POST['dob_month']."-".$_POST['dob_day'];
		$manager_player_id = trim($_POST['manager_player_id']);
		$manager_profile = str_replace("\r\n","<br>", trim($_POST['manager_profile']));
		$manager_pc = str_replace("\r\n","<br>", trim($_POST['manager_pc']));
		$publish = $_POST['publish'];
		$manager_id = $_POST['manager_id'];

		if (!get_magic_quotes_gpc()) {
			$manager_first_name = addslashes($manager_first_name);
			$manager_last_name = addslashes($manager_last_name);
			$manager_pob = addslashes($manager_pob);
			$manager_profile = addslashes($manager_profile);
			$manager_pc = addslashes($manager_pc);
		}
		if (!isset($publish)) { $publish = 0; }

		if ($manager_first_name != '') {
			mysqli_query($db_connect, "UPDATE team_managers SET
				ManagerFirstName = '$manager_first_name',
				ManagerLastName = '$manager_last_name',
				ManagerDOB = '$manager_dob_date',
				ManagerPOB = '$manager_pob',
				ManagerProfile = '$manager_profile',
				ManagerPC = '$manager_pc',
				ManagerPublish = '$publish',
				ManagerPlayerID = '$manager_player_id'
				WHERE ManagerID = '$manager_id'
			") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");

	} else if (isset($delete_submit)) {
		$manager_id = $_POST['manager_id'];
		mysqli_query($db_connect, "DELETE FROM team_managers WHERE ManagerID = '$manager_id' LIMIT 1") or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_seasons WHERE SeasonManagerID = '$manager_id'") or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_managers_time WHERE ManagerID = '$manager_id'") or die(mysqli_error());

	} else if(isset($copy_season_submit)) {
		$copy_season = $_POST['copy_season'];
		$manager_id = $_POST['manager_id'];
		mysqli_query($db_connect, "INSERT INTO team_seasons SET SeasonID = '$copy_season', SeasonManagerID = '$manager_id'") or die(mysqli_error());
		header("Location: $HTTP_REFERER");

	} else if (isset($remove_season_submit)) {
		$remove_season = $_POST['remove_season'];
		$manager_id = $_POST['manager_id'];
		mysqli_query($db_connect, "DELETE FROM team_seasons WHERE SeasonID = '$remove_season' AND SeasonManagerID = '$manager_id' LIMIT 1") or die(mysqli_error());
		header("Location: $HTTP_REFERER");
		
	} else if (isset($add_timeline)) {
		$start_date = $_POST['start_year']."-".$_POST['start_month']."-".$_POST['start_day'];
		$end_date = $_POST['end_year']."-".$_POST['end_month']."-".$_POST['end_day'];
		$manager_id = $_POST['manager_id'];
		mysqli_query($db_connect, "INSERT INTO team_managers_time SET ManagerID = '$manager_id', StartDate = '$start_date', EndDate = '$end_date'") or die(mysqli_error());
		header("Location: $HTTP_REFERER");

	} else if (isset($remove_timeline)) {
		$manager_id = $_POST['manager_id'];
		$remove_timeline_select = $_POST['remove_timeline_select'];
		mysqli_query($db_connect, "DELETE FROM team_managers_time WHERE ID = '$remove_timeline_select' LIMIT 1") or die(mysqli_error());
		header("Location: $HTTP_REFERER");
	}
	echo "<!DOCTYPE html>\n";
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');

	if (!isset($action)) {
		echo "<table align='center' width='600'><tr><td>\n";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Add Manager</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
		echo "<td align='left' valign='top'>First Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='manager_first_name'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Last Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='manager_last_name'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Date of Birth:</td>\n";
		echo "<td align='left' valign='top'>\n";
		echo "<select name='dob_day'>\n";
		for($i = 1 ; $i < 32 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == "01") {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='dob_month'>\n";
		for($i = 1 ; $i < 13 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == "01") {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='dob_year'>\n";
		for($i = 1900 ; $i < 2025 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == "2015") {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Place of Birth:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='manager_pob'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Profile:<br><textarea name='manager_profile' cols='40' rows='15'></textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Playing Career (Clubs Played for):<br><textarea name='manager_pc' cols='40' rows='3'></textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Link to Player Stats:</td>\n";
		echo "<td align='left' valign='top'>\n";
		echo "<select name='manager_player_id'>";
		echo "<option value='0'>None</option>";
		$get_player = mysqli_query($db_connect, "SELECT
			CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
			P.PlayerID AS player_id
			FROM team_players P
			ORDER BY player_name
		") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_player)) {
			echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>";
		}
		mysqli_free_result($get_player);

		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Published:</td>\n";
		echo "<td align='left' valign='top'><input type='checkbox' name='publish' value='1' CHECKED></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='add_submit' value='Add Manager'>\n";
		echo "</form>\n";
	} else if ($action == 'modify') {
		$get_manager = mysqli_query($db_connect, "SELECT
			M.ManagerID AS manager_id,
			M.ManagerFirstName AS manager_first_name,
			M.ManagerLastName AS manager_last_name,
			M.ManagerPublish AS publish,
			M.ManagerPOB AS manager_pob,
			DAYOFMONTH(ManagerDOB) AS day,
			MONTH(ManagerDOB) AS month,
			YEAR(ManagerDOB) AS year,
			M.ManagerPC AS manager_pc,
			M.ManagerProfile AS manager_profile,
			M.ManagerPlayerID AS manager_player_id
			FROM team_managers AS M
			WHERE ManagerID = '$manager_id'
			LIMIT 1
		") or die(mysqli_error());
		$pdata = mysqli_fetch_array($get_manager);
		mysqli_free_result($get_manager);

		echo "<table align='center' width='600'>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Modify/Delete Manager</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>First Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='manager_first_name' value='".$pdata['manager_first_name']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Last Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='manager_last_name' value='".$pdata['manager_last_name']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Date of Birth:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='dob_day'>";
		for($i = 1 ; $i < 32 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == $pdata['day']) {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='dob_month'>";
		for($i = 1 ; $i < 13 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == $pdata['month']) {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='dob_year'>";
		for($i = 1900 ; $i < 2025 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == $pdata['year']) {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Place of Birth:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='manager_pob' value='".$pdata['manager_pob']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Profile:<br>\n";
		$pdata['manager_profile'] = str_replace('<br>', "\r\n", $pdata['manager_profile']);
		echo "<textarea name='manager_profile' cols='40' rows='15'>".$pdata['manager_profile']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Playing Career (Clubs Played for):<br>\n";
		$pdata['manager_pc'] = str_replace('<br>', "\r\n", $pdata['manager_pc']);
		echo "<textarea name='manager_pc' cols='40' rows='3'>".$pdata['manager_pc']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Link to Player Stats:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='manager_player_id'>";
		$get_player = mysqli_query($db_connect, "SELECT
			CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
			P.PlayerID AS player_id
			FROM team_players AS P
			ORDER BY player_name
		") or die(mysqli_error());

		if ($pdata['manager_player_id'] == 0) {
			echo "<option value='0' SELECTED>None</option>";
			while($data = mysqli_fetch_array($get_player)) {
				echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>";
			}
		} else {
			echo "<option value='0'>None</option>";
			while($data = mysqli_fetch_array($get_player)) {
				if ($data['player_id'] == $pdata['manager_player_id']) {
					echo "<option value='".$data['player_id']."' SELECTED>".$data['player_name']."</option>";
				} else {
					echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>";
				}
			}
		}
		mysqli_free_result($get_player);

		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Published:</td>\n";
		echo "<td align='left' valign='top'>";

		if ($pdata['publish'] == 1) {
			echo "<input type='checkbox' name='publish' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='publish' value='1'>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='hidden' name='manager_id' value='".$manager_id."'>\n";
		echo "<input type='submit' name='modify_submit' value='Modify Manager'>\n";
		echo "<input type='submit' name='delete_submit' value='Delete Manager'>\n";
		echo "</form>\n";
		echo "<hr width='100%'>\n";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		$get_timeline = mysqli_query($db_connect, "SELECT
			MT.ID AS id,
			MT.StartDate AS start_date,
			MT.EndDate AS end_date
			FROM team_managers_time AS MT
			WHERE MT.ManagerID = '$manager_id'
			ORDER BY start_date
		") or die(mysqli_error());

		if (mysqli_num_rows($get_timeline) > 0) {
			echo "<b>Current Timeline (YYYY-MM-DD):</b><br>";
			while($data = mysqli_fetch_array($get_timeline)) {
				echo "".$data['start_date']." - ".$data['end_date']."<br>\n";
			}
			mysqli_data_seek($get_timeline, 0);
			echo "<br>\n";
		}
		echo "<b>Add Timeline:</b><br>";
		echo "Start: <select name='start_year'>";
		for($i = 1950 ; $i < 2025 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == "2015") {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='start_month'>";
		for($i = 1 ; $i < 13 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == "01") {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='start_day'>";
		for($i = 1 ; $i < 32 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == "01") {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "End: <select name='end_year'>";
		for($i = 1950 ; $i < 2025 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == "2015") {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='end_month'>";
		for($i = 1 ; $i < 13 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == "01") {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='end_day'>";
		for($i = 1 ; $i < 32 ; $i++) {
			if ($i < 10) {
				$i = "0".$i;
			}
			if ($i == "01") {
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			} else {
				echo "<option value='".$i."'>".$i."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<input type='submit' name='add_timeline' value='Add'><br><br>\n";
		while($data = mysqli_fetch_array($get_timeline)) {
			echo "<b>Remove Timeline:</b><br>";
			echo "<select name='remove_timeline_select'>";
			echo "<option value='".$data['id']."'>".$data['start_date']." - ".$data['end_date']."</option>\n";
			echo "</select>\n";
			echo "<input type='submit' name='remove_timeline' value='Remove'><br>\n";
		}
		echo "<input type='hidden' name='manager_id' value='".$manager_id."'>\n";
		echo "</form>\n";
		mysqli_free_result($get_timeline);

		echo "<hr width='100%'>\n";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<table width='100%' cellspacing='0' cellpadding='0' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'><b>Already in Squad in Season(s):</b><br>";
		$get_seasons = mysqli_query($db_connect, "SELECT
			SN.SeasonName AS season_name,
			S.SeasonID AS season_id
			FROM team_season_names AS SN, team_seasons AS S
			WHERE S.SeasonID = SN.SeasonID
			AND S.SeasonManagerID = '$manager_id'
			ORDER BY season_name
		") or die(mysqli_error());
		$all_seasons = mysqli_query($db_connect, "SELECT SeasonID FROM team_season_names") or die(mysqli_error());
		$check_manager = mysqli_num_rows($all_seasons);
		mysqli_free_result($all_seasons);

		$check_managers = mysqli_num_rows($get_seasons);
		$check_seasons = "";
		while($data = mysqli_fetch_array($get_seasons)) {
			echo "".$data['season_name']."<br>\n";
			$check_season[] = $data['season_id'];
			$check_seasons .= "<option value='".$data['season_id']."'>".$data['season_name']."</option>\n";
		}

		if ($check_manager != $check_managers) {
			$i = 0;
			echo "<br>Copy this Manager to Season: <select name='copy_season'>";
			$get_season = mysqli_query($db_connect, "SELECT * FROM team_season_names ORDER BY SeasonName") or die(mysqli_error());
			while($data = mysqli_fetch_array($get_season)) {
				if ($data['SeasonID'] != $check_season[$i]) {
					echo "<option value='".$data['SeasonID']."'>".$data['SeasonName']."</option>\n";
				} else {
					$i++;
				}
			}
			mysqli_free_result($get_season);

			echo "</select>\n";
			echo "<input type='submit' name='copy_season_submit' value='Copy'><br><br>\n";
			echo "You may Remove Manager from Season: <select name='remove_season'>".$check_seasons."</select>\n";
			echo "<input type='submit' name='remove_season_submit' value='Remove'>\n";
			echo "<input type='hidden' name='manager_id' value='".$manager_id."'>\n";
			echo "</form>\n";
		} else if ($check_manager == $check_managers) {
			echo "You may Remove Manager from Season: <select name='remove_season'>".$check_seasons."</select>\n";
			echo "<input type='submit' name='remove_season_submit' value='Remove'>\n";
			echo "<input type='hidden' name='manager_id' value='".$manager_id."'>\n";
			echo "</form>\n";
		}
		mysqli_free_result($get_seasons);

		echo "<hr width='100%'>\n";
		echo "<form enctype='multipart/form-data' method='post' action='image_upload.php?session_id=".$session."'>\n";
		echo "<b>Upload Manager Picture</b><br>\n";
		echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
		echo "<input name='image_file' type='file'>\n";
		echo "<input name='action' type='hidden' value='7'>\n";
		echo "<input type='submit' name='submit' value='Upload'>\n";
		echo "<input type='hidden' name='manager_id' value='".$manager_id."'>\n";
		echo "</form>\n";
		$image_url = "../images/manager".$manager_id.".jpg";
		$image_url2 = "../images/manager".$manager_id.".png";

		if (file_exists($image_url)) {
			echo "<img src='".$image_url."' alt=''>";
			echo "<br><a href='delete_picture.php?session_id=".$session."&amp;manager_id=".$manager_id."&amp;action=7&amp;type=jpg'>Delete this Picture</a>";
		} else if (file_exists($image_url2)) {
			echo "<img src='".$image_url2."' alt=''>";
			echo "<br><a href='delete_picture.php?session_id=".$session."&amp;manager_id=".$manager_id."&amp;action=7&amp;type=png'>Delete this Picture</a>";
		} else {
			echo "<img src='../images/no_image.png' alt='' width='100' height='100'>";
		}

		if (file_exists($image_url) || file_exists($image_url)) {
			echo "<br><br>\n";
			echo "<form enctype='multipart/form-data' method='post' action='image_upload.php?session_id=".$session."'>\n";
			echo "<b>Upload Second Manager Picture</b><br>\n";
			echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
			echo "<input name='image_file' type='file'>\n";
			echo "<input name='action' type='hidden' value='8'>\n";
			echo "<input type='submit' name='submit' value='Upload'>\n";
			echo "<input type='hidden' name='manager_id' value='".$manager_id."'>\n";
			echo "</form>\n";
			$image_url3 = "../images/manager".$manager_id."_1.jpg";
			$image_url4 = "../images/manager".$manager_id."_1.png";

			if (file_exists($image_url3)) {
				echo "<img src='".$image_url3."' alt=''>";
				echo "<br><a href='delete_picture.php?session_id=".$session."&amp;manager_id=".$manager_id."&amp;action=8&amp;type=jpg'>Delete this Picture</a>";
			} else if(file_exists($image_url4)) {
				echo "<img src='".$image_url4."' alt=''>";
				echo "<br><a href='delete_picture.php?session_id=".$session."&amp;manager_id=".$manager_id."&amp;action=8&amp;type=png'>Delete this Picture</a>";
			} else {
				echo "<img src='../images/no_image.png' alt='' width='100' height='100'>";
			}
			echo "<br><br>\n";
			echo "<form enctype='multipart/form-data' method='post' action='image_upload.php?session_id=".$session."'>\n";
			echo "<b>Upload Third Manager Picture</b><br>\n";
			echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
			echo "<input name='image_file' type='file'>\n";
			echo "<input name='action' type='hidden' value='9'>\n";
			echo "<input type='submit' name='submit' value='Upload'>\n";
			echo "<input type='hidden' name='manager_id' value='".$manager_id."'>\n";
			echo "</form>\n";
			$image_url5 = "../images/manager".$manager_id."_2.jpg";
			$image_url6 = "../images/manager".$manager_id."_2.png";

			if (file_exists($image_url5)) {
					echo "<img src='".$image_url5."' alt=''>";
					echo "<br><a href='delete_picture.php?session_id=".$session."&amp;manager_id=".$manager_id."&amp;action=9&amp;type=jpg'>Delete this Picture</a>";
			} else if (file_exists($image_url6)) {
				echo "<img src='".$image_url6."' alt=''>";
				echo "<br><a href='delete_picture.php?session_id=".$session."&amp;manager_id=".$manager_id."&amp;action=9&amp;type=png'>Delete this Picture</a>";
			} else {
				echo "<img src='../images/no_image.png' alt='' width='100' height='100'>";
			}
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
	echo "</td>\n";
	echo "<td align='left' valign='top'>";
	$get_managers = mysqli_query($db_connect, "SELECT
		CONCAT(M.ManagerFirstName, ' ', M.ManagerLastName) AS manager_name,
		M.ManagerID AS manager_id,
		M.ManagerPublish AS publish
		FROM team_managers AS M, team_seasons AS S
		WHERE M.ManagerID = S.SeasonManagerID
		AND S.SeasonID = '$season_id'
		ORDER BY manager_name
	") or die(mysqli_error());
	$get_total = mysqli_num_rows($get_managers);

	if ($get_total < 1) {
		echo "<b>No Managers.</b>";
	} else {
		echo "<b>Managers: ".$season_name."</b><br><br>Number of Managers: ".$get_total."<br><br>";
		while($data = mysqli_fetch_array($get_managers)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;manager_id=".$data['manager_id']."'>".$data['manager_name']."</a>";

			if ($data['publish'] == 0) {
				echo " (NB)<br>";
			} else  {
				echo "<br>";
			}
		}
	}
	echo "<br><br>";
	echo "NB = This Manager is not Published yet.";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>