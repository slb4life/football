<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host","$db_user","$db_password","$db_name") or die(mysqli_error());
	
	$season_id = $_SESSION['season_id'];
	$season_name = $_SESSION['season_name'];

	$PHP_SELF = $_SERVER['PHP_SELF'];
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];

	if (isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
	if (isset($_REQUEST['player_id'])){ $player_id = $_REQUEST['player_id']; }	
	if (isset($_POST['add_submit'])){ $add_submit = $_POST['add_submit']; }
	if (isset($_POST['delete_submit'])){ $delete_submit = $_POST['delete_submit']; }
	if (isset($_POST['modify_submit'])){ $modify_submit = $_POST['modify_submit'];}
	if (isset($_POST['copy_season_submit'])){ $copy_season_submit = $_POST['copy_season_submit'];}
	if (isset($_POST['remove_season_submit'])){ $remove_season_submit = $_POST['remove_season_submit'];}
	if (isset($_POST['add_opponent_player'])){ $add_opponent_player = $_POST['add_opponent_player'];}

	if (isset($add_submit)) {
		$player_first_name = trim($_POST['player_first_name']);
		$player_last_name = trim($_POST['player_last_name']);
		$player_number = trim($_POST['player_number']);
		$player_position = trim($_POST['player_position']);
		$player_dob_date = $_POST['dob_year']."-".$_POST['dob_month']."-".$_POST['dob_day'];
		$player_dob = trim($_POST['player_dob']);
		$player_height = trim($_POST['player_height']);
		$player_weight = trim($_POST['player_weight']);
		$player_description = str_replace("\r\n","<br>", trim($_POST['player_description']));
		$player_pc = str_replace("\r\n","<br>", trim($_POST['player_pc']));
		$show_stats = 1;
		$all_data = 1;

		if (!get_magic_quotes_gpc()) {
			$player_first_name = addslashes($player_first_name);
			$player_last_name = addslashes($player_last_name);
			$player_number = addslashes($player_number);
			$player_dob = addslashes($player_dob);
			$player_height = addslashes($player_height);
			$player_weight = addslashes($player_weight);
			$player_description = addslashes($player_description);
			$player_pc = addslashes($player_pc);
		}

		if ($player_first_name != '') {
			mysqli_query($db_connect, "INSERT INTO team_players SET
				PlayerFirstName = '$player_first_name',
				PlayerLastName = '$player_last_name',
				PlayerNumber = '$player_number',
				PlayerDOB = '$player_dob_date',
				PlayerPOB = '$player_dob',
				PlayerHeight = '$player_height',
				PlayerWeight = '$player_weight',
				PlayerDescription = '$player_description',
				PlayerPC = '$player_pc',
				PlayerPositionID = '$player_position',
				PlayerShowStats = '$show_stats',
				PlayerAllData = '$all_data',
				PlayerInSquadList = '1'
			") or die(mysqli_error());
			$player_id = mysqli_insert_id($db_connect);
			mysqli_query($db_connect, "INSERT INTO team_seasons SET SeasonID = '$season_id', SeasonPlayerID = '$player_id'") or die(mysqli_error());
		}
	} else if (isset($modify_submit)) {
		$player_first_name = trim($_POST['player_first_name']);
		$player_last_name = trim($_POST['player_last_name']);
		$player_number = trim($_POST['player_number']);
		$player_position = trim($_POST['player_position']);
		$player_dob = trim($_POST['player_dob']);
		$player_height = trim($_POST['player_height']);
		$player_weight = trim($_POST['player_weight']);
		$player_dob_date = $_POST['dob_year']."-".$_POST['dob_month']."-".$_POST['dob_day'];
		$player_description = str_replace("\r\n","<br>", trim($_POST['player_description']));
		$player_pc = str_replace("\r\n","<br>", trim($_POST['player_pc']));
		$player_id = $_POST['player_id'];
		$publish = $_POST['publish'];
		$all_data = $_POST['all_data'];
		$in_squad = $_POST['in_squad'];
		$show_stats = $_POST['show_stats'];

		if (!get_magic_quotes_gpc()) {
			$player_first_name = addslashes($player_first_name);
			$player_last_name = addslashes($player_last_name);
			$player_number = addslashes($player_number);
			$player_dob = addslashes($player_dob);
			$player_height = addslashes($player_height);
			$player_weight = addslashes($player_weight);
			$player_description = addslashes($player_description);
			$player_pc = addslashes($player_pc);
		}

		if (!isset($publish)){ $publish = 0; }
		if (!isset($all_data)){ $all_data = 0; }
		if (!isset($in_squad)){ $in_squad = 0; }
		if (!isset($show_stats)){ $show_stats = 0; }

		if ($player_first_name != '') {
			mysqli_query($db_connect, "UPDATE team_players SET
				PlayerFirstName = '$player_first_name',
				PlayerLastName = '$player_last_name',
				PlayerNumber = '$player_number',
				PlayerDescription = '$player_description',
				PlayerPC = '$player_pc',
				PlayerPublish = '$publish',
				PlayerPositionID = '$player_position',
				PlayerShowStats = '$show_stats',
				PlayerAllData = '$all_data',
				PlayerPOB = '$player_dob',
				PlayerDOB = '$player_dob_date',
				PlayerHeight = '$player_height',
				PlayerWeight = '$player_weight',
				PlayerInSquadList = '$in_squad'
				WHERE PlayerID = '$player_id'
			") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");

	} else if (isset($delete_submit)) {
		$player_id = $_POST['player_id'];
		$query = mysqli_query($db_connect, "SELECT
			G.GoalID AS goal_id,
			S.SubstituteID AS substitute_id
			FROM team_goals AS G, team_substitutes AS S
			WHERE G.GoalPlayerID = '".$player_id."' OR S.SubstitutePlayerID = '".$player_id."'
		") or die(mysqli_error());

		if (mysqli_num_rows($query) == 0) {
			mysqli_query($db_connect, "DELETE FROM team_players WHERE PlayerID = '".$player_id."' LIMIT 1") or die(mysqli_error());
			mysqli_query($db_connect, "DELETE FROM team_seasons WHERE SeasonPlayerID = '".$player_id."' LIMIT 1") or die(mysqli_error());
		} else {
			echo "PERMISSON DENIED! This player has already been added to opening squads or substitutes. Press back-button.";
			exit();
		}
	} else if (isset($copy_season_submit)) {
		$copy_season = $_POST['copy_season'];
		$player_id = $_POST['player_id'];
		mysqli_query($db_connect, "INSERT INTO team_seasons SET SeasonID = '$copy_season', SeasonPlayerID = '$player_id'") or die(mysqli_error());
		header("Location: $HTTP_REFERER");

	} else if (isset($remove_season_submit)) {
		$remove_season = $_POST['remove_season'];
		$player_id = $_POST['player_id'];
		mysqli_query($db_connect, "DELETE FROM team_seasons WHERE SeasonID = '$remove_season' AND SeasonPlayerID = '$player_id' LIMIT 1") or die(mysqli_error());
		header("Location: $HTTP_REFERER");
	
	} else if (isset($add_opponent_player)) {
		$opponent_id = $_POST['opponent_id'];
		$player_id = $_POST['player_id'];
		mysqli_query($db_connect, "INSERT INTO team_players_opponent SET PlayerID = '$player_id', OpponentID = '$opponent_id'") or die(mysqli_error());
		header("Location: $HTTP_REFERER");

	}
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');
	
	if (!isset($action)) {
		echo "<table align='center' width='600'>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Add Player</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>First Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_first_name'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Last Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_last_name'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Number:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_number'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Position:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='player_position'>";
		$get_positions = mysqli_query($db_connect, "SELECT * FROM team_player_positions ORDER BY PlayerPositionID") or die(mysqli_error());
		while($pdata = mysqli_fetch_array($get_positions)) {
			echo "<option value='".$pdata['PlayerPositionID']."'>".$pdata['PlayerPositionName']."</option>\n";
		}
		mysqli_free_result($get_positions);

		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Date of Birth:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='dob_day'>";
		for($i = 1 ; $i < 32 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == "01") {
				echo "<option value='$i' SELECTED>$i</option>\n";
			} else {
				echo "<option value='$i'>$i</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='dob_month'>";
		for($i = 1 ; $i < 13 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == "01") {
				echo "<option value='$i' SELECTED>$i</option>\n";
			} else {
				echo "<option value='$i'>$i</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='dob_year'>";
		for($i = 1900 ; $i < 2025 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == "2014") {
				echo "<option value='$i' SELECTED>$i</option>\n";
			} else {
				echo "<option value='$i'>$i</option>\n";
			}
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Place of Birth:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_dob'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Height:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_height'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Weight:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_weight'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Profile:<br><textarea name='player_description' cols='40' rows='15'></textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Previous Clubs:<br><textarea name='player_pc' cols='40' rows='5'></textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Published:</td>\n";
		echo "<td align='left' valign='top'><input type='checkbox' name='publish' value='1' CHECKED></td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<input type='submit' name='add_submit' value='Add Player'>\n";
		echo "</form>\n";
	} else if ($action == 'modify') {
		$get_player = mysqli_query($db_connect, "SELECT
			P.PlayerID AS player_id,
			P.PlayerFirstName AS player_first_name,
			P.PlayerLastName AS player_last_name,
			P.PlayerPublish AS publish,
			P.PlayerAllData AS all_data,
			P.PlayerShowStats AS show_stats,
			P.PlayerPOB AS player_dob,
			P.PlayerNumber AS player_number,
			P.PlayerPositionID AS position_id,
			DAYOFMONTH(PlayerDOB) AS day_of_month,
			MONTH(PlayerDOB) AS month,
			YEAR(PlayerDOB) AS year,
			P.PlayerHeight AS player_height,
			P.PlayerWeight AS player_weight,
			P.PlayerPC AS player_pc,
			P.PlayerDescription AS player_description,
			P.PlayerID AS player_id,
			P.PlayerInSquadList AS in_squad
			FROM team_players P
			WHERE PlayerID = '$player_id'
			LIMIT 1
		") or die(mysqli_error());
		$pdata = mysqli_fetch_array($get_player);
		mysqli_free_result($get_player);

		echo "<table align='center' width='600'>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Modify/Delete Player</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>First Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_first_name' value='".$pdata['player_first_name']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Last Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_last_name' value='".$pdata['player_last_name']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Number:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_number' value='".$pdata['player_number']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Position:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='player_position'>";
		$get_positions = mysqli_query($db_connect, "SELECT * FROM team_player_positions ORDER BY PlayerPositionID") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_positions)) {
			$player_position = "$data[PlayerPositionID]";

			if ($pdata['position_id'] == "".$player_position."") {
				echo "<option value='$data[PlayerPositionID]' SELECTED>$data[PlayerPositionName]</option>\n";
			} else  {
				echo "<option value='$data[PlayerPositionID]'>$data[PlayerPositionName]</option>\n";
			}
		}
		mysqli_free_result($get_positions);

		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Date of Birth:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='dob_day'>";
		for($i = 1 ; $i < 32 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == $pdata['day_of_month']) {
				echo "<option value='$i' SELECTED>$i</option>\n";
			} else {
				echo "<option value='$i'>$i</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='dob_month'>";
		for($i = 1 ; $i < 13 ; $i++) {
			if($i<10) {
				$i = "0".$i;
			}
			if ($i == $pdata['month']) {
				echo "<option value='$i' SELECTED>$i</option>\n";
			} else {
				echo "<option value='$i'>$i</option>\n";
			}
		}
		echo "</select>\n";
		echo "<select name='dob_year'>";
		for($i = 1900 ; $i < 2025 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == $pdata['year']) {
				echo "<option value='$i' SELECTED>$i</option>\n";
			} else {
				echo "<option value='$i'>$i</option>\n";
			}
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Place of Birth:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_dob' value='".$pdata['player_dob']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Height:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_height' value='".$pdata['player_height']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Weight:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_weight' value='".$pdata['player_weight']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Profile:<br>";
		$pdata['player_description'] = str_replace('<br>', "\r\n", $pdata['player_description']);
		echo "<textarea name='player_description' cols='40' rows='15'>".$pdata['player_description']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Previous Clubs:<br>";
		$pdata['player_pc'] = str_replace('<br>', "\r\n", $pdata['player_pc']);
		echo "<textarea name='player_pc' cols='40' rows='15'>".$pdata['player_pc']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Published:</td>\n";
		echo "<td align='left' valign='top'>";
		if ($pdata['publish'] == 1) {
			echo "<input type='checkbox' name='publish' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='publish' value='1'>";
		}
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Show in squad list in players list, previews and this season page?</td>\n";
		echo "<td align='left' valign='top'>";
		if ($pdata['in_squad'] == 1) {
			echo "<input type='checkbox' name='in_squad' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='in_squad' value='1'>";
		}
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>All data filled to Database:</td>\n";
		echo "<td align='left' valign='top'>";
		if ($pdata['all_data'] == 1) {
			echo "<input type='checkbox' name='all_data' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='all_data' value='1'>";
		}
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Show Stats for this Player:</td>\n";
		echo "<td align='left' valign='top'>";
		if ($pdata['show_stats'] == 1) {
			echo "<input type='checkbox' name='show_stats' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='show_stats' value='1'>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='hidden' name='player_id' value='".$pdata['player_id']."'>\n";
		echo "<input type='submit' name='modify_submit' value='Modify Player'>\n";
		echo "<input type='submit' name='delete_submit' value='Delete Player'>\n";
		echo "</form>\n";
		echo "<hr width='100%'>\n";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'><b>Already in Squad in Season(s):</b><br>";
		$query = mysqli_query($db_connect, "SELECT
			team_season_names.SeasonName AS season_name,
			team_seasons.SeasonID AS season_id
			FROM team_season_names,team_seasons
			WHERE team_seasons.SeasonID = team_season_names.SeasonID
			AND team_seasons.SeasonPlayerID = '$player_id'
			ORDER BY season_name
		") or die(mysqli_error());
		$all_seasons = mysqli_query($db_connect, "SELECT SeasonID FROM team_season_names") or die(mysqli_error());
		$check_player = mysqli_num_rows($all_seasons);
		mysqli_free_result($all_seasons);

		$check_players = mysqli_num_rows($query);
		$check_seasons = '';
		while($data = mysqli_fetch_array($query)) {
			echo "".$data['season_name']."<br>\n";
			$check_season[] = $data['season_id'];
			$check_seasons .= "<option value='".$data['season_id']."'>".$data['season_name']."</option>\n";
		}

		if ($check_player != $check_players) {
			$i = 0;
			echo "<br>Copy this Player to Season: <select name='copy_season'>";
			$get_seasons = mysqli_query($db_connect, "SELECT * FROM team_season_names ORDER BY SeasonName") or die(mysqli_error());
			while($data = mysqli_fetch_array($get_seasons)) {
				if ($data['SeasonID'] != $check_season[$i]) {
					echo "<option value='".$data['SeasonID']."'>".$data['SeasonName']."</option>\n";
				} else {
					$i++;
				}
			}
			mysqli_free_result($get_seasons);

			echo "</select>\n";
			echo "<input type='submit' name='copy_season_submit' value='Copy'><br><br>\n";
			echo "You may Remove Player from Season: <select name='remove_season'>".$check_seasons."</select>\n";
			echo "<input type='submit' name='remove_season_submit' value='Remove'>\n";
			echo "<input type='hidden' name='player_id' value='".$player_id."'>\n";
			echo "</form>\n";
		} else if ($check_player == $check_players) {
			echo "You may Remove Player from Season: <select name='remove_season'>".$check_seasons."</select>\n";
			echo "<input type='submit' name='remove_season_submit' value='Remove'>\n";
			echo "<input type='hidden' name='player_id' value='".$player_id."'>\n";
			echo "</form>\n";
		}
		mysqli_free_result($query);

		echo "<hr width='100%'>\n";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<input type='hidden' name='player_id' value='".$player_id."'>\n";
		echo "<b>Add this Player to some Opponent Team</b><br><br>\n";
		$query = mysqli_query($db_connect, "SELECT
			OpponentName AS opponent_name,
			OpponentID AS opponent_id
			FROM team_opponents
			ORDER by opponent_name
		") or die(mysqli_error());
		echo "<select name='opponent_id'>";
		while($data = mysqli_fetch_array($query)) {
			echo "<option value='".$data['opponent_id']."'>".$data['opponent_name']."</option>\r\n";
		}
		mysqli_free_result($query);

		echo "</select>\n";
		echo "<input type='submit' name='add_opponent_player' value='Add'>\n";
		echo "</form>\n";
		$query = mysqli_query($db_connect, "SELECT
			O.OpponentID AS opponent_id,
			O.OpponentName AS opponent_name
			FROM team_opponents AS O, team_players_opponent AS OP
			WHERE O.OpponentID = OP.OpponentID
			AND OP.PlayerID = '$player_id'
			ORDER BY opponent_name
		") or die(mysqli_error());

		if (mysqli_num_rows($query) == 0) {
			echo "This Player has not played for any Opponent Team...<br>";
		} else {
			echo "This Player has been added to these Opponent Teams:<br><br>";
			while($data = mysqli_fetch_array($query)) {
				echo "".$data['opponent_name']." <a href='remove_opponent_player.php?session_id=".$session."&amp;player_id=".$player_id."&amp;opponent_id=".$data['opponent_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>\r\n";
			}
		}
		echo "<hr width='100%'>\n";
		echo "<form enctype='multipart/form-data' method='post' action='image_upload.php?'session_id=".$session."'>\n";
		echo "<b>Upload Player Picture</b><br>\n";
		echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
		echo "<input name='image_file' type='file'>\n";
		echo "<input name='action' type='hidden' value='1'>\n";
		echo "<input type='submit' name='submit' value='Upload'>\n";
		echo "<input type='hidden' name='player_id' value='".$player_id."'>\n";
		echo "</form>\n";
		$image_url = "../images/".$player_id.".jpg";
		$image_url2 = "../images/".$player_id.".png";

		if (file_exists($image_url)) {
			echo "<img src='".$image_url."' alt=''>";
			echo"<br><a href='delete_picture.php?session_id=".$session."&amp;player_id=".$player_id."&amp;action=1&amp;type=jpg'>Delete this Picture</a>";
		} else if (file_exists($image_url2)) {
			echo "<img src='".$image_url2."' alt=''>";
			echo"<br><a href='delete_picture.php?session_id=".$session."&amp;player_id=".$player_id."&amp;action=1&amp;type=png'>Delete this Picture</a>";
		} else {
			echo "<img src='../images/no_image.png' alt='' width='100' height='100'>";
		}

		if (file_exists($image_url) || file_exists($image_url)) {
			echo "<br><br>\n";
			echo "<form enctype='multipart/form-data' method='post' action='image_upload.php?'session_id=".$session."'>\n";
			echo "<b>Upload Second Player Picture</b><br>\n";
			echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
			echo "<input name='image_file' type='file'>\n";
			echo "<input name='action' type='hidden' value='2'>\n";
			echo "<input type='submit' name='submit' value='Upload'>\n";
			echo "<input type='hidden' name='player_id' value='".$player_id."'>\n";
			echo "</form>\n";
			$image_url3 = "../images/".$player_id."_1.jpg";
			$image_url4 = "../images/".$player_id."_1.png";

			if (file_exists($image_url3)) {
				echo "<img src='".$image_url3."' alt=''>";
				echo "<br><a href='delete_picture.php?session_id=".$session."&amp;player_id=".$player_id."&amp;action=2&amp;type=jpg'>Delete this Picture</a>";
			} else if(file_exists($image_url4)) {
				echo "<img src='".$image_url4."' alt=''>";
				echo "<br><a href='delete_picture.php?session_id=".$session."&amp;player_id=".$player_id."&amp;action=2&amp;type=png'>Delete this Picture</a>";
			} else {
				echo "<img src='../images/no_image.png' alt='' width='100' height='100'>";
			}
			echo "<br><br>\n";
			echo "<form enctype='multipart/form-data' method='post' action='image_upload.php?'session_id=$session'>\n";
			echo "<b>Upload Third Player Picture</b><br>\n";
			echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
			echo "<input name='image_file' type='file'>\n";
			echo "<input name='action' type='hidden' value='3'>\n";
			echo "<input type='submit' name='submit' value='Upload'>\n";
			echo "<input type='hidden' name='player_id' value='".$player_id."'>\n";
			echo "</form>\n";
			$image_url5 = "../images/".$player_id."_2.jpg";
			$image_url6 = "../images/".$player_id."_2.png";

			if (file_exists($image_url5)) {
				echo "<img src='".$image_url5."' alt=''>";
				echo "<br><a href='delete_picture.php?session_id=".$session."&amp;player_id=".$player_id."&amp;action=3&amp;type=jpg'>Delete this Picture</a>";
			} else if(file_exists($image_url6)) {
				echo "<img src='".$image_url6."' alt=''>";
				echo"<br><a href='delete_picture.php?session_id=".$session."&amp;player_id=".$player_id."&amp;action=3&amp;type=png'>Delete this Picture</a>";
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
	$get_players = mysqli_query($db_connect, "SELECT
		team_players.PlayerID AS player_id,
		CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
		team_players.PlayerPublish AS player_publish,
		team_players.PlayerNumber AS player_number
		FROM team_players, team_seasons
		WHERE team_players.PlayerID = team_seasons.SeasonPlayerID
		AND team_seasons.SeasonID = '$season_id'
		ORDER BY player_number
	") or die(mysqli_error());
	$get_total = mysqli_num_rows($get_players);

	if ($get_total < 1) {
		echo "<b>No Players: ".$season_name."</b>";
	} else {
		echo "<b>Player Squad: ".$season_name."</b><br><br>Number of Players: ".$get_total."<br><br>";
		while($data = mysqli_fetch_array($get_players)) {
		echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;player_id=".$data['player_id']."'>#".$data['player_number']." ".$data['player_name']."</a>";
			if ($data['player_publish'] == 0) {
				echo " (NB)<br>\n";
			} else {
				echo "<br>\n";
			}
		}
	}
	echo "<br><br>NB = This Player is not Published yet.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>