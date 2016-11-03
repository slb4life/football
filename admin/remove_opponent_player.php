<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
	$player_id = $_REQUEST['player_id'];
	$opponent_id = $_REQUEST['opponent_id'];

	include('user.php');
	$db_connect = mysqli_connect("$db_host", "$db_user", "$db_password", "$db_name") or die(mysqli_error());

	mysqli_query($db_connect, "DELETE FROM team_players_opponent WHERE PlayerID = '$player_id' AND OpponentID = '$opponent_id' LIMIT 1") or die(mysqli_error());
	header("Location: $HTTP_REFERER");
}
?>