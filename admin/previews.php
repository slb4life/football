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
	if (isset($_REQUEST['match_id'])){ $match_id = $_REQUEST['match_id']; }
	if (isset($_POST['submit'])){ $submit = $_POST['submit']; }

	if (isset($submit)) {
		$preview_tickets = str_replace("\r\n", '<br>', trim($_POST['preview_tickets']));
		$preview_tv = str_replace("\r\n", '<br>', trim($_POST['preview_tv']));
		$preview_text = str_replace("\r\n", '<br>', trim($_POST['preview_text']));
		$preview_text_under = str_replace("\r\n", '<br>', trim($_POST['preview_text_under']));
		$match_id = $_POST['match_id'];
		$preview_id = $_POST['preview_id'];

		if (!get_magic_quotes_gpc()) {
			$preview_tickets = addslashes($preview_tickets);
			$preview_tv = addslashes($preview_tv);
			$preview_text = addslashes($preview_text);
			$preview_text_under = addslashes($preview_text_under);
		}
		if($preview_id == -1) {
			mysqli_query($db_connect, "INSERT INTO team_previews SET
				PreviewText = '$preview_text',
				PreviewTextUnder = '$preview_text_under',
				PreviewTickets = '$preview_tickets',
				PreviewTV = '$preview_tv',
				PreviewMatchID = '$match_id'
			") or die(mysqli_error());
		} else {
			mysqli_query($db_connect, "UPDATE team_previews SET
				PreviewText = '$preview_text',
				PreviewTextUnder = '$preview_text_under',
				PreviewTickets = '$preview_tickets',
				PreviewTV = '$preview_tv'
				WHERE PreviewID = '$preview_id'
			") or die(mysqli_error());
		}
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
	echo "<table align='center' width='600'>\n";
	echo "<tr>\n";
	echo "<td width='300' align='left' valign='top'>";

	if (!isset($action)) {
		echo "<h1>Previews</h1>\n";
		echo "To write a preview,	choose<br>the match first from the right.\n";
	} else if ($action == 'modify') {
		$get_preview = mysqli_query($db_connect, "SELECT
			PreviewID AS preview_id,
			PreviewText AS preview_text,
			PreviewTextUnder AS preview_text_under,
			PreviewTickets AS preview_tickets,
			PreviewTV AS preview_tv
			FROM team_previews
			WHERE PreviewMatchID = '$match_id'
			LIMIT 1
		") or die(mysqli_error());
		
		if (mysqli_num_rows($get_preview) == 0) {
			$preview_text = '';
			$preview_text_under = '';
			$preview_tickets_info = '';
			$preview_tv_info = '';
			$preview_id = -1;
		} else {
			$data = mysqli_fetch_array($get_preview);
			$preview_text = str_replace('<br>', "\r\n", $data['preview_text']);
			$preview_text_under = str_replace('<br>', "\r\n", $data['preview_text_under']);
			$preview_tickets_info = str_replace('<br>', "\r\n", $data['preview_tickets']);
			$preview_tv_info = str_replace('<br>', "\r\n", $data['preview_tv']);
			$preview_id = $data['preview_id'];
		}
		mysqli_free_result($get_preview);

		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Preview</h1>\n";
		$get_match = mysqli_query($db_connect, "SELECT
			DATE_FORMAT(team_matches.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
			team_opponents.OpponentName AS opponent_name,
			team_match_types.MatchTypeName AS match_type_name,
			team_match_places.MatchPlaceName AS match_place,
			team_matches.MatchNeutral AS neutral
			FROM team_matches, team_match_types, team_match_places, team_opponents
			WHERE MatchSeasonID = '$season_id'
			AND team_matches.MatchTypeID = team_match_types.MatchTypeID
			AND team_matches.MatchPlaceID = team_match_places.MatchPlaceID
			AND team_matches.MatchOpponent = team_opponents.OpponentID
			AND MatchID = '$match_id'
			LIMIT 1
		") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_match)) {
			echo "<b>".$data['match_date'].", vs. ".$data['opponent_name']."<br>".$data['match_place']."";
			if($data['neutral'] == 1)
			echo " (Neutral)";
			echo ": ".$data['match_type_name']."</b><br><br>\n";
		}
		mysqli_free_result($get_match);

		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Ticket Info:<br><textarea name='preview_tickets' cols='40' rows='10'>".$preview_tickets_info."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>TV Info:<br><textarea name='preview_tv' cols='40' rows='10'>".$preview_tv_info."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Preview Text:<br><textarea name='preview_text' cols='40' rows='15'>".$preview_text."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Preview text under your Teams Line-up:<br><textarea name='preview_text_under' cols='40' rows='15'>".$preview_text_under."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>";
		echo "<input type='hidden' name='match_id' value='".$match_id."'>";
		echo "<input type='hidden' name='preview_id' value='".$preview_id."'>";
		echo "<input type='submit' name='submit' value='Add/Modify Preview'>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</form>\n";
		echo "Preview will be availabe in Match Calendar, if Preview text is filled.\n";
	}
	echo "</td>\n";
	echo "<td align='left' valign='top' width='300'>\n";
	$get_matches = mysqli_query($db_connect, "SELECT
		DATE_FORMAT(team_matches.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
		team_opponents.OpponentName AS opponent_name,
		team_matches.MatchID AS match_id,
		team_match_types.MatchTypeName AS match_type_name,
		team_matches.MatchStadium AS stadium,
		team_match_places.MatchPlaceName AS match_place,
		team_matches.MatchPublish AS publish,
		team_matches.MatchNeutral AS neutral,
		team_previews.PreviewText AS preview_text,
		team_previews.PreviewTextUnder AS preview_text_under
		FROM (team_matches, team_match_types, team_match_places, team_opponents)
		LEFT OUTER JOIN team_previews ON team_matches.MatchID = team_previews.PreviewMatchID
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
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;match_id=".$data['match_id']."'>".$data['match_date'].", vs. ".$data['opponent_name']."</a><br>".$data['match_place']."";

			if ($data['neutral'] == 1) {
				echo " (Neutral)";
				echo ": ".$data['match_type_name']."";
			}
			if ($data['preview_text'] == '') {
				echo "<br><br>\n";
			} else {
				echo " (P)<br><br>\n";
			}
		}
	}
	echo "<br><br>P = Preview Text is filled</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>