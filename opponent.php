<?php
include('top.php');
$script_name = "opponent.php?".$_SERVER['QUERY_STRING'];

$id = $_REQUEST['id'];

if ($id == '' || !is_numeric($id)) {
	$id = 1;
}

if (isset($id)) {
	$get_opponent = mysqli_query($db_connect, "SELECT
		OpponentName,
		OpponentWWW,
		OpponentInfo,
		OpponentAllData
		FROM team_opponents
		WHERE OpponentID = '".$id."'
		LIMIT 1
	") or die(mysqli_error());
	$data = mysqli_fetch_array($get_opponent);
	mysqli_free_result($get_opponent);
} else {
	header("Location: matches.php");
	exit();
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
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'>";
$team_name = TEAM_NAME;
echo "<font class='bigname'><b>".$locale_history.": ".$team_name." - ".$data['OpponentName']."</b></font>";
if ($data['OpponentAllData'] == 0) {
	echo "<br><small><i>".$locale_opponent_stats_not_complete."</i></small>";
}
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle'><br>";
if ($data['OpponentInfo'] != '') {
	echo "".$data['OpponentInfo']."<br><br>";
}
if ($data['OpponentWWW'] != '') {
	echo "<a href='http://".$data['OpponentWWW']."' target='_blank'>".$data['OpponentWWW']."</a><br><br>";
}
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_players_played_for_this_team.":</b></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle'>";
$query = mysqli_query($db_connect, "
	SELECT P.PlayerID AS player_id,
	P.PlayerFirstName AS first_name,
	P.PlayerLastName AS last_name
	FROM team_players AS P, team_players_opponent AS PO
	WHERE P.PlayerID = PO.PlayerID
	AND PO.OpponentID = '".$id."'
	AND P.PlayerPublish = '1'
	ORDER BY last_name, first_name
") or die(mysqli_error());
$i = 1;
if (mysqli_num_rows($query) == 0) {
	echo "".$locale_no_players_have_played_for_this_team."";
} else {
	while ($data = mysqli_fetch_array($query)) {
		echo "<a href='player.php?id=".$data['player_id']."'>".$data['first_name']." ".$data['last_name']."</a>";
		if ($i < mysqli_num_rows($query)) {
			echo ", ";
		}
		$i++;
	}
}
mysqli_free_result($query);

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<td colspan='5'><b>".$locale_transfers."</b></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_year."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_player."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_from."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_to."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_transfer_value."</b></td>\n";
echo "</tr>\n";
$get_transfers = mysqli_query($db_connect, "SELECT
	S.SeasonName AS season_name,
	O.OpponentName AS opponent_name,
	O.OpponentID AS opponent_id,
	T.Value AS value,
	P.PlayerID AS player_id,
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	T.InOrOut AS in_or_out
	FROM (team_players P, team_season_names S, team_opponents O, team_transfers T)
	WHERE O.OpponentID = T.ClubID
	AND T.SeasonID = S.SeasonID
	AND O.OpponentID = '".$id."'
	AND P.PlayerID = T.PlayerID
	ORDER BY season_name
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($get_transfers)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	if ($data['in_or_out'] == 0) {
		$team_name_homeome = "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."";
		$team_name_awayway = TEAM_NAME;
	} else {
		$team_name_awayway = "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."";
		$team_name_homeome = TEAM_NAME;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['season_name']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$team_name_homeome."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$team_name_awayway."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$data['value']."</td>\n";
	echo "</tr>\n";
	$j++;
}
if ($j==1) {
	echo "<tr align='left'>\n";
	echo "<td colspan='4'>".$locale_no_transfers."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($get_transfers);

echo "</table>\n";
echo "<hr width='100%'>".$locale_match_type_filter.": \n";
echo "<select name='match_type_player'>\n";
echo "<option value='0'>".$locale_all."</option>\n";
while ($data = mysqli_fetch_array($get_types)) {
	if ($data['MatchTypeID'] == $default_match_type_id) {
		echo "<option value='".$data['MatchTypeID']."' selected>".$data['MatchTypeName']."</option>\n";
	} else {
		echo "<option value='".$data['MatchTypeID']."'>".$data['MatchTypeName']."</option>\n";
	}
}
mysqli_free_result($get_types);

echo "</select>\n";
echo "<input type='submit' name='submit2' value='".$locale_change."'>";
$query = mysqli_query($db_connect, "SELECT
	OpponentName As opponent_name,
	OpponentID AS opponent_id
	FROM team_opponents
	WHERE OpponentID != '1'
	ORDER BY opponent_name
") or die(mysqli_error());
echo "<br>".$locale_change_opponent.": \n";
echo "<select name='opponent_id'>\n";
while ($data = mysqli_fetch_array($query)) {
	if ($data['opponent_id'] == $id) {
		echo "<option value='".$data['opponent_id']."' selected>".$data['opponent_name']."</option>\n";
	} else {
		echo "<option value='".$data['opponent_id']."'>".$data['opponent_name']."</option>\n";
	}
}
echo "</select>\n";
echo "<input type='submit' name='change_opponent' value='".$locale_change."'>\n";
mysqli_free_result($query);

if ($default_match_type_id == 0) {
	$default_match_type_id = '%';
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
$get_matches = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchPlaceID AS place,
	M.MatchPublish AS publish,
	M.MatchNeutral AS neutral,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type_name,
	M.MatchAdditionalType AS add_typename,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchStadium AS stadium,
	M.MatchAttendance AS attendance
	FROM team_matches M, team_match_types MT
	WHERE MatchOpponent = '".$id."'
	AND MatchGoals IS NOT NULL
	AND MatchGoalsOpponent IS NOT NULL
	AND M.MatchTypeID = MT.MatchTypeID
	AND M.MatchTypeID LIKE '".$default_match_type_id."'
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$home_matches = 0;
$home_wins = 0;
$home_loses = 0;
$home_draws = 0;
$home_goals = 0;
$home_goals_against = 0;
$away_matches = 0;
$away_wins = 0;
$away_loses = 0;
$away_draws = 0;
$away_goals = 0;
$away_goals_against = 0;
$neutral_matches = 0;
$neutral_wins = 0;
$neutral_loses = 0;
$neutral_draws = 0;
$neutral_goals = 0;
$neutral_goals_against = 0;
$total_matches = 0;
$total_wins = 0;
$total_loses = 0;
$total_draws = 0;
$total_goals = 0;
$total_goals_against = 0;
$i = 0;
while ($data = mysqli_fetch_array($get_matches)) {
	$id[$i] = $data['id'];
	$date[$i] = $data['time'];
	$stadium[$i] = $data['stadium'];
	$attendance[$i] = $data['attendance'];
	$type[$i] = $data['type_name'];
	$add_type[$i] = $data['add_typename'];
	$place[$i] = $data['place'];
	$publish[$i] = $data['publish'];
	$neutral[$i] = $data['neutral'];
	$goals[$i] = $data['goals'];
	$goals_against[$i] = $data['goals_opponent'];
	if ($goals[$i] > $goals_against[$i]) {
		if ($neutral[$i] == 1) {
			$neutral_matches++;
			$neutral_wins++;
			$neutral_goals = $neutral_goals + $goals[$i];
			$neutral_goals_against = $neutral_goals_against + $goals_against[$i];
		} else {
			if ($place[$i] == 1) {
				$home_matches++;
				$home_wins++;
				$home_goals = $home_goals + $goals[$i];
				$home_goals_against = $home_goals_against + $goals_against[$i];
			} else if ($place[$i] == 2) {
				$away_matches++;
				$away_wins++;
				$away_goals = $away_goals + $goals[$i];
				$away_goals_against = $away_goals_against + $goals_against[$i];
			}
		}
	} else if ($goals_against[$i] > $goals[$i]) {
		if ($neutral[$i] == 1) {
			$neutral_matches++;
			$neutral_loses++;
			$neutral_goals = $neutral_goals + $goals[$i];
			$neutral_goals_against = $neutral_goals_against + $goals_against[$i];
		} else {
			if ($place[$i] == 1) {
				$home_matches++;
				$home_loses++;
				$home_goals = $home_goals + $goals[$i];
				$home_goals_against = $home_goals_against + $goals_against[$i];
			} else if ($place[$i] == 2) {
				$away_matches++;
				$away_loses++;
				$away_goals = $away_goals + $goals[$i];
				$away_goals_against = $away_goals_against + $goals_against[$i];
			}
		}
	} else if ($goals[$i] == $goals_against[$i]) {
		if ($neutral[$i] == 1) {
			$neutral_matches++;
			$neutral_draws++;
			$neutral_goals = $neutral_goals + $goals[$i];
			$neutral_goals_against = $neutral_goals_against + $goals_against[$i];
		} else {
			if ($place[$i] == 1) {
				$home_matches++;
				$home_draws++;
				$home_goals = $home_goals + $goals[$i];
				$home_goals_against = $home_goals_against + $goals_against[$i];
			} else if ($place[$i] == 2) {
				$away_matches++;
				$away_draws++;
				$away_goals = $away_goals + $goals[$i];
				$away_goals_against = $away_goals_against + $goals_against[$i];
			}
		}
	}
	$i++;
}
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_home_short." / ".$locale_away_short." / ".$locale_neutral_short."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_matches."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_win_short."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_draw_short."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_lose_short."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_goals."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>+ / -</b></td>\n";
echo "</tr>\n";
$bg_color_1 = BGCOLOR1;
$bg_color_2 = BGCOLOR2;
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$locale_home_short."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$home_matches."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$home_wins."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$home_draws."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$home_loses."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$home_goals." - ".$home_goals_against."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>";
$diff = $home_goals - $home_goals_against;
if ($diff >= 0) {
	echo "+".$diff."";
} else {
	echo "".$diff."";
}
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$locale_away_short."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$away_matches."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$away_wins."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$away_draws."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$away_loses."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$away_goals." - ".$away_goals_against."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>";
$diff = $away_goals - $away_goals_against;
if ($diff >= 0) {
	echo "+".$diff."";
} else {
	echo "".$diff."";
}
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$locale_neutral_short."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$neutral_matches."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$neutral_wins."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$neutral_draws."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$neutral_loses."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>".$neutral_goals." - ".$neutral_goals_against."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".$bg_color_1."'>";
$diff = $neutral_goals - $neutral_goals_against;
if ($diff >= 0) {
	echo "+".$diff."";
} else {
	echo "".$diff."";
}
echo "</td>\n";
echo "</tr>\n";
$total_matches = $home_matches + $away_matches + $neutral_matches;
$total_wins = $home_wins + $away_wins + $neutral_wins;
$total_loses = $home_loses + $away_loses + $neutral_loses;
$total_draws = $home_draws + $away_draws + $neutral_draws;
$total_goals = $home_goals + $away_goals + $neutral_goals;
$total_goals_against = $home_goals_against + $away_goals_against + $neutral_goals_against;
$diff = $total_goals - $total_goals_against;
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_total."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_matches."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_wins."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_draws."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_loses."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_goals." - ".$total_goals_against."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>";
if ($diff >= 0) {
	echo "+".$diff."";
} else {
	echo "".$diff."";
}
echo "</b></td>\n";
echo "</tr>\n";
echo "</table>\n";
if ($total_matches > 0) {
	echo "<br>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_date."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_match_type."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_home_short." / ".$locale_away_short." / ".$locale_neutral_short."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_final_score."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_stadium."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_attendance."</b></td>\n";
	echo "</tr>\n";
	$j = 0;
	while ($j < $i) {
		if ($j % 2 == 0) {
			$bg_color = "".$bg_color_1."";
		} else {
			$bg_color = "".$bg_color_2."";
		}
		if ($neutral[$j] == 1) {
			$p = "".$locale_neutral_short."";
		} else {
			if ($place[$j] == 1) {
				$p = "".$locale_home_short."";
			} else if ($place[$j] == 2) {
				$p = "".$locale_away_short."";
			}
		}
		echo "<tr>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$date[$j]."</td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
		if ($add_type[$j] == '') {
			echo "".$type[$j]."";
		} else {
			echo "".$type[$j]." / ".$add_type[$j]."";
		}
		echo "</td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$p."</td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
		if ($publish[$j] == 1) {
			echo "<a href='match_details.php?id=".$id[$j]."'>".$goals[$j]." - ".$goals_against[$j]."</a>";
		} else {
			echo "".$goals[$j]." - ".$goals_against[$j]."";
		}
		echo "</td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$stadium[$j]."</td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$attendance[$j]."</td>\n";
		echo "</tr>";
		$j++;
	}
	echo "</table>\n";
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>\n";

include('bottom.php');
?>