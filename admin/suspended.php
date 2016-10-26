<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization failed.<br><a href='index.php'>Restart, please</a>";
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host","$db_user","$db_password", "$db_name") or die(mysqli_error());

	$season_id = $_SESSION['season_id'];
	$season_name = $_SESSION['season_name'];

	$PHP_SELF = $_SERVER['PHP_SELF'];
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];

	if (isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
	if (isset($_REQUEST['player_id'])){ $player_id = $_REQUEST['player_id']; }
	if (isset($_POST['submit'])){ $submit = $_POST['submit']; }

	if (isset($submit)) {
		$selected_match = $_POST['selected_match'];
		$reason = trim($_POST['reason']);
		$suspended_player_id = $_POST['suspended_player_id'];
		$get_matches = mysqli_query($db_connect, " SELECT
			DATE_FORMAT(team_matches.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
			MatchID AS match_id
			FROM team_matches
			WHERE MatchSeasonID = '$season_id'
			ORDER BY match_date
		")or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_suspended WHERE SuspendedPlayerID = '$suspended_player_id' AND SuspendedSeasonID = '$season_id'") or die(mysqli_error());
		$i = 0;
		while($mdata = mysqli_fetch_array($get_matches)) {
			if($selected_match[$i] == 1) {
				$match_id = $mdata['match_id'];
				mysqli_query($db_connect, "INSERT INTO team_suspended SET
				SuspendedReason = '$reason',
				SuspendedPlayerID = '$suspended_player_id',
				SuspendedMatchID = '$match_id',
				SuspendedSeasonID = '$season_id'
				") or die(mysqli_error());
			}
			$i++;
		}
		mysqli_free_result($get_matches);

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
		echo "<table align='center' width='600'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'><h1>Suspended Players</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>Select Suspended Player first from the right.</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} else if ($action == 'modify') {
		$get_player_name = mysqli_query($db_connect, "SELECT
			CONCAT(PlayerFirstName, ' ', PlayerLastName) AS player_name,
			PlayerID AS player_id
			FROM team_players
			WHERE PlayerID = '$player_id'
			LIMIT 1
		") or die(mysqli_error());
		$data = mysqli_fetch_array($get_player_name);
		mysqli_free_result($get_player_name);

		$get_suspended_matches = mysqli_query($db_connect, "SELECT
			SuspendedMatchID AS match_id,
			SuspendedReason AS reason
			FROM team_suspended
			WHERE SuspendedPlayerID = '$player_id'
			AND SuspendedSeasonID = '$season_id'
		") or die(mysqli_error());
		$suspension_reason = '';
		$i = 0;
		$counter = 0;
		while($injdata = mysqli_fetch_array($get_suspended_matches)) {
			$suspension_table[$i] = $injdata['match_id'];
			$suspension_reason = $injdata['reason'];
			$i++;
			$counter++;
		}
		mysqli_free_result($get_suspended_matches);

		echo "<table align='center' width='600'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>";
		echo "<form method='post' action='$PHP_SELF?session_id=$session'>\n";
		echo "<h1>Suspended players</h1>";
		echo "Selected Player: ".$data['player_name']."<br><br>";
		echo "Select matches where player is not in squad due to suspension.<br><br>";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='middle'>";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "Reason for Suspension:</td>\n";
		echo "<td align='left' valign='middle'><input type='text' name='reason' value='".$suspension_reason."'></td>\n";
		echo "<tr>\n";
		$get_matches = mysqli_query($db_connect, "SELECT
			DATE_FORMAT(team_matches.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
			team_matches.MatchID AS match_id,
			team_opponents.OpponentName AS opponent_name,
			team_match_types.MatchTypeName AS match_type_name,
			team_match_places.MatchPlaceName AS match_place
			FROM team_matches, team_match_types, team_match_places, team_opponents
			WHERE MatchSeasonID = '$season_id'
			AND team_matches.MatchTypeID = team_match_types.MatchTypeID
			AND team_matches.MatchPlaceID = team_match_places.MatchPlaceID
			AND team_matches.MatchOpponent = team_opponents.OpponentID
			ORDER BY match_date
		") or die(mysqli_error());
		$i = 0;
		while($mdata = mysqli_fetch_array($get_matches)) {
			$check = 0;
			for($j = 0 ; $j < $counter ; $j++) {
				if ($mdata['match_id'] == $suspension_table[$j]) {
					$check = 1;
				}
			}
			echo "<tr>\n";
			echo "<td align='left' valign='middle' colspan='2'>";

			if ($check == 1){
				echo "<input type='checkbox' name='selected_match[".$i."]' value='1' CHECKED>";
			} else {
				echo "<input type='checkbox' name='selected_match[".$i."]' value='1'>";
			}
			echo " ".$mdata['match_date'].", vs. ".$mdata['opponent_name']." (".$mdata['match_type_name'].")";
			echo "</td>\n";
			echo "<tr>\n";
			$i++;
		}
		mysqli_free_result($get_matches);

		echo "<tr>\n";
		echo "<td align='left' valign='middle' colspan='2'>";
		echo "<input type='submit' value='Save changes' name='submit'>";
		echo "<input type='hidden' name='suspended_player_id' value='".$data['player_id']."'>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</form>\n";
	}
	echo "</td>\n";
	echo "<td align='left' valign='top'>";
	$get_players = mysqli_query($db_connect, "SELECT
		CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
		team_players.PlayerID AS player_id
		FROM team_players,team_seasons
		WHERE team_players.PlayerID = team_seasons.SeasonPlayerID
		AND team_seasons.SeasonID = '$season_id'
		ORDER BY player_name
	") or die(mysqli_error());

	if (mysqli_num_rows($get_players) < 1) {
		echo "<b>No Players: ".$season_name."</b>";
	} else {
		echo "<b>Select Suspended Player:<br><br>";
		while($data = mysqli_fetch_array($get_players)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;player_id=".$data['player_id']."'>".$data['player_name']."</a><br>\n";
		}
	}
	mysqli_free_result($get_players);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>