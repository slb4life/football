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
	if (isset($_REQUEST['match_id'])){ $match_id = $_REQUEST['match_id']; }
	if (isset($_POST['modifysubmit'])){ $modifysubmit = $_POST['modifysubmit']; }
	if (isset($_POST['deletesubmit'])){ $deletesubmit = $_POST['deletesubmit']; }

	if (isset($modifysubmit)) {
		$name = trim($_POST['name']);
		$comments = str_replace("\r\n", '<br>', trim($_POST['comments']));
		$id = $_POST['id'];
		mysqli_query($db_connect, "UPDATE team_comments SET
			Name = '$name',
			Comments = '$comments'
			WHERE ID = '$id'
			LIMIT 1
		") or die(mysqli_error());
		header("Location: $HTTP_REFERER");

	} else if (isset($deletesubmit)) {
		$id = $_POST['id'];
		mysqli_query($db_connect, "DELETE FROM team_comments WHERE ID = '$id' LIMIT 1") or die(mysqli_error());
		header("Location: $HTTP_REFERER");
	}
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');
	
	echo "<table align='center' width='600'><tr>\n";
	echo "<td width='300' align='left' valign='top'>";

	if (!isset($action)) {
		echo "<h1>Fan comments</h1>";
		echo "Choose a match from the right first...";
	} else if ($action == 'modify') {
		echo'<h1>Fan comments</h1>';
		$get_match = mysqli_query($db_connect, "SELECT DATE_FORMAT(team_matches.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
			team_opponents.OpponentName AS opponent_name,
			team_match_types.MatchTypeName AS match_type_name,
			team_match_places.MatchPlaceName AS match_place_name,
			team_matches.MatchNeutral AS match_neutral
			FROM team_matches, team_match_types, team_match_places, team_opponents
			WHERE MatchSeasonID = '$season_id'
			AND team_matches.MatchTypeID = team_match_types.MatchTypeID
			AND team_matches.MatchPlaceID = team_match_places.MatchPlaceID
			AND team_matches.MatchOpponent = team_opponents.OpponentID
			AND MatchID = '$match_id'
			LIMIT 1
		") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_match)) {
			echo "<b>".$data['match_date'].", vs. ".$data['opponent_name']."<br>".$data['match_place_name']."";
			if ($data['match_neutral'] == 1)
			echo "(match_neutral)";
			echo ": ".$data['match_type_name']."</b><br><br>\n";
		}
		mysqli_free_result($get_match);

		$query = mysqli_query($db_connect, "SELECT
			ID AS id,
			Name AS name,
			Comments AS comments,
			IP As ip
			FROM team_comments WHERE MatchID = '$match_id'
			ORDER by Time DESC
		") or die(mysqli_error());
		while($data = mysqli_fetch_array($query)) {
			$data['comments'] = str_replace('<br>', "\r\n", $data['comments']);
			echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
			echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
			echo "<td align='left' valign='top' colspan='2'><b>Fan name:</b><br><input type='text' name='name' value='".$data['name']."'></td>\n";
			echo "</tr><tr>\n";
			echo "<td align='left' valign='top' colspan='2'><b>Comments:</b><br><textarea name='comments' cols='40' rows='10'>".$data['comments']."</textarea></td>\n";
			echo "</tr><tr>\n";
			echo "<td align='left' valign='top' colspan='2'>Comment was sent from IP address ".$data['ip']."</td>\n";
			echo "</tr><tr>\n";
			echo "<td align='left' valign='top' colspan='2'>";
			echo "<input type='hidden' name='id' value='".$data['id']."'>";
			echo "<input type='submit' name='modifysubmit' value='Modify'>&nbsp;";
			echo "<input type='submit' name='deletesubmit' value='Delete'>";
			echo "<br>";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
			echo "<hr width='100%'>\n";
		}

		if (mysqli_num_rows($query) == 0) {
			echo "Nobody has made a Comment Yet..";
		}
		mysqli_free_result($query);
	}
	echo "</td>\n";
	echo "<td align='left' valign='top' width='300'>\n";
	$get_matches = mysqli_query($db_connect, "SELECT
		DATE_FORMAT(team_matches.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
		team_opponents.OpponentName AS opponent_name,
		team_matches.MatchID AS match_id,
		team_match_types.MatchTypeName AS match_type_name,
		team_matches.MatchStadium AS match_stadium,
		team_match_places.MatchPlaceName AS match_place_name,
		team_matches.MatchPublish AS publish,
		team_matches.MatchNeutral AS match_neutral,
		team_previews.PreviewText AS preview_text
		FROM (team_matches, team_match_types, team_match_places, team_opponents)
		LEFT OUTER JOIN team_previews ON team_matches.MatchID = team_previews.PreviewMatchID
		WHERE MatchSeasonID = '$season_id'
		AND team_matches.MatchTypeID = team_match_types.MatchTypeID
		AND team_matches.MatchPlaceID = team_match_places.MatchPlaceID
		AND team_matches.MatchOpponent = team_opponents.OpponentID
		ORDER BY match_date
	") or die(mysqli_error());

	if (mysqli_num_rows($get_matches) <1) {
		echo "<b>No matches: ".$season_name."</b>";
	} else {
		echo "<b>Matches in ".$season_name.":</b><br><br>";
		while($data = mysqli_fetch_array($get_matches)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;match_id=".$data['match_id']."'>".$data['match_date'].", vs. ".$data['opponent_name']."</a><br>".$data['match_place_name']."";
			if ($data['match_neutral'] == 1)
				echo "(match_neutral)";
				echo ": ".$data['match_type_name']."<br><br>\n";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>