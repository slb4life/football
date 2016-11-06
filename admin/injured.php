<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host", "$db_user", "$db_password", "$db_name") or die(mysqli_error());
	
	$season_id = $_SESSION['season_id'];
	$season_name = $_SESSION['season_name'];

	$PHP_SELF = $_SERVER['PHP_SELF'];
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];

	if (isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
	if (isset($_REQUEST['player_id'])){ $player_id = $_REQUEST['player_id']; }
	if (isset($_POST['submit'])){ $submit = $_POST['submit']; }

	if (isset($submit)) {
		$selected_match = $_POST['selected_match'];
		$injured_reason = trim($_POST['injured_reason']);
		$injured_player_id = $_POST['injured_player_id'];
		$get_matches = mysqli_query($db_connect, "SELECT
			DATE_FORMAT(M.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
			MatchID AS match_id
			FROM team_matches AS M
			WHERE MatchSeasonID = '$season_id'
			ORDER BY match_date
		") or die(mysqli_error());
		mysqli_query($db_connect, "DELETE FROM team_injured WHERE InjuredPlayerID = '$injured_player_id' AND InjuredSeasonID = '$season_id'") or die(mysqli_error());
		$i = 0;
		while($mdata = mysqli_fetch_array($get_matches)) {
			if ($selected_match[$i] == 1) {
				$injured_match_id = $mdata['match_id'];
				mysqli_query($db_connect, "INSERT INTO team_injured SET
					InjuredReason = '$injured_reason',
					InjuredPlayerID = '$injured_player_id',
					InjuredMatchID = '$injured_match_id',
					InjuredSeasonID = '$season_id'
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
		echo "<table align='center' width='600'><tr>\n";
		echo "<td align='left' valign='top'><h1>Injured players</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
		echo "<td align='left' valign='top'>Select injured player first from the right.</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} elseif ($action == 'modify') {
		$get_player_name = mysqli_query($db_connect, "SELECT
			CONCAT(PlayerFirstName, ' ', PlayerLastName) AS player_name,
			PlayerID AS player_id
			FROM team_players
			WHERE PlayerID = '$player_id'
			LIMIT 1
		") or die(mysqli_error());
		$data = mysqli_fetch_array($get_player_name);
		mysqli_free_result($get_player_name);

		$get_injured_matches = mysqli_query($db_connect, "SELECT
			InjuredMatchID AS match_id,
			InjuredReason AS injured_reason
			FROM team_injured
			WHERE InjuredPlayerID = '$player_id'
			AND InjuredSeasonID = '$season_id'
		") or die(mysqli_error());
		$injury_reason = '';
		$i = 0;
		$counter = 0;
		while($injury_data = mysqli_fetch_array($get_injured_matches)) {
			$injury[$i] = $injury_data['match_id'];
			$injury_reason = $injury_data['injured_reason'];
			$i++;
			$counter++;
		}
		mysqli_free_result($get_injured_matches);

		echo "<table align='center' width='600'><tr>\n";
		echo "<td align='left' valign='top'>";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>";
		echo "<h1>Injured Players</h1>";
		echo "Selected Player: ".$data['player_name']."<br><br>";
		echo "Select Matches Where Player Is Not In Squad Due To Injury.<br><br>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
		echo "<td align='left' valign='middle'><form method='post' action='".$PHP_SELF."?session_id=".$session."'>Reason For Injury:</td>\n";
		echo "<td align='left' valign='middle'><input type='text' name='injured_reason' value='".$injury_reason."'></td>\n";
		echo "<tr>\n";
		$get_matches = mysqli_query($db_connect, "SELECT
			DATE_FORMAT(M.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
			M.MatchID AS match_id,
			O.OpponentName AS opponent_name,
			MT.MatchTypeName AS match_type_name,
			MP.MatchPlaceName AS match_place
			FROM team_matches AS M, team_match_types AS MT, team_match_places AS MP, team_opponents AS O
			WHERE MatchSeasonID = '$season_id'
			AND M.MatchTypeID = MT.MatchTypeID
			AND M.MatchPlaceID = MP.MatchPlaceID
			AND M.MatchOpponent = O.OpponentID
			ORDER BY match_date
		") or die(mysqli_error());
		$i = 0;
		while($mdata = mysqli_fetch_array($get_matches)) {
			$check = 0;
			for($j = 0 ; $j < $counter ; $j++) {
				if ($mdata['match_id'] == $injury[$j]) {
					$check = 1;
				}
			}
			echo "<tr>\n";
			echo "<td align='left' valign='middle' colspan='2'>";
			if ($check == 1) {
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
		echo "<input type='submit' value='Save Changes' name='submit'>";
		echo "<input type='hidden' name='injured_player_id' value='".$data['player_id']."'>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</form>\n";
	}
	echo "</td>\n";
	echo "<td align='left' valign='top'>";
	$get_players = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id
		FROM team_players AS P, team_seasons AS S
		WHERE P.PlayerID = S.SeasonPlayerID
		AND S.SeasonID = '$season_id'
		ORDER BY player_name
	") or die(mysqli_error());

	if (mysqli_num_rows($get_players) < 1) {
		echo "<b>No Players: ".$season_name."</b>";
	} else {
		echo "<b>Select Injured Player:<br><br>";
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