<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization failed.<br><a href='index.php'>Restart, please</a>";
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
		$opponent_name = trim($_POST['opponent_name']);
		$get_opponents = mysqli_query($db_connect, "SELECT OpponentName FROM team_opponents WHERE OpponentName = '$opponent_name'") or die(mysqli_error());

		if (mysqli_num_rows($get_opponents) > 0) {
			echo "There Is Already Opponent Named: ".$opponent_name." In Database.";
			exit();
		}
		mysqli_free_result($get_opponents);

		if (!get_magic_quotes_gpc()) {
			$opponent_name = addslashes($opponent_name);
		}
		if ($opponent_name != '') {
			mysqli_query($db_connect, "INSERT INTO team_opponents SET OpponentName = '$opponent_name'") or die(mysqli_error());
			header("Location: $PHP_SELF?session_id=$session");
		}
	} else if (isset($modify_submit)) {
		$opponent_id = $_POST['opponent_id'];
		$opponent_name = trim($_POST['opponent_name']);
		$opponent_www = str_replace('http://', '', trim($_POST['opponent_www']));
		$opponent_info = str_replace("\r\n", '<br>', trim($_POST['opponent_info']));
		$opponent_all_data = $_POST['opponent_all_data'];
		
		if (!get_magic_quotes_gpc()) {
			$opponent_name = addslashes($opponent_name);
			$opponent_info = addslashes($opponent_info);
		}
		if (!isset($opponent_all_data)){ $opponent_all_data = 0; }

		if ($opponent_name != '') {
			mysqli_query($db_connect, "UPDATE team_opponents SET
				OpponentName = '$opponent_name',
				OpponentWWW = '$opponent_www',
				OpponentInfo = '$opponent_info',
				OpponentAllData = '$opponent_all_data'
				WHERE OpponentID = '$opponent_id'
			") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");
	} else if (isset($delete_submit)) {
		$opponent_id = $_POST['opponent_id'];
		$remove_check = mysqli_query($db_connect, "SELECT MatchID FROM team_matches WHERE MatchOpponent = '$opponent_id'") or die(mysqli_error());

		if (mysqli_num_rows($remove_check) == 0) {
			mysqli_query($db_connect, "DELETE FROM team_opponents WHERE OpponentID = '$opponent_id'") or die(mysqli_error());
			header("Location: $PHP_SELF?session_id=$session");
		} else {
			echo "Permission To Delete Is Denied!<br>Opponent Is Already In Use.<br>Push Back Button To Get Back";
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
	echo "<table align='center' width='600'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top'>";
	
	if (!isset($action)) {
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Add Opponent</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>Opponent Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='opponent_name'></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='add_submit' value='Add Opponent'>\n";
		echo "</form>\n";
	} else if ($action == 'modify') {
		$opponent_id = $_REQUEST['opponent_id'];
		$get_opponent = mysqli_query($db_connect, "SELECT * FROM team_opponents WHERE OpponentID = '$opponent_id' LIMIT 1") or die(mysqli_error());
		$data = mysqli_fetch_array($get_opponent);
		$data['OpponentInfo'] = str_replace('<br>', "\r\n", $data['OpponentInfo']);
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Modify / Delete Opponent</h1>[<a href='../opponent.php?id=".$data['OpponentID']."'>Opponent Info in Stats Pages</a>]\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>Opponent Name:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='opponent_name' value='".$data['OpponentName']."'><input type='hidden' name='opponent_id' value='".$data['OpponentID']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>WWW-Address:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='opponent_www' value='".$data['OpponentWWW']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Club Info:<br>(You may use HTML)<br><textarea name='opponent_info' cols='40' rows='15'>".$data['OpponentInfo']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>All Data filled to Database?</td>\n";
		echo "<td align='left' valign='top'>";

		if ($data['OpponentAllData'] == 1) {
			echo "<input type='checkbox' name='opponent_all_data' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='opponent_all_data' value='1'>";
		}
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>";
		echo "<input type='submit' name='modify_submit' value='Modify Opponent'>";
		echo " <input type='submit' name='delete_submit' value='Delete Opponent'>";
		echo "</form>";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>\n";
		echo "<form enctype='multipart/form-data' method='POST' action='image_upload.php?session_id=".$session."'>\n";
		echo "<b>Upload Logo</b><br>(Max Width 60px)<br>\n";
		echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
		echo "<input name='image_file' type='file'>\n";
		echo "<input name='action' type='hidden' value='6'>\n";
		echo "<input type='hidden' name='opponent_id' value='".$data['OpponentID']."'>\n";
		echo "<input type='submit' name='submit' value='Upload'>\n";
		echo "</form>\n";
		$image_url = "../images/opponent_logo_".$data['OpponentID'].".jpg";
		$image_url2 = "../images/opponent_logo_".$data['OpponentID'].".png";
		$opponent_id = $data['OpponentID'];

		if (file_exists($image_url)) {
			echo "<img src='".$image_url."' alt=''>";
			echo "<br><a href='delete_picture.php?session_id=".$session."&amp;action=6&amp;type=jpg&amp;opponent_id=".$opponent_id."'>Delete Logo</a>";
		} else if (file_exists($image_url2)) {
			echo "<img src='".$image_url2."' alt=''>";
			echo "<br><a href='delete_picture.php?session_id=".$session."&amp;action=6&amp;type=png&amp;opponent_id=".$opponent_id."'>Delete Logo</a>";
		} else {
			echo "<img src='../images/no_image.png' alt='' width='100' height='100'>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		mysqli_free_result($get_opponent);
	}
	echo "</td>\n";
	echo "<td align='left' valign='top'>";
	$get_opponents = mysqli_query($db_connect, "SELECT * FROM team_opponents ORDER BY OpponentName") or die(mysqli_error());

	if (mysqli_num_rows($get_opponents) < 1) {
		echo "<b>No Opponents In Database</b>";
	} else {
		echo "<b>Opponents In Database:</b><br><br>";
		while($data = mysqli_fetch_array($get_opponents)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;opponent_id=".$data['OpponentID']."'>".$data['OpponentName']."</a><br>\n";
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