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

	if (isset($_REQUEST['match_id'])){ $match_id = $_REQUEST['match_id']; }
	if (isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
	if (isset($_POST['submit'])){ $submit = $_POST['submit']; }
	if (isset($_POST['update_picture_info'])){ $update_picture_info = $_POST['update_picture_info']; }

	if (isset($submit)) {
		$match_id = $_POST['match_id'];
		$picture_text = $_POST['picture_text'];
		$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
		$check_type = explode(".",$_FILES['image_file']['name']);

		if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
			echo "Please upload only jpg or png-filetype";
			exit;
		}
		srand((double)microtime()*1000000);
		$picture_name = md5(rand(0,9999));
		$picture_name .= '.' . $check_type[1];

		if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
			copy($_FILES['image_file']['tmp_name'], "../images/".$picture_name);
			$query = mysqli_query($db_connect, "SELECT COUNT(PictureID) AS get_total FROM team_picture_gallery WHERE PictureMatchID = '$match_id'") or die(mysqli_error());
			$data = mysqli_fetch_array($query);
			$get_total = $data['get_total'];
			$get_total++;
			mysqli_free_result($query);

			mysqli_query($db_connect, "INSERT INTO team_picture_gallery SET
				PictureName = '$picture_name',
				PictureMatchID = '$match_id',
				PictureText = '$picture_text',
				PictureNumber = '$get_total'
			") or die(mysqli_error());
			header("Location: $HTTP_REFERER");

		} else {
			echo "Upload attack. Filename: ".$_FILES['image_file']['name'];
		}
		header("Location: $HTTP_REFERER");

	} else if (isset($update_picture_info)) {
		$picture_text = $_POST['picture_text'];
		$picture_id = $_POST['picture_id'];
		mysqli_query($db_connect, "UPDATE team_picture_gallery SET PictureText = '$picture_text' WHERE PictureID = '$picture_id' LIMIT 1") or die(mysqli_error());
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
	echo "<td width='50%' align='left' valign='top'>";
	if (!isset($action)) {
		echo "<h1>Upload Pictures</h1>";
		echo "Choose the Match first from the right.";
	} else if ($action == 'modify') {
		echo "<form enctype='multipart/form-data' method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Upload Pictures</h1>\n";
		$query = mysqli_query($db_connect, "SELECT
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
			AND MatchID = '$match_id' LIMIT 1
		") or die(mysqli_error());
		while($data = mysqli_fetch_array($query)) {
			echo "<b>".$data['match_date'].", vs. ".$data['opponent_name']."<br>".$data['match_place']."";
			if ($data['neutral'] == 1)
				echo "(neutral)";
			echo ": ".$data['match_type_name']."</b><br><br>\n";
		}
		mysqli_free_result($query);
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Text for the Picture:<br>(255 chars max)<br><textarea name='picture_text' cols='40' rows='3'></textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Picture:<br><input name='image_file' type='file'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'><input type='hidden' name='match_id' value='".$match_id."'><input type='submit' name='submit' value='Add Picture'><br></td>\n";
		echo "</tr>\n";
		echo "</form>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top' colspan='2'><b>Pictures so far Added:</b></td>\n";
		echo "</tr>\n";
		$query = mysqli_query($db_connect, "SELECT
			PictureID AS picture_id,
			PictureName AS picture_name,
			PictureText AS picture_text,
			PictureNumber AS picture_number
			FROM team_picture_gallery
			WHERE PictureMatchID = '$match_id'
			ORDER BY picture_number
		") or die(mysqli_error());

		if (mysqli_num_rows($query) > 0) {
			while($data = mysqli_fetch_array($query)) {
				echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
				echo "<tr>\n";
				echo "<td align='left' valign='top' colspan='2'>";
				echo "<img src='../images/".$data['picture_name']."'><br>";
				echo "<a href='delete_match_picture.php?session_id=".$session."&amp;picture_id=".$data['picture_id']."&amp;name=".$data['picture_name']."&amp;match_id=".$match_id."&amp;picture_number=".$data['picture_number']."'>Delete this Picture</a> |";
				echo "<a href='move_picture.php?picture_number=".$data['picture_number']."&amp;match_id=".$match_id."&amp;picture_id=".$data['picture_id']."&amp;order=1&amp;session_id=".$session."'>Move Up</a> |";
				echo "<a href='move_picture.php?picture_number=".$data['picture_number']."&amp;match_id=".$match_id."&amp;picture_id=".$data['picture_id']."&amp;order=0&amp;session_id=".$session."'>Move Down</a><br>";
				echo "<textarea name='picture_text' cols='40' rows='3'>".$data['picture_text']."</textarea><br>\n";
				echo "<input type='submit' name='update_picture_info' value='Update'>\n";
				echo "<input type='hidden' name='picture_id' value='".$data['picture_id']."'>\n";
				echo "</td>\n";
				echo "</tr>\n";
				echo "</form>\n";
			}
		} else {
			echo "<tr>\n";
			echo "<td align='left' valign='top' colspan='2'><i>No Pictures Added..</i></td>\n";
			echo "</tr>\n";
		}
		mysqli_free_result($query);

		echo "</table>\n";
	}
	echo "</td>\n";
	echo "<td align='left' valign='top' width='50%'>\n";
	$get_matches = mysqli_query($db_connect, "SELECT
		DATE_FORMAT(team_matches.MatchDateTime, '%b %D %Y at %H:%i') AS match_date,
		team_opponents.OpponentName AS opponent_name,
		team_matches.MatchID AS match_id,
		team_match_types.MatchTypeName AS match_type_name,
		team_matches.MatchStadium AS stadium,
		team_match_places.MatchPlaceName AS match_place,
		team_matches.MatchPublish AS publish,
		team_matches.MatchNeutral AS neutral
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
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;match_id=".$data['match_id']."'>".$data['match_date'].", vs. ".$data['opponent_name']."</a><br>".$data['match_place']."";
			if ($data['neutral'] == 1)
				echo "(neutral)";
				echo ": ".$data['match_type_name']."";
			if ($data['publish'] == 1)
				echo "<br><br>";
			else
				echo " (NB)<br><br>";
		}
	}
	echo "<br>NB = This Match is not Published yet.";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>