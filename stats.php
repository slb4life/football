<?php
include('top.php');
$script_name = "stats.php?".$_SERVER['QUERY_STRING'];

if (isset($_REQUEST['sort'])){ $sort = $_REQUEST['sort']; }

if (!isset($sort)) {
	$sort = 'number';
}
echo "<form method='post' action='change.php'>\n";
echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
echo "<table width='100%' align='center' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>".$locale_match_type_filter.": \n";
echo "<select name='match_type_player'>\n";
echo "<option value='0'>".$locale_all."</option>\n";
while($data = mysqli_fetch_array($get_match_types)) {
	if ($data['MatchTypeID'] == $default_match_type_id) {
		echo "<option value='".$data['MatchTypeID']."' selected>".$data['MatchTypeName']."</option>\n";
	} else {
		echo "<option value='".$data['MatchTypeID']."'>".$data['MatchTypeName']."</option>\n";
	}
}
mysqli_free_result($get_match_types);

echo "</select>\n";
echo "<input type='submit' name='submit2' value='".$locale_change."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='right' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'>&nbsp;</td>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?sort=name'>".$locale_name."</a></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?sort=minutes'>".$locale_minutes_short."</a></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?sort=apps'>".$locale_openings_short."</a></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?sort=ins'>".$locale_to_the_field_short."</a></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?sort=goals'>".$locale_goals_short."</a></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?sort=assists'>".$locale_goal_assists_short."</a></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?sort=yellows'>".$locale_yellow_cards_short."</a></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?sort=reds'>".$locale_red_cards_short."</a></td>\n";
echo "</tr>\n";
if ($default_season_id != 0 && $default_match_type_id != 0) {
	$get_players = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		P.PlayerLastName AS player_last_name,
		P.PlayerFirstName AS player_first_name,
		P.PlayerPublish AS publish,
		P.PlayerNumber AS player_number,
		COUNT( G.GoalPlayerID ) AS goals
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_goals G ON G.GoalPlayerID = S.SeasonPlayerID AND G.GoalMatchID = M.MatchID AND G.GoalOwn = '0'
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_assists = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(GA.GoalAssistPlayerID) AS assists
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_goal_assists GA ON GA.GoalAssistPlayerID = S.SeasonPlayerID AND GA.GoalAssistMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_yellows = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(YC.YellowCardPlayerID) AS yellows
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_yellow_cards YC ON YC.YellowCardPlayerID = S.SeasonPlayerID AND YC.YellowCardMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_reds = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(RC.RedCardPlayerID) AS reds
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_red_cards RC ON RC.RedCardPlayerID = S.SeasonPlayerID AND RC.RedCardMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	")
	or die(mysqli_error());
	$get_apps = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(A.AppearancePlayerID) AS apps
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_appearances A ON A.AppearancePlayerID = S.SeasonPlayerID AND A.AppearanceMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_ins = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(SU.SubstitutionPlayerIDIn) AS ins
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_substitutions SU ON SU.SubstitutionPlayerIDIn = S.SeasonPlayerID AND SU.SubstitutionMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
} else if ($default_season_id == 0 && $default_match_type_id != 0) {
	$get_players = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		P.PlayerLastName AS player_last_name,
		P.PlayerFirstName AS player_first_name,
		P.PlayerPublish AS publish,
		P.PlayerNumber AS player_number,
		COUNT( G.GoalPlayerID ) AS goals
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_goals G ON G.GoalPlayerID = S.SeasonPlayerID AND G.GoalMatchID = M.MatchID AND G.GoalOwn = '0'
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_assists = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(GA.GoalAssistPlayerID) AS assists
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_goal_assists GA ON GA.GoalAssistPlayerID = S.SeasonPlayerID AND GA.GoalAssistMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_yellows = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(YC.YellowCardPlayerID) AS yellows
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_yellow_cards YC ON YC.YellowCardPlayerID = S.SeasonPlayerID AND YC.YellowCardMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_reds = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(RC.RedCardPlayerID) AS reds
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_red_cards RC ON RC.RedCardPlayerID = S.SeasonPlayerID AND RC.RedCardMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_apps = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(A.AppearancePlayerID) AS apps
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_appearances A ON A.AppearancePlayerID = S.SeasonPlayerID AND A.AppearanceMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_ins = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(SU.SubstitutionPlayerIDIn) AS ins
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID = '$default_match_type_id'
		LEFT OUTER JOIN team_substitutions SU ON SU.SubstitutionPlayerIDIn = S.SeasonPlayerID AND SU.SubstitutionMatchID = M.MatchID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
} else if ($default_season_id != 0 && $default_match_type_id == 0) {
	$get_players = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		P.PlayerLastName AS player_last_name,
		P.PlayerFirstName AS player_first_name,
		P.PlayerPublish AS publish,
		P.PlayerNumber AS player_number,
		COUNT( G.GoalPlayerID ) AS goals
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_goals G ON G.GoalPlayerID = S.SeasonPlayerID AND G.GoalOwn = '0' AND G.GoalSeasonID = '$default_season_id'
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_assists = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(GA.GoalAssistPlayerID) AS assists
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_goal_assists GA ON GA.GoalAssistPlayerID = S.SeasonPlayerID AND GA.GoalAssistSeasonID = '$default_season_id'
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_yellows = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(YC.YellowCardPlayerID) AS yellows
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_yellow_cards YC ON YC.YellowCardPlayerID = S.SeasonPlayerID AND YC.YellowCardSeasonID = '$default_season_id'
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_reds = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(RC.RedCardPlayerID) AS reds
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_red_cards RC ON RC.RedCardPlayerID = S.SeasonPlayerID AND RC.RedCardSeasonID = '$default_season_id'
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_apps = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(A.AppearancePlayerID) AS apps
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_appearances A ON A.AppearancePlayerID = S.SeasonPlayerID AND A.AppearanceSeasonID = '$default_season_id'
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_ins = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT(SU.SubstitutionPlayerIDIn) AS ins
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID = '$default_season_id'
		LEFT OUTER JOIN team_substitutions SU ON SU.SubstitutionPlayerIDIn = S.SeasonPlayerID AND SU.SubstitutionSeasonID = '$default_season_id'
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
} else if ($default_season_id == 0 && $default_match_type_id == 0) {
	$get_players = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		P.PlayerLastName AS player_last_name,
		P.PlayerFirstName AS player_first_name,
		P.PlayerPublish AS publish,
		P.PlayerNumber AS player_number,
		COUNT( G.GoalPlayerID ) AS goals
		FROM team_players AS P
		LEFT OUTER JOIN team_goals G ON G.GoalPlayerID = P.PlayerID AND G.GoalOwn = '0'
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_assists = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT( GA.GoalAssistPlayerID ) AS assists
		FROM team_players AS P
		LEFT OUTER JOIN team_goal_assists GA ON GA.GoalAssistPlayerID = P.PlayerID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_yellows = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT( YC.YellowCardPlayerID ) AS yellows
		FROM team_players AS P
		LEFT OUTER JOIN team_yellow_cards YC ON YC.YellowCardPlayerID = P.PlayerID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_reds = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT( RC.RedCardPlayerID ) AS reds
		FROM team_players AS P
		LEFT OUTER JOIN team_red_cards RC ON RC.RedCardPlayerID = P.PlayerID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_apps = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT( A.AppearancePlayerID ) AS apps
		FROM team_players AS P
		LEFT OUTER JOIN team_appearances A ON A.AppearancePlayerID = P.PlayerID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
	$get_ins = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		COUNT( SU.SubstitutionPlayerIDIn ) AS ins
		FROM team_players AS P
		LEFT OUTER JOIN team_substitutions SU ON SU.SubstitutionPlayerIDIn = P.PlayerID
		WHERE P.PlayerID != '' AND P.PlayerPositionID != '5'
		GROUP BY player_id
		ORDER BY player_id
	") or die(mysqli_error());
}
$i = 0;
while($data = mysqli_fetch_array($get_players)) {
	$players[$i] = $data['player_id'];
	$minutes[$i] = 0;

	if ($data['player_last_name'] == '') {
		$names[$i] = $data['player_first_name'];
	} else {
		$names[$i] = $data['player_last_name'] . ', ' . $data['player_first_name'];
	}
	if ($default_season_id == 0) {
		$tdefault_season_id = '%';
	} else {
		$tdefault_season_id = $default_season_id;
	}
	if ($default_match_type_id == 0) {
		$tdefault_match_type_id = '%';
	} else {
		$tdefault_match_type_id = $default_match_type_id;
	}
	$query = mysqli_query($db_connect, "SELECT
		S.SubstitutionMinute AS minute,
		M.MatchOvertime AS match_overtime
		FROM team_substitutions AS S, team_matches AS M
		WHERE S.SubstitutionPlayerIDIn = '$players[$i]'
		AND S.SubstitutionSeasonID LIKE '$tdefault_season_id'
		AND M.MatchID = S.SubstitutionMatchID
		AND M.MatchTypeID LIKE '$tdefault_match_type_id'
	") or die(mysqli_error());
	while($tdata = mysqli_fetch_array($query)) {
		if ($tdata['match_overtime'] == 0) {
			$match_minutes = 90;
		} else {
			$match_minutes = 120;
		}
		$minutes[$i] = $minutes[$i] + ($match_minutes-$tdata['minute']);
	}
	mysqli_free_result($query);

	$query = mysqli_query($db_connect, "SELECT *
		FROM team_appearances AS A, team_matches AS M
		WHERE A.AppearancePlayerID = '$players[$i]'
		AND A.AppearanceSeasonID LIKE '$tdefault_season_id'
		AND M.MatchID = A.AppearanceMatchID
		AND M.MatchTypeID LIKE '$tdefault_match_type_id'
	") or die(mysqli_error());
	while($tdata = mysqli_fetch_array($query)) {
		if (isset($tdata['team_matches.MatchOvertime']) == 0) {
			$match_minutes = 90;
		} else {
			$match_minutes = 120;
		}
		$minutes[$i] = $minutes[$i] + $match_minutes;
	}
	mysqli_free_result($query);

	$query = mysqli_query($db_connect, "SELECT
		S.SubstitutionMinute AS minute,
		M.MatchOvertime AS match_overtime
		FROM team_substitutions AS S, team_matches AS M
		WHERE S.SubstitutionPlayerIDOut = '$players[$i]'
		AND S.SubstitutionSeasonID LIKE '$tdefault_season_id'
		AND M.MatchID = S.SubstitutionMatchID
		AND M.MatchTypeID LIKE '$tdefault_match_type_id'
	") or die(mysqli_error());
	while($tdata = mysqli_fetch_array($query)) {
		if ($tdata['match_overtime'] == 0) {
			$match_minutes = 90;
		} else {
			$match_minutes = 120;
		}
		$minutes[$i] = $minutes[$i] - ($match_minutes-$tdata['minute']);
	}
	mysqli_free_result($query);

	$query = mysqli_query($db_connect, "SELECT
		RC.RedCardMinute AS minute,
		M.MatchOvertime AS match_overtime
		FROM team_red_cards AS RC, team_matches AS M
		WHERE RC.RedCardPlayerID = '$players[$i]'
		AND RC.RedCardSeasonID LIKE '$tdefault_season_id'
		AND M.MatchID = RC.RedCardMatchID
		AND M.MatchTypeID LIKE '$tdefault_match_type_id'
	") or die(mysqli_error());
	while($tdata = mysqli_fetch_array($query)) {
		if ($tdata['match_overtime'] == 0) {
			$match_minutes = 90;
		} else {
			$match_minutes = 120;
		}
		$minutes[$i] = $minutes[$i] - ($match_minutes-$tdata['minute']);
	}
	mysqli_free_result($query);

	$publish[$i] = $data['publish'];
	$numbers[$i] = $data['player_number'];
	$goals[$i] = $data['goals'];
	$i++;
}
$get_total = mysqli_num_rows($get_players);
mysqli_free_result($get_players);

$i = 0;
while($data = mysqli_fetch_array($get_assists)) {
	$assists[$i] = $data['assists'];
	$i++;
}
mysqli_free_result($get_assists);

$i = 0;
while($data = mysqli_fetch_array($get_yellows)) {
	$yellows[$i] = $data['yellows'];
	$i++;
}
mysqli_free_result($get_yellows);

$i = 0;
while($data = mysqli_fetch_array($get_reds)) {
	$reds[$i] = $data['reds'];
	$i++;
}
mysqli_free_result($get_reds);

$i = 0;
while($data = mysqli_fetch_array($get_apps)) {
	$apps[$i] = $data['apps'];
	$i++;
}
mysqli_free_result($get_apps);

$i = 0;
while($data = mysqli_fetch_array($get_ins)) {
	$ins[$i] = $data['ins'];
	$i++;
}
mysqli_free_result($get_ins);

if ($get_total > 0) {
	switch ($sort) {
		case 'name':
		array_multisort($names, SORT_ASC, SORT_STRING, $players, $numbers, $goals, $apps, $assists, $yellows, $reds, $ins, $minutes, $publish);
		break;
		case 'number':
		array_multisort($numbers, SORT_ASC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $players, $goals, $apps, $assists, $yellows, $reds, $ins, $minutes, $publish);
		break;
		case 'minutes':
		array_multisort($minutes, SORT_DESC, SORT_NUMERIC, $apps, SORT_DESC, SORT_NUMERIC, $ins, SORT_DESC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $players, $goals, $numbers, $assists, $yellows, $reds, $minutes, $publish);
		break;
		case 'apps':
		array_multisort($apps, SORT_DESC, SORT_NUMERIC, $ins, SORT_DESC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $players, $goals, $numbers, $assists, $yellows, $reds, $minutes, $publish);
		break;
		case 'ins':
		array_multisort($ins, SORT_DESC, SORT_NUMERIC, $apps, SORT_DESC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $players, $goals, $numbers, $assists, $yellows, $reds, $minutes, $publish);
		break;
		case 'goals':
		array_multisort($goals, SORT_DESC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $players, $numbers, $apps, $assists, $yellows, $reds, $ins, $minutes, $publish);
		break;
		case 'assists':
		array_multisort($assists, SORT_DESC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $players, $goals, $apps, $numbers, $yellows, $reds, $ins, $minutes, $publish);
		break;
		case 'yellows':
		array_multisort($yellows, SORT_DESC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $players, $goals, $apps, $numbers, $assists, $reds, $ins, $minutes, $publish);
		break;
		case 'reds':
		array_multisort($reds, SORT_DESC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $players, $goals, $apps, $yellows, $numbers, $ins, $minutes, $publish);
		break;
	}
	$i = 0;
	$j = 1;
	while($i < $get_total) {
		if ($j % 2 == 0) {
			$bg_color = '#'.BGCOLOR1;
		} else {
			$bg_color = '#'.BGCOLOR2;
		}
		echo "<tr>\n";
		echo "<td align='right' valign='middle' bgcolor='".$bg_color."'>";
		switch ($sort) {
			case 'apps': {
				if ($i > 0) {
					if ($apps[$i] == $apps[$i-1]) {
						echo "&nbsp;";
						$j++;
					} else {
						echo "".$j.".";
						$j++;
					}
				} else {
					echo "".$j.".";
					$j++;
				}
			}
			break;
			case 'minutes': {
				if ($i > 0) {
					if ($minutes[$i] == $minutes[$i-1]) {
						echo "&nbsp;";
						$j++;
					} else {
						echo "".$j.".";
						$j++;
					}
				} else {
					echo "".$j.".";
					$j++;
				}
			}
			break;
			case 'ins': {
				if ($i > 0) {
					if ($ins[$i] == $ins[$i-1]) {
						echo "&nbsp;";
						$j++;
					} else {
						echo "".$j.".";
						$j++;
					}
				} else {
					echo "".$j.".";
					$j++;
				}
			}
			break;
			case 'goals': {
				if ($i > 0) {
					if ($goals[$i] == $goals[$i-1]) {
						echo "&nbsp;";
						$j++;
					} else {
						echo "".$j.".";
						$j++;
					}
				} else {
					echo "".$j.".";
					$j++;
				}
			}
			break;
			case 'assists': {
				if ($i > 0) {
					if ($assists[$i] == $assists[$i-1]) {
						echo "&nbsp;";
						$j++;
					} else {
						echo "".$j.".";
						$j++;
					}
				} else {
					echo "".$j.".";
					$j++;
				}
			}
			break;
			case 'yellows': {
				if ($i > 0) {
					if ($yellows[$i] == $yellows[$i-1]) {
						echo "&nbsp;";
						$j++;
					} else {
						echo "".$j.".";
						$j++;
					}
				} else {
					echo "".$j.".";
					$j++;
				}
			}
			break;
			case 'reds': {
				if ($i > 0) {
					if ($reds[$i] == $reds[$i-1]) {
						echo "&nbsp;";
						$j++;
					} else {
						echo "".$j.".";
						$j++;
					}
				} else {
					echo "".$j.".";
					$j++;
				}
			}
			break;
			case 'number': {
				echo "#".$numbers[$i]."";
				$j++;
			}
			break;
			case 'name': {
				echo "".$j.".";
				$j++;
			}
			break;
		}
		echo "</td>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>";
		if ($publish[$i] == 1) {
			echo "<a href='player.php?id=".$players[$i]."'>".$names[$i]."</a>";
		} else {
			echo "".$names[$i]."";
		}
		echo "</td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
		if ($sort == 'minutes')
			echo "<b>";
			echo "".$minutes[$i]."";
		if ($sort == 'minutes')
			echo "</b>";
			echo "</td>\n";
			echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
		if ($sort == 'apps')
			echo "<b>";
			echo "".$apps[$i]."";
		if ($sort == 'apps')
			echo "</b>";
			echo "</td>\n";
			echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
		if ($sort == 'ins')
			echo "<b>";
			echo "".$ins[$i]."";
		if ($sort == 'ins')
			echo "</b>";
			echo "</td>\n";
			echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
		if ($sort == 'goals')
			echo "<b>";
			echo "".$goals[$i]."";
		if ($sort == 'goals')
			echo "</b>";
			echo "</td>\n";
			echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
		if ($sort == 'assists')
			echo "<b>";
			echo "".$assists[$i]."";
		if ($sort == 'assists')
			echo "</b>";
			echo "</td>\n";
			echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
		if ($sort == 'yellows')
			echo "<b>";
			echo "".$yellows[$i]."";
		if ($sort == 'yellows')
			echo "</b>";
			echo "</td>\n";
			echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
		if ($sort == 'reds')
			echo "<b>";
			echo "".$reds[$i]."";
		if ($sort == 'reds')
			echo "</b>";
			echo "</td>\n";
			echo "</tr>\n";
		$i++;
	}
	echo "<tr>\n";
	echo "<td align='center' valign='middle' colspan='9' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<a href='?sort=number'>".$locale_by_number."</a> |\n";
	echo "<a href='?sort=name'>".$locale_alphabetically."</a> |\n";
	echo "<a href='?sort=minutes'>".$locale_minutes."</a> |\n";
	echo "<a href='?sort=apps'>".$locale_in_opening."</a> |\n";
	echo "<a href='?sort=ins'>".$locale_substituted."</a> |\n";
	echo "<a href='?sort=goals'>".$locale_goals_long."</a> |\n";
	echo "<a href='?sort=assists'>".$locale_goal_assists_long."</a> |\n";
	echo "<a href='?sort=yellows'>".$locale_yellow_cards_long."</a> |\n";
	echo "<a href='?sort=reds'>".$locale_red_cards_long."</a>\n";
	echo "</td>\n";
	echo "</tr>\n";
} else {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' colspan='7'>".$locale_no_players_added."</td>\n";
	echo "</tr>\n";
}
echo "</table>\n";
if (SHOW_STAFF == 1) {
	echo "<br><br>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_managers."</b></td>\n";
	echo "<td align='center' valign='middle'><b>".$locale_wins_short."</b></td>\n";
	echo "<td align='center' valign='middle'><b>".$locale_draws_short."</b></td>\n";
	echo "<td align='center' valign='middle'><b>".$locale_loses_short."</b></td>\n";
	echo "<td align='center' valign='middle'><b>".$locale_win_streak_short."</b></td>\n";
	echo "<td align='center' valign='middle'><b>".$locale_undefeated_streak_short."</b></td>\n";
	echo "<td align='center' valign='middle'><b>".$locale_win_proc_short."</b></td>\n";
	echo "<td align='center' valign='middle'><b>".$locale_draw_proc_short."</b></td>\n";
	echo "<td align='center' valign='middle'><b>".$locale_lose_proc_short."</b></td>\n";
	echo "</tr>\n";

	if ($default_season_id == 0) {
		$tdefault_season_id = '%';
	} else {
		$tdefault_season_id = $default_season_id;
	}
	if ($default_match_type_id == 0) {
		$tdefault_match_type_id = '%';
	} else {
		$tdefault_match_type_id = $default_match_type_id;
	}
	$query = mysqli_query($db_connect, "SELECT
		M.ManagerID AS manager_id,
		M.ManagerLastName AS manager_last_name,
		M.ManagerFirstName AS manager_first_name
		FROM team_managers M, team_matches MA, team_seasons S
		WHERE MA.MatchSeasonID LIKE '$tdefault_season_id'
		AND MA.MatchTypeID LIKE '$tdefault_match_type_id'
		AND M.ManagerID = S.SeasonManagerID
		AND S.SeasonID = MA.MatchSeasonID
		GROUP BY manager_id
		ORDER BY manager_last_name, manager_first_name
	") or die(mysqli_error());
	$j = 1;
	while($data = mysqli_fetch_array($query)) {
		$id = $data['manager_id'];
		if ($j % 2 == 0) {
			$bg_color = '#'.BGCOLOR1;
		} else {
			$bg_color = '#'.BGCOLOR2;
		}
		$wins = 0;
		$draws = 0;
		$loses = 0;
		$streak = 0;
		$streak2 = 0;
		$record = 0;
		$record2 = 0;
		$get_timeline = mysqli_query($db_connect, "SELECT
			T.StartDate AS start_date,
			T.EndDate AS end_date
			FROM team_managers_time T, team_managers M
			WHERE T.ManagerID = M.ManagerID
			AND M.ManagerID = '$id'
			ORDER BY start_date
		") or die(mysqli_error());
		$y = mysqli_num_rows($get_timeline);
		if ($y > 0) {
			$temp_to_query = ' AND (';
			$x = 1;
			while($macth_date = mysqli_fetch_array($get_timeline)) {
				$temp_to_query .= "(M.MatchDateTime <= '".$macth_date['end_date']." 00:00:00' AND M.MatchDateTime >= '".$macth_date['start_date']." 00:00:00')";
				if ($x != $y) {
					$temp_to_query .= ' OR ';
				}
				$x++;
			}
		}
		mysqli_free_result($get_timeline);

		$temp_to_query = " ";
		$get_matches = mysqli_query($db_connect, "SELECT
			M.MatchGoals AS goals,
			M.MatchGoalsOpponent AS goals_o
			FROM team_matches M
			WHERE M.MatchSeasonID LIKE '$tdefault_season_id'
			AND M.MatchTypeID LIKE '$tdefault_match_type_id' $temp_to_query
			AND M.MatchGoals IS NOT NULL
			AND M.MatchGoalsOpponent IS NOT NULL
			ORDER BY M.MatchDateTime
		") or die(mysqli_error());
		$k = 0;
		while($data_m = mysqli_fetch_array($get_matches)) {
			if ($data_m['goals'] > $data_m['goals_o']) {
				$wins = $wins + 1;
				$streak++;
				$streak2++;
				$t = $k + 1;
				$track = 1;
				$track2 = 1;
			}
			if ($data_m['goals'] == $data_m['goals_o']) {
				$draws = $draws + 1;
				$streak=0;
				$track = 0;
				$track2 = 1;
				$streak2++;
			}
			if ($data_m['goals'] < $data_m['goals_o']) {
				$loses = $loses + 1;
				$streak=0;
				$streak2=0;
				$track = 0;
				$track2 = 0;
			}
			if ($k > 0) {
				if ($track == 1) {
					if ($streak >= $record) {
						$record = $streak;
					}
				}
				if ($track2 == 1) {
					if ($streak2 >= $record2) {
						$record2 = $streak2;
					}
				}
			}
			$k = $k +1;
		}
		mysqli_free_result($get_matches);

		$all = $wins + $loses + $draws;
		if ($all == 0) {
			$win_pros = round(0, 2);
			$draw_pros = round(0, 2);
			$lose_pros = round(0, 2);
		} else {
			$win_pros = round(100*($wins/$all), 2);
			$draw_pros = round(100*($draws/$all), 2);
			$lose_pros = round(100*($loses/$all), 2);
		}
		echo "<tr align='left' bgcolor='".$bg_color."'>\n";
		echo "<td align='left' valign='middle'>";
		if ($data['manager_last_name'] == '') {
			echo "<a href='manager.php?id=".$data['manager_id']."'>".$data['manager_first_name']."</a>";
		} else {
			echo"<a href='manager.php?id=".$data['manager_id']."'>".$data['manager_last_name'].", ".$data['manager_first_name']."</a>";
		}
		echo "</td>\n";
		echo "<td align='center' valign='middle'>".$wins."</td>\n";
		echo "<td align='center' valign='middle'>".$draws."</td>\n";
		echo "<td align='center' valign='middle'>".$loses."</td>\n";
		echo "<td align='center' valign='middle'>".$record."</td>\n";
		echo "<td align='center' valign='middle'>".$record2."</td>\n";
		echo "<td align='center' valign='middle'>".$win_pros." %</td>\n";
		echo "<td align='center' valign='middle'>".$draw_pros." %</td>\n";
		echo "<td align='center' valign='middle'>".$lose_pros." %</td>\n";
		echo "</tr>\n";
		$j++;
	}
	if ($j == 1) {
		echo "<tr>\n";
		echo "<td align='left' valign='middle'>".$locale_no_staff_added."</td>\n";
		echo "</tr>\n";
	}
	mysqli_free_result($query);
	
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