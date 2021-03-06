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
	if (isset($_REQUEST['transfer_id'])){ $transfer_id = $_REQUEST['transfer_id']; }
	if (isset($_POST['add'])){ $add = $_POST['add']; }
	if (isset($_POST['player_id'])){ $player_id = $_POST['player_id']; }
	if (isset($_POST['opponent_id'])){ $opponent_id = $_POST['opponent_id']; }
	if (isset($_POST['transfer_status'])){ $transfer_status = $_POST['transfer_status']; }
	if (isset($_POST['transfer_value'])){ $transfer_value = $_POST['transfer_value']; }

	if (get_magic_quotes_gpc()) {
		$transfer_value = addslashes($transfer_value);
	}
	if (isset($add)) {
		mysqli_query($db_connect, "INSERT INTO team_transfers SET TransferSeasonID = '$season_id', TransferPlayerID = '$player_id', TransferOpponentID = '$opponent_id', TransferStatus = '$transfer_status', TransferValue = '$transfer_value'") or die(mysqli_error());
		header("Location: $HTTP_REFERER");

	} else if (isset($action) == 'delete') {
		$transfer_id = $_REQUEST['transfer_id'];
		mysqli_query($db_connect, "DELETE FROM team_transfers WHERE TransferID = '$transfer_id'") or die(mysqli_error());
		header("Location: $HTTP_REFERER");

	} else {
		echo "<!DOCTYPE html>\n";
		echo "<html>\n";
		echo "<head>\n";
		echo "<title>Admin Area</title>\n";
		echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
		echo "</head>\n";
		echo "<body>\n";
		include('menu.php');
		echo "<table align='center' width='600'><tr>\n";
		echo "<td align='left' valign='top'><h1>Transfers</h1>\n";
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
		echo "<td align='left' valign='top'>";
		echo "<select name='player_id'>";
		$get_players = mysqli_query($db_connect, "SELECT
			CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
			P.PlayerID AS player_id
			FROM team_players AS P, team_seasons AS S
			WHERE P.PlayerID = S.SeasonPlayerID
			AND S.SeasonID = '$season_id'
			ORDER BY player_name
		") or die(mysqli_error());
		$get_total = mysqli_num_rows($get_players);

		if ($get_total < 1) {
			echo "<b>No Players</b>";
		} else {
			while($data = mysqli_fetch_array($get_players)) {
				echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\r\n";
			}
		}
		echo "</select>\n";
		echo "<select name='transfer_status'>\n";
		echo "<option value='0'>From</option>\n";
		echo "<option value='1'>To</option>\n";
		echo "</select>\n";
		echo "<select name='opponent_id'>";
		$get_opponents = mysqli_query($db_connect, "SELECT * FROM team_opponents ORDER BY OpponentName") or die(mysqli_error());
		
		if (mysqli_num_rows($get_opponents) < 1) {
			echo "<b>No Opponents</b>";
		} else {
			while($data = mysqli_fetch_array($get_opponents)) {
				echo "<option value='".$data['OpponentID']."'>".$data['OpponentName']."</option>\r\n";
			}
		}
		echo "</select>\n";
		echo "<br><br>";
		echo "Transfer Value: <input type='text' name='transfer_value'>\n";
		echo "<input type='submit' name='add' value='Add Transfer'>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</form>\n";
		echo "</td>\n";
		echo "<td align='left' valign='top'>";
		$get_transfers = mysqli_query($db_connect, "SELECT
			CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
			T.TransferID AS transfer_id,
			T.TransferStatus AS transfer_status
			FROM (team_transfers AS T, team_players AS P)
			WHERE T.TransferPlayerID = P.PlayerID
			AND T.TransferSeasonID = '$season_id'
			ORDER BY transfer_id
		") or die(mysqli_error());

		if (mysqli_num_rows($get_transfers) < 1) {
			echo "<b>No Trasfers For This Season</b>";
		} else {
			echo "<b>Delete Transfer:</b><br><br>";
			while($data = mysqli_fetch_array($get_transfers)) {
				if ($data['transfer_status'] == 0) {
					echo "In: ";
				} else {
					echo "Out: ";
				}
				echo "".$data['player_name']." <a href='".$PHP_SELF."?session_id=".$session."&amp;action=delete&amp;transfer_id=".$data['transfer_id']."'><img src='../images/remove.png' border='0' alt='Remove'></a><br>";
			}
		}
		mysqli_free_result($get_transfers);
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>