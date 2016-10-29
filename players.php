<?php
include('top.php');
$script_name = "players.php?".$_SERVER['QUERY_STRING'];

switch (PRINT_DATE) {
	case 1: {
		$how_to_print_in_report = "%d.%m.%Y";
	}
	break;
	case 2: {
		$how_to_print_in_report = "%m.%d.%Y";
	}
	break;
	case 3: {
		$how_to_print_in_report = "%b %D %Y";
	}
	break;
}
echo "<form method='post' action='change.php'>\n";
echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr align='left'>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><font class='bigname'><b>".$locale_players."</b></font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_name."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_dob."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_pob."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_height."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_weight."</b></td>\n";
echo "</tr>\n";
$default_season = DEFAULT_SEASON;
$query = mysqli_query($db_connect, "SELECT
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	P.PlayerID AS player_id,
	P.PlayerPublish AS publish,
	P.PlayerPositionID AS player_position_id,
	DATE_FORMAT(P.PlayerDOB, '$how_to_print_in_report') AS player_dob,
	P.PlayerPOB AS player_pob,
	P.PlayerHeight AS player_height,
	P.PlayerWeight AS player_weight,
	P.PlayerNumber AS player_number
	FROM (team_players P, team_seasons S)
	WHERE P.PlayerID = S.SeasonPlayerID
	AND S.SeasonID = '$default_season'
	AND P.PlayerInSquadList = '1'
	ORDER BY player_position_id, player_number
") or die(mysqli_error());
echo "<tr>\n";
echo "<td colspan='5' align='left' bgcolor='".(CELLBGCOLORTOP)."'><b>".$locale_goalkeepers."</b></td>\n";
echo "</tr>\n";
$j = 1;
$player_position_id = 1;
while($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	if ($data['player_position_id'] > 1) {
		if ($player_position_id != $data['player_position_id']) {
			switch ($player_position_id) {
				case 1: {
					echo "<tr>\n";
					echo "<td colspan='5' align='left' bgcolor='".(CELLBGCOLORTOP)."'><b>".$locale_defenders."</b></td>\n";
					echo "</tr>\n";
				}
				break;
				case 2: {
					echo "<tr>\n";
					echo "<td colspan='5' align='left' bgcolor='".(CELLBGCOLORTOP)."'><b>".$locale_midfields."</b></td>\n";
					echo "</tr>";
				}
				break;
				case 3: {
					echo "<tr>\n";
					echo "<td colspan='5' align='left' bgcolor='".(CELLBGCOLORTOP)."'><b>".$locale_forwards."</b></td>\n";
					echo "</tr>\n";
				}
				break;
			}
		}
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>";

	if ($data['publish'] == 1) {
		echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a> (#".$data['player_number'].")";
	} else {
		echo "".$data['player_name']." (#".$data['player_number'].")";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$data['player_dob']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$data['player_pob']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$data['player_height']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$data['player_weight']."</td>\n";
	echo "</tr>\n";
	$j++;
	$player_position_id = $data['player_position_id'];
}
mysqli_free_result($query);

echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

include('bottom.php');
?>