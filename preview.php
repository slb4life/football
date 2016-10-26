<?php
include('top.php');
$script_name = "preview.php?".$_SERVER['QUERY_STRING'];

$team_name = TEAM_NAME;
$id = mysqli_real_escape_string($db_connect, $_REQUEST['id']);

echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
$query = mysqli_query($db_connect, "SELECT
	P.PreviewText AS preview_text,
	P.PreviewTextUnder AS preview_text_under,
	P.PreviewTickets AS preview_tickets,
	P.PreviewTV AS preview_tv,
	O.OpponentName AS opponent_name,
	O.OpponentID AS opponent_id,
	MT.MatchTypeName AS match_type_name,
	DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date,
	UNIX_TIMESTAMP(M.MatchDateTime) AS match_time_unix,
	M.MatchStadium AS match_stadium,
	M.MatchPlaceID AS match_place_id,
	M.MatchSeasonID AS match_season_id
	FROM team_matches M, team_opponents O, team_previews P, team_match_types MT
	WHERE O.OpponentID = M.MatchOpponent
	AND MT.MatchTypeID = M.MatchTypeID
	AND M.MatchID = P.PreviewMatchID
	AND P.PreviewMatchID = '$id'
	LIMIT 1
") or die(mysqli_error());
//$opponent_id_record = $opponent_id;
$data = mysqli_fetch_array($query);
$match_season_id = $data['match_season_id'];
$match_time_unix = $data['match_time_unix'];
$opponent_id = $data['opponent_id'];
mysqli_free_result($query);

$today = getdate();
$current_month = $today['mon'];
$current_mday = $today['mday'];
$current_year = $today['year'];
$current_hours = $today['hours'];
$current_minutes = $today['minutes'];
$current_seconds = $today['seconds'];
$current_time_unix = mktime($current_hours, $current_minutes, $current_seconds, $current_month, $current_mday, $current_year);
$match_time_unix = $match_time_unix + 6600;

if ($data['match_place_id'] == 1) {
	echo "<font class='bigname'>".$locale_preview.": ".$team_name." - ".$data['opponent_name']."</font>";
} else {
	echo "<font class='bigname'>".$locale_preview.": ".$data['opponent_name']." - ".$team_name."</font>";
}
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='top'>".$data['match_type_name']."<br>".$data['match_date']." - ".$data['match_stadium']."<br>";

if ($current_time_unix < $match_time_unix) {
	if ($data['preview_tickets'] != '') {
		echo "<br><b>".$locale_ticket_info.":</b> ".$data['preview_tickets']."<br>";
	} else {
		echo "<br>";
	}
	if ($data['preview_tv'] != '') {
		echo "<b>".$locale_tv_info.":</b> ".$data['preview_tv']."<br><br>";
	} else {
		if ($data['preview_tickets']) {
			echo "<br>";
		}
	}
} else {
	echo '<br>';
}
echo "".$data['preview_text']."<br><br>";
$get_squad = mysqli_query($db_connect, "SELECT
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	P.PlayerID AS player_id,
	P.PlayerPublish AS publish,
	P.PlayerPositionID AS player_position
	FROM team_players P, team_seasons S
	WHERE P.PlayerID = S.SeasonPlayerID
	AND S.SeasonID = '$id'
	AND P.PlayerPositionID != '5'
	AND P.PlayerInSquadList = '1'
	ORDER BY player_position
") or die(mysqli_error());

$injured_query = mysqli_query($db_connect, "SELECT
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	P.PlayerID AS player_id,
	P.PlayerPublish AS publish,
	I.InjuredReason AS injured_reason
	FROM team_players P, team_injured I
	WHERE P.PlayerID = I.InjuredPlayerID
	AND I.InjuredMatchID = '$id'
") or die(mysqli_error());

while ($data = mysqli_fetch_array($injured_query)) {
	$check[] = $data['player_id'];
}

if ($current_time_unix < $match_time_unix) {
	$suspended_query = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPublish AS publish,
		S.SuspendedReason AS suspended_reason
		FROM team_players P, team_suspended S
		WHERE P.PlayerID = S.SuspendedPlayerID
		AND S.SuspendedMatchID = '$id'
	") or die(mysqli_error());

	while ($data = mysqli_fetch_array($suspended_query)) {
		$check[] = $data['player_id'];
	}
	$qty_check = count($check);

	if (mysqli_num_rows($get_squad) > 0) {
		$squad_qty = mysqli_num_rows($get_squad);
		$ii = 1;
		echo "<b>".$locale_squad.":</b><br>";
		while ($data = mysqli_fetch_array($get_squad)) {
			$c = 0;
			for($i = 0 ; $i < $qty_check ; $i++) {
				if ($data['player_id'] == $check[$i]) {
					$c = 1;
				}
			}
			if ($c == 0) {
				if ($data['publish'] == 1) {
					echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
				} else {
					echo "".$data['player_name']."";
				}
				if ($ii < $squad_qty) {
					echo ", ";
				}
			}
			$ii++;
		}
		echo "<br>";
	} else {
		echo "<b>".$locale_squad.":</b><br>".$locale_nobody."<br>";
	}
	mysqli_free_result($get_squad);
	
	echo "<br>";

	if (mysqli_num_rows($injured_query) > 0) {
		mysqli_data_seek($injured_query, 0);
	}

	if (mysqli_num_rows($injured_query) > 0) {
		echo "<b>".$locale_injured.":</b><br>";
		while ($data = mysqli_fetch_array($injured_query)) {
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
			} else {
				echo "".$data['player_name']."";
			}
			if ($data['injured_reason'] == '') {
				echo "<br>";
			} else {
				echo " (".$data['injured_reason'].")<br>";
			}
		}
	} else {
		echo "<b>".$locale_injured.":</b><br>".$locale_nobody."<br>";
	}
	echo "<br>";
	mysqli_free_result($injured_query);

	if (mysqli_num_rows($suspended_query) > 0) {
		mysqli_data_seek($suspended_query, 0);
	}

	if (mysqli_num_rows($suspended_query) > 0) {
		echo "<b>".$locale_suspended.":</b><br>";
		while ($data = mysqli_fetch_array($suspended_query)) {
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
			} else {
				echo "".$data['player_name']."";
			}
			if ($data['suspended_reason'] == '') {
				echo "<br>";
			} else {
				echo " (".$data['suspended_reason'].")<br>";
			}
		}
	} else {
		echo "<b>".$locale_suspended.":</b><br>".$locale_nobody."<br>";
	}
	echo "<br>";
	mysqli_free_result($suspended_query);
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<td align='left' valign='middle'>".$data['preview_text_under']."<br><br></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle'></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='middle'><font class='bigname'><b>".$locale_history."</b></font></td>\n";
echo "</tr>\n";
echo "</table>\n";
$get_matches = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchPlaceID AS match_place_id,
	M.MatchPublish AS publish,
	M.MatchNeutral AS neutral,
	DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date,
	MT.MatchTypeName AS match_type_name,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent
	FROM team_matches M, team_match_types MT
	WHERE MatchOpponent = '$opponent_id'
	AND MatchGoals IS NOT NULL
	AND MatchGoalsOpponent IS NOT NULL
	AND M.MatchTypeID = MT.MatchTypeID
	ORDER BY match_date DESC
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
	$match_date[$i] = $data['match_date'];
	$match_type_name[$i] = $data['match_type_name'];
	$match_place_id[$i] = $data['match_place_id'];
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
			if ($match_place_id[$i] == 1) {
				$home_matches++;
				$home_wins++;
				$home_goals = $home_goals + $goals[$i];
				$home_goals_against = $home_goals_against + $goals_against[$i];
			} else if ($match_place_id[$i] == 2) {
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
			if ($match_place_id[$i] == 1) {
				$home_matches++;
				$home_loses++;
				$home_goals = $home_goals + $goals[$i];
				$home_goals_against = $home_goals_against + $goals_against[$i];
			} else if ($match_place_id[$i] == 2) {
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
			if ($match_place_id[$i] == 1) {
				$home_matches++;
				$home_draws++;
				$home_goals = $home_goals + $goals[$i];
				$home_goals_against = $home_goals_against + $goals_against[$i];
			} else if ($match_place_id[$i] == 2) {
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
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$locale_home_short."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$home_matches."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$home_wins."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$home_draws."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$home_loses."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$home_goals." - ".$home_goals_against."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>";
$diff = $home_goals - $home_goals_against;

if ($diff >= 0) {
	echo "+".$diff."";
} else {
	echo "".$diff."";
}
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$locale_away_short."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$away_matches."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$away_wins."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$away_draws."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$away_loses."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$away_goals." - ".$away_goals_against."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>";
$diff = $away_goals - $away_goals_against;

if ($diff >= 0) {
	echo "+".$diff."";
} else {
	echo "".$diff."";
}
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$locale_neutral_short."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$neutral_matches."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$neutral_wins."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$neutral_draws."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$neutral_loses."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>".$neutral_goals." - ".$neutral_goals_against."</td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(BGCOLOR1)."'>";
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
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_date_and_time."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_match_type."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_home_short." / ".$locale_away_short." / ".$locale_neutral_short."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_final_score."</b></td>\n";
echo "</tr>\n";
$j = 0;
while ($j < $i) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	if ($neutral[$j] == 1) {
		$home_away_netral = "".$locale_neutral_short."";
	} else {
		if ($match_place_id[$j] == 1) {
			$home_away_netral = "".$locale_home_short."";
		} else if ($match_place_id[$j] == 2) {
			$home_away_netral = "".$locale_away_short."";
		}
	}
	echo "<tr>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$match_date[$j]."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$match_type_name[$j]."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$home_away_netral."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($publish[$j] == 1) {
		echo "<a href='match_details.php?id=".$id[$j]."'>".$goals[$j]." - ".$goals_against[$j]."</a>";
	} else {
		echo "".$goals[$j]." - ".$goals_against[$j]."";
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
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