<?php
include('top.php');
$script_name = "search.php?".$_SERVER['QUERY_STRING'];

echo "<form method='post' action='change.php'>\n";
echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='top'>";

if (isset ($_POST['submit'])) {
	$submit = $_POST['submit'];
}
if (isset($submit)) {
	echo "<b>".$locale_search_results.":</b><br><br>\n";
	$string = mysqli_real_escape_string($db_connect, trim($_POST['string']));

	if (!get_magic_quotes_gpc()) {
		$string = addslashes($string);
	}
	$get_players = mysqli_query($db_connect, "SELECT
		CONCAT(PlayerFirstName, ' ', PlayerLastName) AS player_name,
		PlayerID AS player_id
		FROM team_players
		WHERE PlayerFirstName LIKE '%$string%'
		OR PlayerLastName LIKE '%$string%'
	") or die(mysqli_error());

	if (mysqli_num_rows($get_players) > 0) {
		while($data = mysqli_fetch_array($get_players)) {
			echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a><br>\n";
		}
	}
	mysqli_free_result($get_players);

	$get_opps = mysqli_query($db_connect, "SELECT
		OpponentName AS opponent_name,
		OpponentID AS opponent_id
		FROM team_opponents
		WHERE OpponentName LIKE '%$string%'
	") or die(mysqli_error());

	if (mysqli_num_rows($get_opps) > 0) {
		while($data = mysqli_fetch_array($get_opps)) {
			echo "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a><br>\n";
		}
	}
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>\n";
include('bottom.php');
?>