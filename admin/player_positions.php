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

	echo "<!DOCTYPE html>\n";
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');
	echo "<table align='center' width='600'><tr>\n";
	echo "<td align='center' valign='top'>\n";
	echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."&amp;action=player_selected'>\n";
	echo "<h1>Injured Players</h1>\n";
	echo "Select Injured Player: ";
	echo "<select name='injured_player'>";
	$get_players = mysqli_query($db_connect, "SELECT
		CONCAT(team_players.PlayerFirstName, ' ', team_players.PlayerLastName) AS player_name,
		team_players.PlayerID AS player_id
		FROM team_players,team_seasons
		WHERE team_players.PlayerID = team_seasons.SeasonPlayerID
		AND team_seasons.SeasonID = '$season_id'
		ORDER BY player_name
	") or die(mysqli_error());
	while($data = mysqli_fetch_array($get_players)) {
		echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
	}
	mysqli_free_result($get_players);

	echo "</select>\n";
	echo "<input type='submit' name='player_selected' value='Select Player'>\n";
	echo "</form>\n";
	echo "</td>\n";
	echo "</tr>\n";

	if ($action == 'player_selected') {
		echo "<tr>\n";
		echo "<td>Player has been Selected as Injured.</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>