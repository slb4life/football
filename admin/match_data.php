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

	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];

	if (isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
	if (isset($_POST['add_squad_submit'])){ $add_squad_submit = $_POST['add_squad_submit']; }
	if (isset($_POST['add_goal_scorer_submit'])){ $add_goal_scorer_submit = $_POST['add_goal_scorer_submit']; }
	if (isset($_POST['add_goal_assist_submit'])){ $add_goal_assist_submit = $_POST['add_goal_assist_submit']; }
	if (isset($_POST['add_yellow_card_submit'])){ $add_yellow_card_submit = $_POST['add_yellow_card_submit']; }
	if (isset($_POST['add_red_card_submit'])){ $add_red_card_submit = $_POST['add_red_card_submit']; }
	if (isset($_POST['add_substitutes_submit'])){ $add_substitutes_submit = $_POST['add_substitutes_submit']; }
	if (isset($_POST['add_substitutions_submit'])){ $add_substitutions_submit = $_POST['add_substitutions_submit']; }

	if (isset($add_squad_submit)) {
		$add_to_squad = $_POST['add_to_squad'];
		$match_id = $_POST['match_id'];
		$season_id = $_POST['season_id'];
		foreach($add_to_squad as $player_id) {
			$get_squad = mysqli_query($db_connect, "SELECT
				team_appearances.AppearancePlayerID AS player_id
				FROM team_appearances
				WHERE team_appearances.AppearancePlayerID = '$player_id'
				AND team_appearances.AppearanceMatchID = '$match_id'
				AND team_appearances.AppearanceSeasonID = '$season_id'
			") or die(mysqli_error());

			if (mysqli_num_rows($get_squad) == 0) {
				mysqli_query($db_connect, "INSERT INTO team_appearances SET
					AppearancePlayerID = '$player_id',
					AppearanceMatchID = '$match_id',
					AppearanceSeasonID = '$season_id'
				") or die(mysqli_error());
			}
			mysqli_free_result($get_squad);
		}
		header("Location: $HTTP_REFERER");
		
	} else if (isset($add_goal_scorer_submit)) {
		$player_id = $_POST['add_to_goal_scorers'];
		$match_id = $_POST['match_id'];
		$season_id = $_POST['season_id'];
		$goal_minute = trim($_POST['goal_minute']);
		if (isset($_POST['own_goal'])){ $own_goal = $_POST['own_goal']; }
		if (isset($_POST['penalty'])){ $penalty = $_POST['penalty']; }
		$goal_own_scorer = trim($_POST['goal_own_scorer']);

		if (isset($own_goal) && isset($penalty)) {
			header("Location: $HTTP_REFERER");
			exit();
		}

		$query = mysqli_query($db_connect, "SELECT
			team_substitutes.SubstitutePlayerID AS substitute_player_id
			FROM team_substitutes,team_appearances
			WHERE (team_substitutes.SubstitutePlayerID = '$player_id'
			AND team_substitutes.SubstituteMatchID = '$match_id'
			AND team_substitutes.SubstituteSeasonID = '$season_id')
			OR (team_appearances.AppearancePlayerID = '$player_id'
			AND team_appearances.AppearanceMatchID = '$match_id'
			AND team_appearances.AppearanceSeasonID = '$season_id')
		") or die(mysqli_error());

		if (mysqli_num_rows($query) == 0) {
			echo "Please Add Player to Open Line-up or Substitutions First.<br>Push Back Button to get Back.";
			exit();
		} else {
			if (isset($penalty)) {
				mysqli_query($db_connect, "INSERT INTO team_goals SET
					GoalPlayerID = '$player_id',
					GoalMatchID = '$match_id',
					GoalSeasonID = '$season_id',
					GoalPenalty = '1',
					GoalMinute = '$goal_minute',
					GoalOwnScorer = ''
				") or die(mysqli_error());
				header("Location: $HTTP_REFERER");

			} else if (isset($own_goal)) {
				mysqli_query($db_connect, "INSERT INTO team_goals SET
					GoalPlayerID = '$player_id',
					GoalMatchID = '$match_id',
					GoalSeasonID = '$season_id',
					GoalOwn = '1',
					GoalMinute = '$goal_minute',
					GoalOwnScorer = '$goal_own_scorer'
				") or die(mysqli_error());
				header("Location: $HTTP_REFERER");

			} else {
				mysqli_query($db_connect, "INSERT INTO team_goals SET
					GoalPlayerID = '$player_id',
					GoalMatchID = '$match_id',
					GoalSeasonID = '$season_id',
					GoalMinute = '$goal_minute',
					GoalOwnScorer = ''
				") or die(mysqli_error());
				header("Location: $HTTP_REFERER");
			}
		}
		mysqli_free_result($query);

	} else if (isset($add_goal_assist_submit)) {
		$player_id = $_POST['add_to_goal_assists'];
		$match_id = $_POST['match_id'];
		$season_id = $_POST['season_id'];
		$goal_assist_minute = $_POST['add_goal_assist_minute'];
		$query = mysqli_query($db_connect, "SELECT
			team_substitutes.SubstitutePlayerID AS substitute_player_id
			FROM team_substitutes,team_appearances
			WHERE (team_substitutes.SubstitutePlayerID = '$player_id'
			AND team_substitutes.SubstituteMatchID = '$match_id'
			AND team_substitutes.SubstituteSeasonID = '$season_id')
			OR (team_appearances.AppearancePlayerID = '$player_id'
			AND team_appearances.AppearanceMatchID = '$match_id'
			AND team_appearances.AppearanceSeasonID = '$season_id')
		") or die(mysqli_error());

		if (mysqli_num_rows($query) == 0) {
			echo "Please Add Player to Open Line-up or Substitutions first.<br>Push Back Button to get Back.";
			exit();
		} else {
			mysqli_query($db_connect, "INSERT INTO team_goal_assists SET
				GoalAssistPlayerID = '$player_id',
				GoalAssistMatchID = '$match_id',
				GoalAssistSeasonID = '$season_id',
				GoalAssistMinute = '$goal_assist_minute'
			") or die(mysqli_error($db_connect));
			header("Location: $HTTP_REFERER");
		}
		mysqli_free_result($query);

	} else if (isset($add_yellow_card_submit)) {
		$player_id = $_POST['add_to_yellow_cards'];
		$match_id = $_POST['match_id'];
		$season_id = $_POST['season_id'];
		$yellow_card_minute = $_POST['yellow_card_minute'];
		$query = mysqli_query($db_connect, "SELECT
			team_substitutes.SubstitutePlayerID AS substitute_player_id
			FROM team_substitutes,team_appearances
			WHERE (team_substitutes.SubstitutePlayerID = '$player_id'
			AND team_substitutes.SubstituteMatchID = '$match_id'
			AND team_substitutes.SubstituteSeasonID = '$season_id')
			OR (team_appearances.AppearancePlayerID = '$player_id'
			AND team_appearances.AppearanceMatchID = '$match_id'
			AND team_appearances.AppearanceSeasonID = '$season_id')
		") or die(mysqli_error());

		if (mysqli_num_rows($query) == 0) {
			echo "Please Add Player to Open Line-up or Substitutions first.<br>Push Back Button to get Back.";
			exit();
		} else {
			mysqli_query($db_connect, "INSERT INTO team_yellow_cards SET
				YellowCardPlayerID = '$player_id',
				YellowCardMatchID = '$match_id',
				YellowCardSeasonID = '$season_id',
				YellowCardMinute = '$yellow_card_minute'
			") or die(mysqli_error());
			header("Location: $HTTP_REFERER");
		}
		mysqli_free_result($query);

	} else if (isset($add_red_card_submit)) {
		$player_id = $_POST['add_to_red_cards'];
		$match_id = $_POST['match_id'];
		$season_id = $_POST['season_id'];
		$red_card_minute = $_POST['red_card_minute'];
		$query = mysqli_query($db_connect, "SELECT
			team_substitutes.SubstitutePlayerID AS substitute_player_id
			FROM team_substitutes,team_appearances
			WHERE (team_substitutes.SubstitutePlayerID = '$player_id'
			AND team_substitutes.SubstituteMatchID = '$match_id'
			AND team_substitutes.SubstituteSeasonID = '$season_id')
			OR (team_appearances.AppearancePlayerID = '$player_id'
			AND team_appearances.AppearanceMatchID = '$match_id'
			AND team_appearances.AppearanceSeasonID = '$season_id')
		") or die(mysqli_error());

		if (mysqli_num_rows($query) == 0) {
			echo "Please Add Player to Open Line-up or Substitutions first.<br>Push Back Button to get Back.";
			exit();
		} else {
			mysqli_query($db_connect, "INSERT INTO team_red_cards SET
				RedCardPlayerID = '$player_id',
				RedCardMatchID = '$match_id',
				RedCardSeasonID = '$season_id',
				RedCardMinute = '$red_card_minute'
			") or die(mysqli_error());
			header("Location: $HTTP_REFERER");
		}
		mysqli_free_result($query);

	} else if (isset($add_substitutes_submit)) {
		$add_to_substitutes = $_POST['add_to_substitutes'];
		$match_id = $_POST['match_id'];
		$season_id = $_POST['season_id'];
		foreach($add_to_substitutes as $player_id) {
			$get_substitutes = mysqli_query($db_connect, "SELECT
				team_substitutes.SubstitutePlayerID AS substitute_player_id
				FROM team_substitutes,team_appearances
				WHERE (team_substitutes.SubstitutePlayerID = '$player_id'
				AND team_substitutes.SubstituteMatchID = '$match_id'
				AND team_substitutes.SubstituteSeasonID = '$season_id')
				OR (team_appearances.AppearancePlayerID = '$player_id'
				AND team_appearances.AppearanceMatchID = '$match_id'
				AND team_appearances.AppearanceSeasonID = '$season_id')
			") or die(mysqli_error());

			if (mysqli_num_rows($get_substitutes) == 0) {
				mysqli_query($db_connect, "INSERT INTO team_substitutes SET
					SubstitutePlayerID = '$player_id',
					SubstituteMatchID = '$match_id',
					SubstituteSeasonID = '$season_id'
				") or die(mysqli_error());
			}
			mysqli_free_result($get_substitutes);
		}
		header("Location: $HTTP_REFERER");

	} else if (isset($add_substitutions_submit)) {
		$player_id_in = $_POST['add_to_substitutions_in'];
		$player_id_out = $_POST['add_to_substitutions_out'];
		$match_id = $_POST['match_id'];
		$season_id = $_POST['season_id'];
		$substitution_minute = $_POST['substitution_minute'];

		if ($player_id_in == $player_id_out) {
			echo "In and Out Player can't be the same.<br>Push Back Button to get Back.";
			exit();
		}
		mysqli_query($db_connect, "INSERT INTO team_substitutions SET
			SubstitutionPlayerIDIn = '$player_id_in',
			SubstitutionPlayerIDOut = '$player_id_out',
			SubstitutionMatchID = '$match_id',
			SubstitutionSeasonID = '$season_id',
			SubstitutionMinute = '$substitution_minute'
		") or die(mysqli_error());
		header("Location: $HTTP_REFERER");

	} else if ($action) {
		if ($action == 'remove_from_squad') {
			$id = $_REQUEST['id'];
			mysqli_query($db_connect, "DELETE FROM team_appearances WHERE AppearanceID = '$id'") or die(mysqli_error());
			header("Location: $HTTP_REFERER");

		} else if ($action == 'remove_from_goal_scorers') {
			$id = $_REQUEST['id'];
			mysqli_query($db_connect, "DELETE FROM team_goals WHERE GoalID = '$id'") or die(mysqli_error());
			header("Location: $HTTP_REFERER");

		} else if ($action == 'remove_from_goal_assists') {
			$id = $_REQUEST['id'];
			mysqli_query($db_connect, "DELETE FROM team_goal_assists WHERE GoalAssistID = '$id'") or die(mysqli_error());
			header("Location: $HTTP_REFERER");

		} else if ($action == 'remove_from_yellow_cards') {
			$id = $_REQUEST['id'];
			mysqli_query($db_connect, "DELETE FROM team_yellow_cards WHERE YellowCardID = '$id'") or die(mysqli_error());
			header("Location: $HTTP_REFERER");

		} else if ($action == 'remove_from_red_cards') {
			$id = $_REQUEST['id'];
			mysqli_query($db_connect, "DELETE FROM team_red_cards WHERE RedCardID = '$id'") or die(mysqli_error());
			header("Location: $HTTP_REFERER");

		} else if ($action == 'remove_from_substitutes') {
			$id = $_REQUEST['id'];
			mysqli_query($db_connect, "DELETE FROM team_substitutes WHERE SubstituteID = '$id'") or die(mysqli_error());
			header("Location: $HTTP_REFERER");
		} else if ($action == 'remove_from_substitutions') {
			$id = $_REQUEST['id'];
			mysqli_query($db_connect, "DELETE FROM team_substitutions WHERE SubstitutionID = '$id'") or die(mysqli_error());
			header("Location: $HTTP_REFERER");
		}
	}
mysqli_close($db_connect);
}
?>