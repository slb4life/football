<?php
include('top.php');
$script_name = "opponent_list.php?".$_SERVER['QUERY_STRING'];

echo "<form method='post' action='change.php'>\n";
echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='4'><font class='bigname'>".$locale_opponent_list."</font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>0-C</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>D-L</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>M-R</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>S-Z</b></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='center' valign='top'>";
$query = mysqli_query($db_connect, "SELECT
	OpponentName AS opponent_name,
	OpponentID AS opponent_id
	FROM team_opponents
	WHERE OpponentName REGEXP '^[0-C]'
	ORDER BY opponent_name
") or die(mysqli_error());

while ($data = mysqli_fetch_array($query)) {
	if ($data['opponent_id'] = 1) {
		echo "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a><br>\n";
	}
}
mysqli_free_result($query);
	
echo "</td>\n";
echo "<td align='center' valign='top'>";
$query = mysqli_query($db_connect, "SELECT
	OpponentName AS opponent_name,
	OpponentID AS opponent_id
	FROM team_opponents
	WHERE OpponentName REGEXP '^[D-L]'
	ORDER BY opponent_name
") or die(mysqli_error());

while($data = mysqli_fetch_array($query)) {
	if ($data['opponent_id'] = 1) {
		echo "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a><br>\n";
	}
}
mysqli_free_result($query);

echo "</td>\n";
echo "<td align='center' valign='top'>";
$query = mysqli_query($db_connect, "SELECT
	OpponentName AS opponent_name,
	OpponentID AS opponent_id
	FROM team_opponents
	WHERE OpponentName REGEXP '^[M-R]'
	ORDER BY opponent_name
") or die(mysqli_error());

while ($data = mysqli_fetch_array($query)) {
	if ($data['opponent_id'] = 1) {
		echo "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a><br>\n";
	}
}
mysqli_free_result($query);

echo "</td>\n";
echo "<td align='center' valign='top'>";
$query = mysqli_query($db_connect, "SELECT
	OpponentName AS opponent_name,
	OpponentID AS opponent_id
	FROM team_opponents
	WHERE OpponentName REGEXP '^[S-Z]'
	ORDER BY opponent_name
") or die(mysqli_error());

while ($data = mysqli_fetch_array($query)) {
	if ($data['opponent_id'] = 1) {
		echo "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a><br>\n";
	} 
}
mysqli_free_result($query);

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