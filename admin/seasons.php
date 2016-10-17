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
	if (isset($_POST['add_submit'])){ $add_submit = $_POST['add_submit']; }
	if (isset($_POST['modify_submit'])){ $modify_submit = $_POST['modify_submit']; }
	if (isset($_POST['delete_submit'])){ $delete_submit = $_POST['delete_submit']; }

	if (isset($add_submit)) {
		$name = trim($_POST['name']);
		$query = mysqli_query($db_connect, "SELECT SeasonName FROM team_season_names WHERE SeasonName = '$name'") or die(mysqli_error());

		if (mysqli_num_rows($query) > 0) {
			echo "There is already season named: $name in database.<br>Please write another name for the season.";
			exit();
		}
		mysqli_free_result($query);

		if ($name != '') {
			mysqli_query($db_connect, "INSERT INTO team_season_names SET SeasonName = '$name'") or die(mysqli_error());
			header("Location: $PHP_SELF?session_id=$session");
		}
	} else if (isset($modify_submit)) {
		$name = $_POST['name'];
		$publish = $_POST['publish'];
		$season_id = $_POST['season_id'];

		if (!isset($publish)) {
			$publish = 0;
		}

		if ($name != '') {
			mysqli_query($db_connect, "UPDATE team_season_names SET SeasonName = '$name', SeasonPublish = '$publish' WHERE SeasonID = '$season_id'") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");

	} else if (isset($delete_submit)) {
		$season_id = $_POST['season_id'];
		$query = mysqli_query($db_connect, "SELECT M.MatchID FROM team_matches M, team_seasons S WHERE M.MatchSeasonID = '$season_id' OR S.SeasonPlayerID = '$season_id'") or die(mysqli_error());

		if (mysqli_num_rows($query) == 0) {
			mysqli_query($db_connect, "DELETE FROM team_season_names WHERE SeasonID = '$season_id'") or die(mysqli_error());
		} else {
			echo "There is already match or player booked that season you wanted to delete. You must delete match or player first.";
			exit();
		}
		header("Location: $PHP_SELF?session_id=$session");
	}
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');
	echo "<table align='center' width='600'><tr>\n";
	echo "<td>\n";
	
	if (!isset($action)) {
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Add Season</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>Season Name (Years):</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='name'></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='add_submit' value='Add Season'>\n";
		echo "</form>\n";
	} else if ($action == 'modify') {
		$season_id = $_REQUEST['season'];
		$get_season = mysqli_query($db_connect, "SELECT * FROM team_season_names WHERE SeasonID = '$season_id' LIMIT 1") or die(mysqli_error());
		$data = mysqli_fetch_array($get_season);
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Modify / Delete Season</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
		echo "<td align='left' valign='top'>Season Name (Years):</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<input type='text' name='name' value='".$data['SeasonName']."'>";
		echo "<input type='hidden' name='season_id' value='".$data['SeasonID']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Published:</td>\n";
		echo "<td align='left' valign='top'>";

		if ($data['SeasonPublish'] == 1) {
			echo "<input type='checkbox' name='publish' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='publish' value='1'>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='modify_submit' value='Modify Season'>\n";
		echo "<input type='submit' name='delete_submit' value='Delete Season'>\n";
		echo "</form>\n";
		echo "<a href='".$PHP_SELF."?session_id=".$session."'>Add Season</a>\n";
		mysqli_free_result($get_season);
	}
	echo "</td>\n";
	echo "<td align='left' valign='top'>";
	$get_seasons = mysqli_query($db_connect, "SELECT * FROM team_season_names ORDER BY SeasonName") or die(mysqli_error());

	if (mysqli_num_rows($get_seasons) < 1) {
		echo "<b>No Seasons so far in Database</b>";
	} else {
		echo "<b>Seasons so far in Database:</b><br><br>";
		while($data = mysqli_fetch_array($get_seasons)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;season=".$data['SeasonID']."'>".$data['SeasonName']."</a>";
			if ($data['SeasonPublish'] == 0) {
				echo " (NB)<br>";
			} else {
				echo "<br>";
			}
		}
	}
	echo "<br>NB = This season is not published yet.";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>