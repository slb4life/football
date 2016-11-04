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
	if (isset($_POST['modify_submit'])){ $modify_submit = $_POST['modify_submit']; }
	if (isset($_POST['delete_submit'])){ $delete_submit = $_POST['delete_submit']; }

	if (isset($modify_submit)) {
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

	} else if (isset($delete_submit)) {
		$id = $_POST['id'];
		mysqli_query($db_connect, "DELETE FROM team_comments WHERE ID = '$id' LIMIT 1") or die(mysqli_error());
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
	echo "<table align='center' width='600'><tr>\n";
	echo "<td width='300' align='left' valign='top'>";

	if (!isset($action)) {
		echo "<h1>Fan Comments</h1>";
		echo "Choose A Match From The Right First...";
	} else if ($action == 'modify') {
		echo "<h1>Fan Comments</h1>";
		$get_matches = mysqli_query($db_connect, "SELECT
			DATE_FORMAT(M.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
			O.OpponentName AS opponent_name,
			MT.MatchTypeName AS match_type_name,
			MP.MatchPlaceName AS match_place_name,
			M.MatchNeutral AS match_neutral
			FROM team_matches AS M, team_match_types AS MT, team_match_places AS MP, team_opponents AS O
			WHERE MatchSeasonID = '$season_id'
			AND M.MatchTypeID = MT.MatchTypeID
			AND M.MatchPlaceID = MP.MatchPlaceID
			AND M.MatchOpponent = O.OpponentID
			AND MatchID = '$match_id'
			LIMIT 1
		") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_matches)) {
			echo "<b>".$data['match_date'].", vs. ".$data['opponent_name']."<br>".$data['match_place_name']."";

			if ($data['match_neutral'] == 1) {
				echo "(match_neutral)";
				echo ": ".$data['match_type_name']."</b><br><br>\n";
			}
		}
		mysqli_free_result($get_matches);

		$get_comments = mysqli_query($db_connect, "SELECT
			ID AS id,
			Name AS name,
			Comments AS comments,
			IP AS ip
			FROM team_comments WHERE MatchID = '$match_id'
			ORDER by Time DESC
		") or die(mysqli_error());
		while($data = mysqli_fetch_array($get_comments)) {
			$data['comments'] = str_replace('<br>', "\r\n", $data['comments']);
			echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
			echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
			echo "<td align='left' valign='top' colspan='2'><b>Fan Name:</b><br><input type='text' name='name' value='".$data['name']."'></td>\n";
			echo "</tr><tr>\n";
			echo "<td align='left' valign='top' colspan='2'><b>Comments:</b><br><textarea name='comments' cols='40' rows='10'>".$data['comments']."</textarea></td>\n";
			echo "</tr><tr>\n";
			echo "<td align='left' valign='top' colspan='2'>Comment Was Sent From IP Address ".$data['ip']."</td>\n";
			echo "</tr><tr>\n";
			echo "<td align='left' valign='top' colspan='2'>";
			echo "<input type='hidden' name='id' value='".$data['id']."'>\n";
			echo "<input type='submit' name='modify_submit' value='Modify'>\n";
			echo "<input type='submit' name='delete_submit' value='Delete'>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		if (mysqli_num_rows($get_comments) == 0) {
			echo "Nobody Has Made A Comment Yet..";
		}
		mysqli_free_result($get_comments);
	}
	echo "</td>\n";
	echo "<td align='left' valign='top' width='300'>\n";
	$get_matches = mysqli_query($db_connect, "SELECT
		DATE_FORMAT(M.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
		O.OpponentName AS opponent_name,
		M.MatchID AS match_id,
		MT.MatchTypeName AS match_type_name,
		M.MatchStadium AS match_stadium,
		MP.MatchPlaceName AS match_place_name,
		M.MatchPublish AS publish,
		M.MatchNeutral AS match_neutral,
		P.PreviewText AS preview_text
		FROM (team_matches AS M, team_match_types AS MT, team_match_places AS MP, team_opponents AS O)
		LEFT OUTER JOIN team_previews AS P ON M.MatchID = P.PreviewMatchID
		WHERE MatchSeasonID = '$season_id'
		AND M.MatchTypeID = MT.MatchTypeID
		AND M.MatchPlaceID = MP.MatchPlaceID
		AND M.MatchOpponent = O.OpponentID
		ORDER BY match_date
	") or die(mysqli_error());

	if (mysqli_num_rows($get_matches) <1) {
		echo "<b>No Matches: ".$season_name."</b>";
	} else {
		echo "<b>Matches In ".$season_name.":</b><br><br>";
		while($data = mysqli_fetch_array($get_matches)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;match_id=".$data['match_id']."'>".$data['match_date'].", vs. ".$data['opponent_name']."</a><br>".$data['match_place_name']."";

			if ($data['match_neutral'] == 1) {
				echo "(match_neutral)";
				echo ": ".$data['match_type_name']."<br><br>\n";
			}
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