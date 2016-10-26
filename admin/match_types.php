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
	
	if (isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
	if (isset($_POST['add_submit'])){ $add_submit = $_POST['add_submit']; }
	if (isset($_POST['modify_submit'])){ $modify_submit = $_POST['modify_submit']; }
	if (isset($_POST['delete_submit'])){ $delete_submit = $_POST['delete_submit']; }

	if (isset($add_submit)) {
		$match_type = trim($_POST['match_type']);
		$query = mysqli_query($db_connect, "SELECT MatchTypeName FROM team_match_types WHERE MatchTypeName = '$match_type'") or die(mysqli_error());

		if (mysqli_num_rows($query) > 0) {
			echo "There is already Match Type Named: ".$match_type." in Database.<br>Please write another Match Type.";
			exit();
		}
		mysqli_free_result($query);

		if (!get_magic_quotes_gpc()) {
			$match_type = addslashes($match_type);
		}
		if ($match_type != ''){
			mysqli_query($db_connect, "INSERT INTO team_match_types SET MatchTypeName = '$match_type'") or die(mysqli_error());
			header("Location: $PHP_SELF?session_id=$session");
		}
	} else if (isset($modify_submit)) {
		$match_type = trim($_POST['match_type']);
		$match_type_id = $_POST['match_type_id'];

		if (!get_magic_quotes_gpc()) {
			$match_type = addslashes($match_type);
		}
		if ($match_type != '') {
			mysqli_query($db_connect, "UPDATE team_match_types SET MatchTypeName = '$match_type' WHERE MatchTypeID = '$match_type_id'") or die(mysqli_error());
		}
		header("Location: $PHP_SELF?session_id=$session");
	
	} else if (isset($delete_submit)) {
		$match_type_id = $_POST['match_type_id'];
		$query = mysqli_query($db_connect, "SELECT MatchTypeID FROM team_matches WHERE MatchTypeID = '$match_type_id'") or die(mysqli_error());

		if (mysqli_num_rows($query) == 0) {
			mysqli_query($db_connect, "DELETE FROM team_match_types WHERE MatchTypeID = '$match_type_id'") or die(mysqli_error());
			header("Location: $PHP_SELF?session_id=$session");

		} else {
			echo "Permission to Delete is Denied!<br>Match Type is already in use.<br>Push back Button to get back";
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
		echo "<h1>Add Match Type (Leagues, Cups)</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>Match Type Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='match_type'></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='add_submit' value='Add Type'>\n";
		echo "</form>\n";
	} else if ($action == 'modify') {
		$match_type_id = $_REQUEST['match_type'];
		$get_match_type = mysqli_query($db_connect, "SELECT * FROM team_match_types WHERE MatchTypeID = '$match_type_id' LIMIT 1") or die(mysqli_error());
		$data = mysqli_fetch_array($get_match_type);
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Modify / Delete type</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
		echo "<td align='left' valign='top'>Match Type Name:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<input type='text' name='match_type' value='".$data['MatchTypeName']."'>";
		echo "<input type='hidden' name='match_type_id' value='".$data['MatchTypeID']."'>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='modify_submit' value='Modify Type'>\n";
		echo "<input type='submit' name='delete_submit' value='Delete Type'>\n";
		echo "</form>\n";
		echo "<a href='".$PHP_SELF."?session_id=".$session."'>Add Match Type</a>\n";
		mysqli_free_result($get_match_type);
	}
	echo "</td>\n";
	echo "<td align='left' valign='top'>\n";
	$get_match_types = mysqli_query($db_connect, "SELECT * FROM team_match_types ORDER BY MatchTypeName") or die(mysqli_error());

	if (mysqli_num_rows($get_match_types) < 1) {
		echo "<b>No Match Types so far in Database</b>";
	} else {
		echo "<b>Match Types so far in Database:</b><br><br>";
		while($data = mysqli_fetch_array($get_match_types)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;match_type=".$data['MatchTypeID']."'>".$data['MatchTypeName']."</a><br>";
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