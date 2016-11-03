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
	if (isset($_POST['add_submit'])){ $add_submit = $_POST['add_submit']; }
	if (isset($_POST['modify_submit'])){ $modify_submit = $_POST['modify_submit']; }
	if (isset($_POST['delete_submit'])){ $delete_submit = $_POST['delete_submit']; }

	if (isset($add_submit)) {
		$player_position = trim($_POST['player_position']);
		$get_player_positions = mysqli_query($db_connect, "SELECT PlayerPositionName FROM team_player_positions WHERE PlayerPositionName = '$player_position'") or die(mysqli_error());

		if (mysqli_num_rows($get_player_positions) > 0) {
			echo "There Is Already Player Position Named: ".$player_position." In Database.<br>Please Write Another Player Position.";
			exit();
		}
		mysqli_free_result($get_player_positions);

		if (!get_magic_quotes_gpc()) {
			$player_position = addslashes($player_position);
		}
		if ($player_position != ''){
			mysqli_query($db_connect, "INSERT INTO team_player_positions SET PlayerPositionName = '$player_position'") or die(mysqli_error());
			header("Location: $PHP_SELF?session_id=$session");
		}
	} else if (isset($modify_submit)) {
		$player_position = trim($_POST['player_position']);
		$player_position_id = $_POST['player_position_id'];

		if (!get_magic_quotes_gpc()) {
			$player_position = addslashes($player_position);
		}
		if ($player_position != '') {
			mysqli_query($db_connect, "UPDATE team_player_positions SET PlayerPositionName = '$player_position' WHERE PlayerPositionID = '$player_position_id'") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");
	
	} else if (isset($delete_submit)) {
		$player_position_id = $_POST['player_position_id'];
		$get_player_position = mysqli_query($db_connect, "SELECT PlayerPositionID FROM team_players WHERE PlayerPositionID = '$player_position_id'") or die(mysqli_error());

		if (mysqli_num_rows($get_player_position) == 0) {
			mysqli_query($db_connect, "DELETE FROM team_player_positions WHERE PlayerPositionID = '$player_position_id'") or die(mysqli_error());
			header("Location: $PHP_SELF?session_id=$session");

		} else {
			echo "Permission To Delete Is Denied!<br>Player Position Is Already In Use.<br>Push Back Button To Get Back";
			exit();
		}
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
	echo "<td>\n";

	if (!isset($action)) {
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Add Player Position</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>Player Position Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='player_position'></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='add_submit' value='Add Position'>\n";
		echo "</form>\n";
	} else if ($action == 'modify') {
		$player_position_id = $_REQUEST['player_position_id'];
		$get_player_positions = mysqli_query($db_connect, "SELECT * FROM team_player_positions WHERE PlayerPositionID = '$player_position_id' LIMIT 1") or die(mysqli_error());
		$data = mysqli_fetch_array($get_player_positions);
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Modify / Delete Position</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
		echo "<td align='left' valign='top'>Player Position Name:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<input type='text' name='player_position' value='".$data['PlayerPositionName']."'>";
		echo "<input type='hidden' name='player_position_id' value='".$data['PlayerPositionID']."'>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='modify_submit' value='Modify Position'>\n";
		echo "<input type='submit' name='delete_submit' value='Delete Position'>\n";
		echo "</form>\n";
		echo "<br>";
		echo "<a href='".$PHP_SELF."?session_id=".$session."'>Add Player Position</a>\n";
		mysqli_free_result($get_player_positions);
	}
	echo "</td>\n";
	echo "<td align='left' valign='top'>";
	$get_match_types = mysqli_query($db_connect, "SELECT * FROM team_player_positions ORDER BY PlayerPositionName") or die(mysqli_error());

	if (mysqli_num_rows($get_match_types) < 1) {
		echo "<b>No Player Positions So Far In Database</b>";
	} else {
		echo "<b>Player Positions So Far In Database:</b><br><br>";
		while($data = mysqli_fetch_array($get_match_types)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;player_position_id=".$data['PlayerPositionID']."'>".$data['PlayerPositionName']."</a><br>";
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