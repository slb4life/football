<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

$script_name = "matches.php?".$_SERVER['QUERY_STRING'];

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
	if (isset($_REQUEST['match_id'])){ $match_id = $_REQUEST['match_id']; }
	if (isset($_POST['add_submit'])){ $add_submit = $_POST['add_submit']; }
	if (isset($_POST['modify_submit'])){ $modify_submit = $_POST['modify_submit']; }
	if (isset($_POST['delete_submit'])){ $delete_submit = $_POST['delete_submit']; }
	if (isset($_POST['confirm_delete_submit'])){ $confirm_delete_submit = $_POST['confirm_delete_submit']; }

	if (isset($add_submit)) {
		$year = $_POST['year'];
		$month = $_POST['month'];
		$day = $_POST['day'];
		$hour = $_POST['hour'];
		$minute = $_POST['minute'];
		$match_opponent = trim($_POST['match_opponent']);
		$match_place_id = $_POST['match_place_id'];
		if (isset($_POST['match_neutral'])){ $match_neutral = $_POST['match_neutral']; }
		$match_stadium = trim($_POST['match_stadium']);
		$match_type_id = $_POST['match_type_id'];
		$match_additional_type = trim($_POST['match_additional_type']);
		$match_referee = trim($_POST['match_referee']);
		$match_attendance = trim($_POST['match_attendance']);
		$goals = trim($_POST['goals']);
		$goals_opponent = trim($_POST['goals_opponent']);
		if (isset($_POST['match_overtime'])){ $match_overtime = $_POST['match_overtime']; }
		if (isset($_POST['match_penalty_shootout'])){ $match_penalty_shootout = $_POST['match_penalty_shootout']; }
		$penalty_goals = trim($_POST['penalty_goals']);
		$penalty_goals_opponent = trim($_POST['penalty_goals_opponent']);
		$match_report = str_replace("\r\n",'<br>', trim($_POST['match_report']));
		$publish = $_POST['publish'];
		$match_date = $year."-".$month."-".$day." ".$hour."-".$minute."-00";

		if (!get_magic_quotes_gpc()) {
		    $match_additional_type = addslashes($match_additional_type);
		    $match_referee = addslashes($match_referee);
		    $match_report = addslashes($match_report);
		    $match_stadium = addslashes($match_stadium);
		}
		if (!isset($match_neutral)){ $match_neutral = 0; }
		if (!isset($match_overtime)){ $match_overtime = 0; }
		if (!isset($match_penalty_shootout)){ $match_penalty_shootout = 0; }
		if (!isset($publish)){ $publish = 0; }

		if ($match_opponent != '') {
			$add = "INSERT INTO team_matches SET
				MatchSeasonID = '$season_id',
				MatchDateTime = '$match_date',
				MatchTypeID = '$match_type_id',
				MatchAdditionalType = '$match_additional_type',
				MatchOpponent = '$match_opponent',
				MatchPlaceID = '$match_place_id',
				MatchNeutral = '$match_neutral',
				MatchStadium = '$match_stadium',
			";
			if ($goals == '' || $goals_opponent == '') {
				$add .= "MatchGoals = NULL,";
				$add .= "MatchGoalsOpponent = NULL,";
			} else {
				$add .= "MatchGoals = '$goals',";
				$add .= "MatchGoalsOpponent = '$goals_opponent',";
			}
			if ($penalty_goals == '' || $penalty_goals_opponent == '') {
				$add .= "MatchPenaltyGoals = NULL,";
				$add .= "MatchPenaltyGoalsOpponent = NULL,";
			} else {
				$add .= "MatchPenaltyGoals = '$penalty_goals',";
				$add .= "MatchPenaltyGoalsOpponent = '$penalty_goals_opponent',";
			}
			if (!is_numeric($match_attendance) || $match_attendance == '') {
				$add .= "MatchAttendance = NULL,";
			} else {
				$add .= "MatchAttendance = '$match_attendance',";
			}
			$add .= "MatchReferee = '$match_referee',
				MatchReport = '$match_report',
				MatchOvertime = '$match_overtime',
				MatchPenaltyShootout  = '$match_penalty_shootout',
				MatchPublish = '$publish'
			";
			mysqli_query($db_connect, "$add") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");

	} else if (isset($modify_submit)) {
		$match_id = $_POST['match_id'];
		$year = $_POST['year'];
		$month = $_POST['month'];
		$day = $_POST['day'];
		$hour = $_POST['hour'];
		$minute = $_POST['minute'];
		$match_opponent = trim($_POST['match_opponent']);
		$match_place_id = $_POST['match_place_id'];
		if (isset($_POST['match_neutral'])){ $match_neutral = $_POST['match_neutral']; }
		$match_stadium = trim($_POST['match_stadium']);
		$match_type_id = $_POST['match_type_id'];
		$match_additional_type = trim($_POST['match_additional_type']);
		$match_referee = trim($_POST['match_referee']);
		$match_attendance = trim($_POST['match_attendance']);
		$goals = trim($_POST['goals']);
		$goals_opponent = trim($_POST['goals_opponent']);
		if (isset($_POST['match_overtime'])){ $match_overtime = $_POST['match_overtime']; }
		if (isset($_POST['match_penalty_shootout'])){ $match_penalty_shootout = $_POST['match_penalty_shootout']; }
		$penalty_goals = trim($_POST['penalty_goals']);
		$penalty_goals_opponent = trim($_POST['penalty_goals_opponent']);
		$shots = trim($_POST['shots']);
		$shots_opponent = trim($_POST['shots_opponent']);
		$shots_on_goal = trim($_POST['shots_on_goal']);
		$shots_on_goal_opponent = trim($_POST['shots_on_goal_opponent']);
		$offsides = trim($_POST['offsides']);
		$offsides_opponent = trim($_POST['offsides_opponent']);
		$corners = trim($_POST['corners']);
		$corners_opponent = trim($_POST['corners_opponent']);
		$freekicks = trim($_POST['freekicks']);
		$freekicks_opponent = trim($_POST['freekicks_opponent']);
		$penalties = trim($_POST['penalties']);
		$penalties_opponent = trim($_POST['penalties_opponent']);
		$publish = $_POST['publish'];
		$publish_optional = $_POST['publish_optional'];
		$match_report = str_replace("\r\n",'<br>', trim($_POST['match_report']));
		$opening_opponent = str_replace("\r\n",'<br>', trim($_POST['opening_opponent']));
		$substitutes_opponent = str_replace("\r\n",'<br>', trim($_POST['substitutes_opponent']));
		$substitutions_opponent = str_replace("\r\n",'<br>', trim($_POST['substitutions_opponent']));
		$goal_scorers_opponent = str_replace("\r\n",'<br>', trim($_POST['goal_scorers_opponent']));
		$yellow_cards_opponent = str_replace("\r\n",'<br>', trim($_POST['yellow_cards_opponent']));
		$red_cards_opponent = str_replace("\r\n",'<br>', trim($_POST['red_cards_opponent']));
		$match_date = $year."-".$month."-".$day." ".$hour."-".$minute."-00";
		
		if (!get_magic_quotes_gpc()) {
			$match_additional_type = addslashes($match_additional_type);
			$match_referee = addslashes($match_referee);
			$match_report = addslashes($match_report);
			$match_stadium = addslashes($match_stadium);
			$opening_opponent = addslashes($opening_opponent);
			$substitutes_opponent = addslashes($substitutes_opponent);
			$substitutions_opponent = addslashes($substitutions_opponent);
			$goal_scorers_opponent = addslashes($goal_scorers_opponent);
			$yellow_cards_opponent = addslashes($yellow_cards_opponent);
			$red_cards_opponent = addslashes($red_cards_opponent);
		}
		if (!isset($match_neutral)){ $match_neutral = 0; }
		if (!isset($match_overtime)){ $match_overtime = 0; }
		if (!isset($match_penalty_shootout)){ $match_penalty_shootout = 0; }
		if (!isset($publish)){ $publish = 0; }
		if (!isset($publish_optional)){ $publish_optional = 0; }

		if ($match_opponent != '') {
			$modify = "UPDATE team_matches SET
				MatchDateTime = '$match_date',
				MatchTypeID = '$match_type_id',
				MatchAdditionalType = '$match_additional_type',
				MatchOpponent = '$match_opponent',
				MatchPlaceID = '$match_place_id',
				MatchNeutral = '$match_neutral',
				MatchStadium = '$match_stadium',
			";
			if ($goals == '' || $goals_opponent == '') {
				$modify .= "MatchGoals = NULL,";
				$modify .= "MatchGoalsOpponent = NULL,";
			} else {
				$modify .= "MatchGoals = '$goals',";
				$modify .= "MatchGoalsOpponent = '$goals_opponent',";
			}
			if ($penalty_goals == '' || $penalty_goals_opponent == '') {
				$modify .= "MatchPenaltyGoals = NULL,";
				$modify .= "MatchPenaltyGoalsOpponent = NULL,";
			} else {
				$modify .= "MatchPenaltyGoals = '$penalty_goals',";
				$modify .= "MatchPenaltyGoalsOpponent = '$penalty_goals_opponent',";
			}
			if (!is_numeric($match_attendance) || $match_attendance == '') {
				$modify .= "MatchAttendance = NULL,";
			} else {
				$modify .= "MatchAttendance = '$match_attendance',";
			}
			$modify .= "MatchOvertime = '$match_overtime',
				MatchPenaltyShootout = '$match_penalty_shootout',
				MatchShots = '$shots',
				MatchShotsOpponent = '$shots_opponent',
				MatchShotsOnGoal = '$shots_on_goal',
				MatchShotsOnGoalOpponent = '$shots_on_goal_opponent',
				MatchOffsides = '$offsides',
				MatchOffsidesOpponent = '$offsides_opponent',
				MatchCorners = '$corners',
				MatchCornersOpponent = '$corners_opponent',
				MatchFreekicks = '$freekicks',
				MatchFreekicksOpponent = '$freekicks_opponent',
				MatchPenalties = '$penalties',
				MatchPenaltiesOpponent = '$penalties_opponent',
				MatchReferee = '$match_referee',
				MatchReport = '$match_report',
				MatchPublish = '$publish',
				MatchPublishOptional = '$publish_optional',
				MatchOpeningOpponent = '$opening_opponent',
				MatchSubstitutesOpponent = '$substitutes_opponent',
				MatchSubstitutionsOpponent = '$substitutions_opponent',
				MatchGoalscorersOpponent = '$goal_scorers_opponent',
				MatchYellowCardsOpponent = '$yellow_cards_opponent',
				MatchRedCardsOpponent = '$red_cards_opponent'
				WHERE MatchID = '$match_id'
			";
			mysqli_query($db_connect, "$modify") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");

	} else if (isset($delete_submit)) {
		$match_id = $_POST['match_id'];
		echo "<form method='post' action='$PHP_SELF?session_id=$session'>\n";
		echo "Are you sure you want to delete a match?<br><br>\n";
		echo "<input type='submit' value='Delete Match' name='confirm_delete_submit'>\n";
		echo "<input type='hidden' name='match_id' value='$match_id'>\n";
		echo "</form>\n";
		exit();

	} else if (isset($confirm_delete_submit)) {
		$match_id = $_POST['match_id'];
		mysqli_query($db_connect, "DELETE FROM team_goals WHERE GoalMatchID = '$match_id'") or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_yellow_cards WHERE YellowCardMatchID = '$match_id'") or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_red_cards WHERE RedCardMatchID = '$match_id'") or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_appearances WHERE AppearanceMatchID = '$match_id'") or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_substitutes WHERE SubstituteMatchID = '$match_id'") or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_substitutions WHERE SubstitutionMatchID = '$match_id'") or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_matches WHERE MatchID = '$match_id'") or die(mysqli_error());
		header("Location: $HTTP_REFERER");
	}
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');
	echo "<table align='center' width='800'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>";
	if (!isset($action)) {
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Add Match</h1>\n";
		echo "<table cellspacing='3' cellpadding='3' width='100%' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>*Date and Time:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='day'>";
		for($i = 1 ; $i < 32 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == "01")
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select>\n";
		echo "<select name='month'>\n";
		for($i = 1 ; $i < 13 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == "01")
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select>\n";
		echo "<select name='year'>\n";
		for($i = 1950 ; $i < 2025 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == "2014")
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select><br>\n";
		echo "at\n";
		echo "<select name='hour'>\n";
		for($i = 0 ; $i < 24 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == "19")
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select>\n";
		echo "<select name='minute'>\n";
		for($i = 0 ; $i < 60 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($i == "00")
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select>";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>*Home or Away?</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<input type='radio' name='match_place_id' value='1' CHECKED> Home Match<br>";
		echo "<input type='radio' name='match_place_id' value='2'> Away Match<br>";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>*Opponent:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='match_opponent'>";
		$get_opponents = mysqli_query($db_connect, "SELECT * FROM team_opponents ORDER BY OpponentName") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_opponents)) {
			echo "<option value='".$data['OpponentID']."'>".$data['OpponentName']."</option>\n";
		}
		mysqli_free_result($get_opponents);

		echo "</select>";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Neutral?</td>\n";
		echo "<td align='left' valign='top'><input type='checkbox' name='match_neutral' value='1'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>*Match Type:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='match_type_id'>";
		$get_types = mysqli_query($db_connect, "SELECT * FROM team_match_types ORDER BY MatchTypeName") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_types)) {
			echo "<option value='".$data['MatchTypeID']."'>".$data['MatchTypeName']."</option>\n";
		}
		mysqli_free_result($get_types);

		echo "</select>";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Additional Match Type:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='match_additional_type'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Goals:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='goals' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Goals for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='goals_opponent' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Overtime?</td>\n";
		echo "<td align='left' valign='top'><input type='checkbox' name='match_overtime' value='1'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Penalty Shootout?</td>\n";
		echo "<td align='left' valign='top'><input type='checkbox' name='match_penalty_shootout' value='1'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Penalty Goals:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='penalty_goals' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Penalty Goals for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='penalty_goals_opponent' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Referee:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='match_referee'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Attendance:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='match_attendance'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>City/Stadium:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='match_stadium'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Report:<br><textarea name='match_report' cols='40' rows='15'></textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Published:<input type='checkbox' name='publish' value='1' CHECKED></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'><input type='submit' name='add_submit' value='Add Match'></td>\n";
		echo "</tr>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</form>\n";
	} else if ($action == 'modify') {
		$get_match = mysqli_query($db_connect, "SELECT
			DAYOFMONTH(MatchDateTime) AS day,
			MONTH(MatchDateTime) AS month,
			YEAR(MatchDateTime) AS year,
			HOUR(MatchDateTime) AS hour,
			MINUTE(MatchDateTime) AS minute,
			MatchPlaceID AS match_place_id,
			MatchNeutral AS match_neutral,
			MatchOpponent AS match_opponent,
			MatchStadium AS match_stadium,
			MatchReport AS match_report,
			MatchReferee AS match_referee,
			MatchGoals AS goals,
			MatchGoalsOpponent AS goals_opponent,
			MatchPenaltyGoals AS penalty_goals,
			MatchPenaltyGoalsOpponent AS penalty_goals_opponent,
			MatchOvertime AS match_overtime,
			MatchPenaltyShootout AS match_penalty_shootout,
			MatchShots AS shots,
			MatchShotsOpponent AS shots_opponent,
			MatchShotsOnGoal AS shots_on_goal,
			MatchShotsOnGoalOpponent AS shots_on_goal_opponent,
			MatchOffsides AS offsides,
			MatchOffsidesOpponent AS offsides_opponent,
			MatchCorners AS corners,
			MatchCornersOpponent AS corners_opponent,
			MatchFreekicks AS freekicks,
			MatchFreekicksOpponent AS freekicks_opponent,
			MatchPenalties AS penalties,
			MatchPenaltiesOpponent AS penalties_opponent,
			MatchAttendance AS match_attendance,
			MatchPublish as publish,
			MatchPublishOptional AS publish_optional,
			MatchTypeID AS match_type_id,
			MatchAdditionalType AS match_additional_type,
			MatchOpeningOpponent AS opening_opponent,
			MatchSubstitutesOpponent AS substitutes_opponent,
			MatchSubstitutionsOpponent AS substitutions_opponent,
			MatchGoalscorersOpponent AS goal_scorers_opponent,
			MatchYellowCardsOpponent AS yellow_cards_opponent,
			MatchRedCardsOpponent AS red_cards_opponent
			FROM team_matches
			WHERE MatchID = '$match_id'
			LIMIT 1
		") or die(mysqli_error());
		$match_data = mysqli_fetch_array($get_match);
		mysqli_free_result($get_match);

		$match_data['match_report'] = str_replace('<br>',"\r\n", $match_data['match_report']);
		$match_data['opening_opponent'] = str_replace('<br>',"\r\n", $match_data['opening_opponent']);
		$match_data['substitutes_opponent'] = str_replace('<br>',"\r\n", $match_data['substitutes_opponent']);
		$match_data['substitutions_opponent'] = str_replace('<br>',"\r\n", $match_data['substitutions_opponent']);
		$match_data['goal_scorers_opponent'] = str_replace('<br>',"\r\n", $match_data['goal_scorers_opponent']);
		$match_data['yellow_cards_opponent'] = str_replace('<br>',"\r\n", $match_data['yellow_cards_opponent']);
		$match_data['red_cards_opponent'] = str_replace('<br>',"\r\n", $match_data['red_cards_opponent']);
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
		echo "<b>JUMP TO:</b> [<a href='#your_team'>Your Team Statistics</a>] [<a href='#opp_team'>Opponent Statistics</a>] [<a href='#opp'>Optional Stats</a>]<br>\n";
		echo "<b>CHECK:</b> [<a href='../match_details.php?id=".$match_id."'>Match Details Page</a>]<br>\n";
		echo "<table cellspacing='3' cellpadding='3' width='100%' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top' bgcolor='#99CCFF' colspan='2'><b>Match Info:</b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>*Date and Time:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='day'>";
		for($i = 1 ; $i < 32 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($match_data['day'] == $i)
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select>\n";
		echo "<select name='month'>";
		for($i = 1 ; $i < 13 ; $i++) {
			if($i<10) {
				$i = "0".$i;
			}
			if ($match_data['month'] == $i)
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select>\n";
		echo "<select name='year'>";
		for($i = 1950 ; $i < 2025 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($match_data['year'] == $i)
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select><br>\n";
		echo "at\n";
		echo "<select name='hour'>\n";
		for($i = 0 ; $i < 24 ; $i++) {
			if ($i<10) {
				$i = "0".$i;
			}
			if ($match_data['hour'] == $i)
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select>\n";
		echo "<select name='minute'>";
		for($i = 0 ; $i < 60 ; $i++) {
			if ( $i<10) {
				$i = "0".$i;
			}
			if ($match_data['minute'] == $i)
				echo "<option value='".$i."' SELECTED>".$i."</option>\n";
			else
				echo "<option value='".$i."'>".$i."</option>\n";
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>*Home or Away?</td>\n";
		echo "<td align='left' valign='top'>";
		if ($match_data['match_place_id'] == 1) {
			echo "<input type='radio' name='match_place_id' value='1' CHECKED> Home Match<br>\n";
			echo "<input type='radio' name='match_place_id' value='2'> Away Match<br>\n";
		} else {
			echo "<input type='radio' name='match_place_id' value='1'> Home Match<br>\n";
			echo "<input type='radio' name='match_place_id' value='2' CHECKED> Away Match<br>\n";
		}
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Neutral ?</td>\n";
		echo "<td align='left' valign='top'>";
		if ($match_data['match_neutral'] == 1) {
			echo "<input type='checkbox' name='match_neutral' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='match_neutral' value='1'>";
		}
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>*Opponent:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='match_opponent'>";
		$get_opponents = mysqli_query($db_connect, "SELECT * FROM team_opponents ORDER BY OpponentName") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_opponents)) {
			if ($match_data['match_opponent'] == $data['OpponentID']) {
				echo "<option value='".$data['OpponentID']."' SELECTED>".$data['OpponentName']."</option>\n";
			} else {
				echo "<option value='".$data['OpponentID']."'>".$data['OpponentName']."</option>\n";
			}
		}
		mysqli_free_result($get_opponents);

		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>*Match Type:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='match_type_id'>";
		$get_types = mysqli_query($db_connect, "SELECT * FROM team_match_types ORDER BY MatchTypeName") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_types)) {
			if ($match_data['match_type_id'] == $data['MatchTypeID']) {
				echo "<option value='$data[MatchTypeID]' SELECTED>$data[MatchTypeName]</option>\n";
			} else {
				echo "<option value='$data[MatchTypeID]'>$data[MatchTypeName]</option>\n";
			}
		}
		mysqli_free_result($get_types);

		echo "</select>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Additional Match Type:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='match_additional_type' value='".$match_data['match_additional_type']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Goals:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='goals' value='".$match_data['goals']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Goals for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='goals_opponent' value='".$match_data['goals_opponent']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Overtime?</td>\n";
		echo "<td align='left' valign='top'>";
		if ($match_data['match_overtime'] == 1) {
			echo "<input type='checkbox' name='match_overtime' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='match_overtime' value='1'>";
		}
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Penalty Shootout?</td>\n";
		echo "<td align='left' valign='top'>";
		if ($match_data['match_penalty_shootout'] == 1) {
			echo "<input type='checkbox' name='match_penalty_shootout' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='match_penalty_shootout' value='1'>";
		}
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Penalty Goals:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='penalty_goals' value='".$match_data['penalty_goals']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Penalty Goals for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='penalty_goals_opponent' value='".$match_data['penalty_goals_opponent']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Referee:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='match_referee' value='".$match_data['match_referee']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Attendance:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='match_attendance' value='".$match_data['match_attendance']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>City/Stadium:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='match_stadium' value='".$match_data['match_stadium']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Report:<br><textarea name='match_report' cols='40' rows='15'>".$match_data['match_report']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>";
		echo "<input type='hidden' name='match_id' value='".$match_id."'>Published:";
		if ($match_data['publish'] == 1) {
			echo "<input type='checkbox' name='publish' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='publish' value='1'>";
		}
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'><input type='submit' name='modify_submit' value='Modify Match'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#99CCFF' colspan='2'><b>Deleting this Match</b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Delete this Match?<br>Warning! All the data (Goal Scorers, Opening Line-up etc.) will be lost after pressing the button.</td>\n";
		echo "<td align='left' valign='top'><input type='submit' name='delete_submit' value='Delete Match'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#99CCFF' colspan='2'><b><a name='opp'>Optional Statistics</a></b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Shots:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='shots' value='".$match_data['shots']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Shots for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='shots_opponent' value='".$match_data['shots_opponent']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Shots on Goal:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='shots_on_goal' value='".$match_data['shots_on_goal']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Shots on Goal for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='shots_on_goal_opponent' value='".$match_data['shots_on_goal_opponent']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Offsides:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='offsides' value='".$match_data['offsides']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Offsides for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='offsides_opponent' value='".$match_data['offsides_opponent']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Corner Kicks:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='corners' value='".$match_data['corners']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Corner Kicks for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='corners_opponent' value='".$match_data['corners_opponent']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Free Kicks:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='freekicks' value='".$match_data['freekicks']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Free Kicks for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='freekicks_opponent' value='".$match_data['freekicks_opponent']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Penalty Kicks:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='penalties' value='".$match_data['penalties']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Penalty Kicks for Opponent:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='penalties_opponent' value='".$match_data['penalties_opponent']."' size='3'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Published Optional Statistics:";
		if ($match_data['publish_optional'] == 1) {
			echo "<input type='checkbox' name='publish_optional' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='publish_optional' value='1'>";
		}
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'><input type='submit' name='modify_submit' value='Modify Match'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#99CCFF' colspan='2'><b><a name='opp_team'>Optional Opponent Statistics</a></b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Opening Line-up for Opponent:<br><textarea name='opening_opponent' cols='30' rows='7'>".$match_data['opening_opponent']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Substitutes for Opponent:<br><textarea name='substitutes_opponent' cols='30' rows='5'>".$match_data['substitutes_opponent']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Substitutions for Opponent:<br><textarea name='substitutions_opponent' cols='30' rows='3'>".$match_data['substitutions_opponent']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Goal Scorers for Opponent:<br><textarea name='goal_scorers_opponent' cols='30' rows='3'>".$match_data['goal_scorers_opponent']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Yellow Cards for Opponent:<br><textarea name='yellow_cards_opponent' cols='30' rows='3'>".$match_data['yellow_cards_opponent']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Red Cards for Opponent:<br><textarea name='red_cards_opponent' cols='30' rows='3'>".$match_data['red_cards_opponent']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'><input type='submit' name='modify_submit' value='Modify Match'></form></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'><b><a name='your_team'>Your Team Statistics</a></b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' bgcolor='#99CCFF' colspan='2'><b>Opening Line-up:</b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>";
		$get_players = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_players.PlayerID AS player_id
			FROM team_players,team_seasons
			WHERE team_players.PlayerID = team_seasons.SeasonPlayerID
			AND team_seasons.SeasonID = '$season_id'
			AND team_players.PlayerPositionID != '5'
			ORDER BY player_name
		") or die(mysqli_error());
		echo "<form method='post' action='match_data.php?session_id=".$session."'>\n";
		echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
		echo "(NOTE: hold down CTRL to select more than one.)<br>\n";
		echo "<select name='add_to_squad[]' size='10' multiple='multiple'>";
		while($data = mysqli_fetch_array($get_players)) {
			echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
		}
		echo "</select>\n";
		echo "<input type='submit' name='add_squad_submit' value='Add'><br>\n";
		echo "<input type='hidden' name='match_id' value='".$match_id."'>\n";
		echo "<input type='hidden' name='season_id' value='".$season_id."'>\n";
		$get_squad = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_players.PlayerNumber AS player_number,
			team_appearances.AppearanceID AS appearance_id
			FROM team_players, team_appearances
			WHERE team_players.PlayerID = team_appearances.AppearancePlayerID
			AND team_appearances.AppearanceMatchID = '$match_id'
			AND team_appearances.AppearanceSeasonID = '$season_id'
			ORDER BY player_number
		") or die(mysqli_error());

		if (mysqli_num_rows($get_squad) == 0) {
			echo "No Opening Line-up Players Added Yet.";
		} else {
			while($data = mysqli_fetch_array($get_squad)) {
				echo "#".$data['player_number']." ".$data['player_name']." <a href='match_data.php?session_id=".$session."&amp;action=remove_from_squad&amp;id=".$data['appearance_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>\n";
			}
		}
		mysqli_free_result($get_squad);

		echo "</form>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' bgcolor='#99CCFF' colspan='2'><b>Substitutes:</b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>";
		mysqli_data_seek($get_players, 0);

		echo "<form method='post' action='match_data.php?session_id=".$session."'>\n";
		echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
		echo "(NOTE: hold down CTRL to select more than one.)<br>\n";
		echo "<select name='add_to_substitutes[]' size='10' multiple='multiple'>";
		while($data = mysqli_fetch_array($get_players)) {
			echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
		}
		echo "</select>\n";
		echo "<input type='submit' name='add_substitutes_submit' value='Add'><br>\n";
		echo "<input type='hidden' name='match_id' value='".$match_id."'>\n";
		echo "<input type='hidden' name='season_id' value='".$season_id."'>\n";
		$get_substitutes = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_players.PlayerNumber AS player_number,
			team_substitutes.SubstituteID AS substitute_id
			FROM team_players, team_substitutes
			WHERE team_players.PlayerID = team_substitutes.SubstitutePlayerID
			AND team_substitutes.SubstituteMatchID = '$match_id'
			AND team_substitutes.SubstituteSeasonID = '$season_id'
			ORDER BY player_number
		") or die(mysqli_error());

		if (mysqli_num_rows($get_substitutes) == 0) {
			echo "No Substitutes Added Yet.";
		} else {
			while($data = mysqli_fetch_array($get_substitutes)) {
				echo "#".$data['player_number']." ".$data['player_name']." <a href='match_data.php?session_id=".$session."&amp;action=remove_from_substitutes&amp;id=".$data['substitute_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>\n";
			}
		}
		mysqli_free_result($get_substitutes);
		mysqli_free_result($get_players);

		echo "</form>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' bgcolor='#99CCFF' colspan='2'><b>Substitutions:</b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>";
		$get_opening = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_players.PlayerID AS player_id
			FROM team_players, team_appearances
			WHERE team_players.PlayerID = team_appearances.AppearancePlayerID
			AND team_appearances.AppearanceMatchID = '$match_id'
			AND team_appearances.AppearanceSeasonID = '$season_id'
			ORDER BY player_name
		") or die(mysqli_error());
		$get_sub = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_players.PlayerID AS player_id
			FROM team_players, team_substitutes
			WHERE team_players.PlayerID = team_substitutes.SubstitutePlayerID
			AND team_substitutes.SubstituteMatchID = '$match_id'
			AND team_substitutes.SubstituteSeasonID = '$season_id'
			ORDER BY player_name
		") or die(mysqli_error());
		$get_sub_in = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_players.PlayerID AS player_id
			FROM team_players, team_substitutions
			WHERE team_players.PlayerID = team_substitutions.SubstitutionPlayerIDIn
			AND team_substitutions.SubstitutionMatchID = '$match_id'
			AND team_substitutions.SubstitutionSeasonID = '$season_id'
			ORDER BY player_name
		") or die(mysqli_error());
		echo "<form method='post' action='match_data.php?session_id=".$session."'>\n";
		echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
		echo "Out: <select name='add_to_substitutions_out'>";
		while($data = mysqli_fetch_array($get_opening)) {
			echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
		}
		if (mysqli_num_rows($get_sub_in) > 0) {
			echo "<option value='-'>----------------------</option>\n";
			while($data = mysqli_fetch_array($get_sub_in)) {
				echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
			}
		}
		echo "</select>\n";
		echo "In: <select name='add_to_substitutions_in'>";
		while($data = mysqli_fetch_array($get_sub)) {
			echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
		}
		mysqli_free_result($get_sub);

		echo "</select>\n";
		echo "<br>\n";
		echo "Minute: <input type='text' name='substitution_minute' size='2'>\n";
		echo "<input type='submit' name='add_substitutions_submit' value='Add'><br>\n";
		echo "<input type='hidden' name='match_id' value='".$match_id."'>\n";
		echo "<input type='hidden' name='season_id' value='".$season_id."'>\n";
		$get_substitutions = mysqli_query($db_connect, "SELECT
			CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
			CONCAT(PL.PlayerFirstName, ' ', PL.PlayerLastName) AS player_name2,
			S.SubstitutionMinute AS substitution_minute,
			S.SubstitutionID AS substitute_id
			FROM team_players P, team_players PL, team_substitutions S
			WHERE S.SubstitutionMatchID = '$match_id'
			AND S.SubstitutionSeasonID = '$season_id'
			AND P.PlayerID = S.SubstitutionPlayerIDIn
			AND PL.PlayerID = S.SubstitutionPlayerIDOut
			ORDER BY substitution_minute
		") or die(mysqli_error());

		if (mysqli_num_rows($get_substitutions) == 0) {
			echo "No Substitutions Added Yet.";
		} else {
			while($data = mysqli_fetch_array($get_substitutions)) {
				echo "".$data['player_name']." for ".$data['player_name2']." (".$data['substitution_minute'].")<a href='match_data.php?session_id=".$session."&amp;action=remove_from_substitutions&amp;id=".$data['substitute_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>\n";
			}
		}
		mysqli_free_result($get_substitutions);

		echo "</form>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' bgcolor='#99CCFF' colspan='2'><b>Goal Scorers:</b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>";
		
		if (mysqli_num_rows($get_opening) > 0) {
			mysqli_data_seek($get_opening, 0);
		}
		if (mysqli_num_rows($get_sub_in) > 0) {
			mysqli_data_seek($get_sub_in, 0);
		}
		echo "<form method='post' action='match_data.php?session_id=".$session."'>\n";
		echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
		echo "<select name='add_to_goal_scorers'>";
		while($data = mysqli_fetch_array($get_opening)) {
			echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
		}
		if (mysqli_num_rows($get_sub_in) > 0) {
			echo "<option value='-'>----------------------</option>\n";
			while($data = mysqli_fetch_array($get_sub_in)) {
				echo"<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
			}
		}
		echo "</select>\n";
		echo "Minute: <input type='text' name='goal_minute' size='2'><br>\n";
		echo "Penalty Shot? <input type='checkbox' value='1' name='penalty'><br>\n";
		echo "Own goal? <input type='checkbox' value='1' name='own_goal'><br>\n";
		echo "If Own Goal, Scorer? <input type='text' name='goal_own_scorer'><br>\n";
		echo "<input type='submit' name='add_goal_scorer_submit' value='Add'><br>\n";
		echo "<input type='hidden' name='match_id' value='".$match_id."'>\n";
		echo "<input type='hidden' name='season_id' value='".$season_id."'>\n";
		$get_goal_scorers = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_goals.GoalMinute AS goal_minute,
			team_goals.GoalID AS goal_id,
			team_goals.GoalPenalty AS goal_penalty,
			team_goals.GoalOwn AS goal_own,
			team_goals.GoalOwnScorer AS goal_own_scorer
			FROM team_players, team_goals
			WHERE team_players.PlayerID = team_goals.GoalPlayerID
			AND team_goals.GoalMatchID = '$match_id'
			AND team_goals.GoalSeasonID = '$season_id'
			ORDER BY goal_minute
		") or die(mysqli_error());

		if (mysqli_num_rows($get_goal_scorers) == 0) {
			echo "<br>No Goals Added Yet.";
		} else {
			while($data = mysqli_fetch_array($get_goal_scorers)) {
				if ($data['goal_own'] == 1) {
					echo "".$data['goal_own_scorer']." (O.G.) (".$data['goal_minute'].") <a href='match_data.php?session_id=".$session."&amp;action=remove_from_goal_scorers&amp;id=".$data['goal_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>\n";
				} else if ($data['goal_penalty'] == 1) {
					echo "".$data['player_name']." (Penalty) (".$data['goal_minute'].") <a href='match_data.php?session_id=".$session."&amp;action=remove_from_goal_scorers&amp;id=".$data['goal_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>\n";
				} else {
					echo "".$data['player_name']." (".$data['goal_minute'].") <a href='match_data.php?session_id=".$session."&amp;action=remove_from_goal_scorers&amp;id=".$data['goal_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>\n";
				}
			}
		}
		mysqli_free_result($get_goal_scorers);

		echo "</form>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top' bgcolor='#99CCFF' colspan='2'><b>Assists:</b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>\n";
		echo "<form method='post' action='match_data.php?session_id=".$session."'>\n";
		echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
		$get_goal_scorers = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_players.PlayerID AS player_id,
			team_goals.GoalID AS goal_id,
			team_goals.GoalMinute AS goal_minute
			FROM team_players, team_goals
			WHERE team_players.PlayerID = team_goals.GoalPlayerID
			AND team_goals.GoalMatchID = '$match_id'
			AND team_goals.GoalSeasonID = '$season_id'
			AND team_goals.GoalOwn = 0
			AND team_goals.GoalPenalty = 0
			ORDER BY goal_minute
		") or die(mysqli_error());

		if (mysqli_num_rows($get_goal_scorers) > 0) {
			echo "<select name='add_to_assists_scorer'>";
			while($data = mysqli_fetch_array($get_goal_scorers)) {
				echo "<option value='".$data['player_id']."'>".$data['player_name']." (".$data['goal_minute'].")</option>\n";
			}
			echo "</select>\n Assisted by ";

			if (mysqli_num_rows($get_opening) > 0) {
				mysqli_data_seek($get_opening, 0);
			}
			if (mysqli_num_rows($get_sub_in) > 0) {
				mysqli_data_seek($get_sub_in, 0);
			}
			echo "<select name='add_to_assists'>";
			while($data = mysqli_fetch_array($get_opening)) {
				echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
			}
			if (mysqli_num_rows($get_sub_in) > 0) {
				echo "<option value='-'>----------------------</option>\n";
				while($data = mysqli_fetch_array($get_sub_in)) {
					echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
				}
			}
			echo "</select> <input type='submit' name='add_assist_submit' value='Add'><br>\n";
			echo "<input type='hidden' name='match_id' value='".$match_id."'>\n";
			echo "<input type='hidden' name='season_id' value='".$season_id."'>\n";
		} else {
			echo "No Goals In This Match...";
		}
		mysqli_free_result($get_goal_scorers);

		echo "</form>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top' bgcolor='#99CCFF' colspan='2'><b>Yellow Cards:</b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>";

		if (mysqli_num_rows($get_opening) > 0) {
			mysqli_data_seek($get_opening, 0);
		}
		if (mysqli_num_rows($get_sub_in) > 0) {
			mysqli_data_seek($get_sub_in, 0);
		}
		echo "<form method='post' action='match_data.php?session_id=".$session."'>\n";
		echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
		echo "<select name='add_to_yellow_cards'>";
		while($data = mysqli_fetch_array($get_opening)) {
			echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
		}
		if (mysqli_num_rows($get_sub_in) > 0) {
			echo "<option value='-'>----------------------</option>\n";
			while($data = mysqli_fetch_array($get_sub_in)) {
				echo"<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
			}
		}
		echo "</select>\n";
		echo "Minute: <input type='text' name='yellow_card_minute' size='2'>\n";
		echo "<input type='submit' name='add_yellow_card_submit' value='Add'><br>\n";
		echo "<input type='hidden' name='match_id' value='".$match_id."'>\n";
		echo "<input type='hidden' name='season_id' value='".$season_id."'>";
		$get_yellow_cards = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_yellow_cards.YellowCardMinute AS yellow_card_minute,
			team_yellow_cards.YellowCardID AS yellow_card_id
			FROM team_players, team_yellow_cards
			WHERE team_players.PlayerID = team_yellow_cards.YellowCardPlayerID
			AND team_yellow_cards.YellowCardMatchID = '$match_id'
			AND team_yellow_cards.YellowCardSeasonID = '$season_id'
			ORDER BY yellow_card_minute
		") or die(mysqli_error());

		if (mysqli_num_rows($get_yellow_cards) == 0) {
			echo "No Yellow Cards Added Yet.";
		} else {
			while($data = mysqli_fetch_array($get_yellow_cards)) {
				echo "".$data['player_name']." (".$data['yellow_card_minute'].") <a href='match_data.php?session_id=".$session."&amp;action=remove_from_yellow_cards&amp;id=".$data['yellow_card_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>\n";
			}
		}
		mysqli_free_result($get_yellow_cards);

		echo "</form>\n";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' bgcolor='#99CCFF' colspan='2'><b>Red Cards:</b></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>";

		if (mysqli_num_rows($get_opening) > 0) {
			mysqli_data_seek($get_opening, 0);
		}
		if (mysqli_num_rows($get_sub_in) > 0) {
			mysqli_data_seek($get_sub_in, 0);
		}
		echo "<form method='post' action='match_data.php?session_id=".$session."'>\n";
		echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
		echo "<select name='add_to_red_cards'>";
		while($data = mysqli_fetch_array($get_opening)) {
			echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
		}
		if (mysqli_num_rows($get_sub_in) > 0) {
			echo "<option value='-'>----------------------</option>\n";
			while($data = mysqli_fetch_array($get_sub_in)) {
				echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
			}
		}
		mysqli_free_result($get_opening);
		mysqli_free_result($get_sub_in);

		echo "</select>\n";
		echo "Minute: <input type='text' name='red_card_minute' size='2'>\n";
		echo "<input type='submit' name='add_red_card_submit' value='Add'><br>\n";
		echo "<input type='hidden' name='match_id' value='".$match_id."'>\n";
		echo "<input type='hidden' name='season_id' value='".$season_id."'>\n";
		$get_red_cards = mysqli_query($db_connect, "SELECT
			CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
			team_red_cards.RedCardMinute AS red_card_minute,
			team_red_cards.RedCardID as red_card_id
			FROM team_players, team_red_cards
			WHERE team_players.PlayerID = team_red_cards.RedCardPlayerID
			AND team_red_cards.RedCardMatchID = '$match_id'
			AND team_red_cards.RedCardSeasonID = '$season_id'
			ORDER BY red_card_minute
		") or die(mysqli_error());

		if (mysqli_num_rows($get_red_cards) == 0) {
			echo "No Red Cards Added Yet.";
		} else {
			while($data = mysqli_fetch_array($get_red_cards)) {
				echo "".$data['player_name']." (".$data['red_card_minute'].") <a href='match_data.php?session_id=".$session."&amp;action=remove_from_red_cards&amp;id=".$data['red_card_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>\n";
			}
		}
		mysqli_free_result($get_red_cards);

		echo "</form>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
	echo "</td>\n";
	echo "<td align='left' valign='top' width='50%'>";
	$get_matches = mysqli_query($db_connect, "SELECT
		DATE_FORMAT(team_matches.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
		team_opponents.OpponentName AS opponent_name,
		team_matches.MatchID AS match_id,
		team_match_types.MatchTypeName AS match_type_name,
		team_matches.MatchStadium AS match_stadium,
		team_match_places.MatchPlaceName AS match_place_name,
		team_matches.MatchPublish AS publish,
		team_matches.MatchNeutral AS match_neutral
		FROM team_matches, team_match_types, team_match_places, team_opponents
		WHERE MatchSeasonID = '$season_id'
		AND team_matches.MatchTypeID = team_match_types.MatchTypeID
		AND team_matches.MatchPlaceID = team_match_places.MatchPlaceID
		AND team_matches.MatchOpponent = team_opponents.OpponentID
		ORDER BY match_date
	") or die(mysqli_error());

	if (mysqli_num_rows($get_matches) <1) {
		echo "<b>No Matches: ".$season_name."</b>";
	} else {
		echo "<b>Matches in ".$season_name.":</b><br><br>";
		while($data = mysqli_fetch_array($get_matches)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;match_id=".$data['match_id']."'>".$data['match_date'].", vs. ".$data['opponent_name']."</a><br>".$data['match_place_name']."";

			if ($data['match_neutral'] == 1) {
				echo "(Neutral)";
			}
			echo ": ".$data['match_type_name']."";

			if ($data['publish'] == 1) {
				echo "<br><br>\n";
			} else {
				echo " (NB)<br><br>\n";
			}
		}
	}
	echo "<br><br>\n";
	echo "NB = This Match Is Not Published Yet.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>