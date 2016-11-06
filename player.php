<?php
include('top.php');
$script_name = "player.php?".$_SERVER['QUERY_STRING'];

if (isset($_REQUEST['sort'])){ $sort = $_REQUEST['sort']; }

if (!isset($sort)) {
	$sort = 'season_name';
}
if (isset($_REQUEST['season_id_page']) || (isset($_REQUEST['season_name_page']))) {
	$season_id_page = $_REQUEST['season_id_page'];
	$season_name_page = $_REQUEST['season_name_page'];
}
$id = $_REQUEST['id'];

if ($id == '' || !is_numeric($id)) {
	$id = 1;
}
$get_player_info = mysqli_query($db_connect, "SELECT
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	PlayerID AS player_id,
	PlayerNumber AS player_nember,
	PlayerDescription AS player_description,
	DATE_FORMAT(PlayerDOB, '$how_to_print_in_player') AS player_dob,
	PlayerPOB AS player_pob,
	PlayerHeight AS player_height,
	PlayerWeight AS player_weight,
	PlayerPC AS player_pc,
	PlayerShowStats AS show_stats,
	PlayerPositionID AS player_position
	FROM (team_players AS P)
	WHERE P.PlayerID = '$id'
	LIMIT 1
") or die(mysqli_error());
$player_data = mysqli_fetch_array($get_player_info);
mysqli_free_result($get_player_info);

echo "<form method='post' action='change.php'>\n";
echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<td><b>".$locale_player_profile.": ".$player_data['player_name']."";

if ($player_data['player_position'] != 5) {
	echo ", #".$player_data['player_nember']."";
}
echo "</b>";
switch ($player_data['player_position']) {
	case 1: {
		echo"<br><i>".$locale_goalkeeper."</i>";
	}
	break;
	case 2: {
		echo"<br><i>".$locale_defender."</i>";
	}
	break;
	case 3: {
		echo"<br><i>".$locale_midfield."</i>";
	}
	break;
	case 4: {
		echo"<br><i>".$locale_forward."</i>";
	}
	break;
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='top'>";
$image_url = "images/".$player_data['player_id'].".jpg";
$image_url2 = "images/".$player_data['player_id'].".png";
$image_url3 = "images/".$player_data['player_id']."_1.jpg";
$image_url4 = "images/".$player_data['player_id']."_1.png";
$image_url5 = "images/".$player_data['player_id']."_2.jpg";
$image_url6 = "images/".$player_data['player_id']."_2.png";

if (isset($_REQUEST['show_all'])) {
	$show_all = $_REQUEST['show_all'];
}
if (!isset($show_all)) {
	if (strlen($player_data['player_description']) > 2000) {
		$player_data['player_description'] = substr($player_data['player_description'], 0, 2000);
		$player_data['player_description'] .= "...[<a href='player.php?id=".$id."&amp;show_all=1'>".$locale_read_more."</a>]";
	}
}
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' valign='top' width='20%'>";

if (file_exists($image_url) || file_exists($image_url2)) {
	if (file_exists($image_url))
		echo "<img src='".$image_url."'>";
	if (file_exists($image_url2))
		echo "<img src='".$image_url2."'>";
	if (file_exists($image_url3))
		echo "<br><br><img src='".$image_url3."'>";
	if (file_exists($image_url4))
		echo "<br><br><img src='".$image_url4."'>";
	if (file_exists($image_url5))
		echo "<br><br><img src='".$image_url5."'>";
	if (file_exists($image_url6))
		echo "<br><br><img src='".$image_url6."'>";
} else {
	echo "<img src='images/no_image.png'>";
}
echo "</td>\n";
echo "<td align='left' valign='top' width='50%'>".$player_data['player_description']."</td>\n";
echo "<td align='left' valign='top' width='30%'>\n";
echo "<p><b>".$locale_pob."</b><br>".$player_data['player_pob']."</p>\n";
echo "<p><b>".$locale_dob."</b><br>".$player_data['player_dob']."</p>\n";
echo "<p><b>".$locale_height."</b><br>".$player_data['player_height']."</p>\n";
echo "<p><b>".$locale_weight."</b><br>".$player_data['player_weight']."</p>\n";

if ($player_data['player_pc'] == 1) {
	echo "<p><b>".$locale_previous_clubs."</b><br>".$player_data['player_pc']."</p>\n";
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<td colspan='4'><b>".$locale_transfers."</b></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_year."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_from."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_to."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_transfer_value."</b></td>\n";
echo "</tr>\n";
$get_transfers = mysqli_query($db_connect, "SELECT
	S.SeasonName AS season_name,
	O.OpponentName AS opponent_name,
	O.OpponentID AS opponent_id,
	T.TransferValue AS transfer_value,
	T.TransferStatus AS transfer_status
	FROM (team_season_names AS S, team_opponents AS O, team_transfers AS T)
	WHERE O.OpponentID = T.TransferOpponentID
	AND T.TransferSeasonID = S.SeasonID
	AND T.TransferPlayerID = '$id'
") or die(mysqli_error());
$j = 1;
while($data = mysqli_fetch_array($get_transfers)) {
	if ($j % 2 == 0) {
		$bg_color = '#'.BGCOLOR1;
	} else {
		$bg_color = '#'.BGCOLOR2;
	}
	if ($data['transfer_status'] == 0) {
		$team_name_home = "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."";
		$team_name_away = TEAM_NAME;
	} else {
		$team_name_away = "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."";
		$team_name_home = TEAM_NAME;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['season_name']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$team_name_home."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$team_name_away."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$data['transfer_value']."</td>\n";
	echo "</tr>\n";
	$j++;
}
if ($j == 1) {
	echo "<tr align='left'>\n";
	echo "<td colspan='4'>".$locale_no_transfers."</td>\n";
	echo "</tr>";
}
mysqli_free_result($get_transfers);

echo "</table>\n";

if (!isset($season_id_page)) {
	echo "<hr width='100%'>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top'>";

	if ($player_data['show_stats'] == 1 && $player_data['player_position'] != 5) {
		echo "".$locale_match_type_filter.": \n";
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
	}
	$sql = "SELECT
		P.PlayerID AS player_id,
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerPublish AS publish
		FROM (team_seasons AS S)
		LEFT OUTER JOIN team_players AS P ON P.PlayerID = S.SeasonPlayerID
	";
	if ($default_season_id == 0) {
		$sql .= "WHERE P.PlayerID != ''
			AND P.PlayerPublish = '1'
			GROUP BY player_id
			ORDER BY player_name
		";
	} else {
		$sql .= "AND S.SeasonID = '$default_season_id'
			WHERE P.PlayerID != ''
			AND P.PlayerPublish = '1'
			GROUP BY player_id
			ORDER BY player_name
		";
	}
	$query = mysqli_query($db_connect, "".$sql."") or die(mysqli_error());
	echo "<br>".$locale_change_player.": \n";
	echo "<select name='player_id'>\n";
	while($data = mysqli_fetch_array($query)) {
		if ($data['player_id'] == $id) {
			echo "<option value='".$data['player_id']."' SELECTED>".$data['player_name']."</option>\n";
		} else {
			echo "<option value='".$data['player_id']."'>".$data['player_name']."</option>\n";
		}
	}
	echo "</select>\n";
	echo "<input type='submit' name='change_player' value='".$locale_change."'>\n";
	mysqli_free_result($query);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	if ($player_data['show_stats'] == 1 && $player_data['player_position'] != 5) {
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?id=".$id."&amp;sort=season_name'>".$locale_season_short."</a></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?id=".$id."&amp;sort=minutes'>".$locale_minutes_short."</a></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?id=".$id."&amp;sort=apps'>".$locale_openings_short."</a></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?id=".$id."&amp;sort=ins'>".$locale_to_the_field_short."</a></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?id=".$id."&amp;sort=goals'>".$locale_goals_short."</a></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?id=".$id."&amp;sort=assists'>".$locale_goal_assists_short."</a></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?id=".$id."&amp;sort=yellows'>".$locale_yellow_cards_short."</a></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><a href='?id=".$id."&amp;sort=reds'>".$locale_red_cards_short."</a></td>\n";
		echo "</tr>\n";

		if ($default_match_type_id != 0) {
			$get_seasons = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( A.AppearancePlayerID ) AS apps
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_matches AS M ON M.MatchSeasonID = S.SeasonID
				AND M.MatchTypeID = '$default_match_type_id'
				LEFT OUTER JOIN team_appearances AS A ON A.AppearancePlayerID = '$id'
				AND A.AppearanceSeasonID = S.SeasonID
				AND A.AppearanceMatchID = M.MatchID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_goals = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( G.GoalPlayerID ) AS goals
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_matches AS M ON M.MatchSeasonID = S.SeasonID
				AND M.MatchTypeID = '$default_match_type_id'
				LEFT OUTER JOIN team_goals AS G ON G.GoalPlayerID = '$id'
				AND GoalOwn = '0'
				AND G.GoalSeasonID = S.SeasonID
				AND G.GoalMatchID = M.MatchID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_ins = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( SU.SubstitutionPlayerIDIn ) AS ins
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_matches AS M ON M.MatchSeasonID = S.SeasonID
				AND M.MatchTypeID = '$default_match_type_id'
				LEFT OUTER JOIN team_substitutions AS SU ON SU.SubstitutionPlayerIDIn = '$id'
				AND SU.SubstitutionSeasonID = S.SeasonID
				AND SU.SubstitutionMatchID = M.MatchID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_assists = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( GA.GoalAssistPlayerID ) AS assists
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_matches AS M ON M.MatchSeasonID = S.SeasonID
				AND M.MatchTypeID = '$default_match_type_id'
				LEFT OUTER JOIN team_goal_assists AS GA ON GA.GoalAssistPlayerID = '$id'
				AND GA.GoalAssistSeasonID = S.SeasonID
				AND GA.GoalAssistMatchID = M.MatchID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_yellows = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( YC.YellowCardPlayerID ) AS yellows
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_matches AS M ON M.MatchSeasonID = S.SeasonID
				AND M.MatchTypeID = '$default_match_type_id'
				LEFT OUTER JOIN team_yellow_cards AS YC ON YC.YellowCardPlayerID = '$id'
				AND YC.YellowCardSeasonID = S.SeasonID
				AND YC.YellowCardMatchID = M.MatchID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_reds = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( RC.RedCardPlayerID ) AS reds
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_matches AS M ON M.MatchSeasonID = S.SeasonID
				AND M.MatchTypeID = '$default_match_type_id'
				LEFT OUTER JOIN team_red_cards AS RC ON RC.RedCardPlayerID = '$id'
				AND RC.RedCardSeasonID = S.SeasonID
				AND RC.RedCardMatchID = M.MatchID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
		} else {
			$get_seasons = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( A.AppearancePlayerID ) AS apps
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_appearances AS A ON A.AppearancePlayerID = '$id'
				AND A.AppearanceSeasonID = S.SeasonID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_goals = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( G.GoalPlayerID ) AS goals
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_goals AS G ON G.GoalPlayerID = '$id'
				AND GoalOwn = '0'
				AND G.GoalSeasonID = S.SeasonID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_ins = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( SU.SubstitutionPlayerIDIn ) AS ins
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_substitutions AS SU ON SU.SubstitutionPlayerIDIn = '$id'
				AND SU.SubstitutionSeasonID = S.SeasonID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_assists = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( GA.GoalAssistPlayerID ) AS assists
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_goal_assists AS GA ON GA.GoalAssistPlayerID = '$id'
				AND GA.GoalAssistSeasonID = S.SeasonID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_yellows = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( YC.YellowCardPlayerID ) AS yellows
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_yellow_cards AS YC ON YC.YellowCardPlayerID = '$id'
				AND YC.YellowCardSeasonID = S.SeasonID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
			$get_reds = mysqli_query($db_connect, "SELECT
				SN.SeasonName AS season_name,
				S.SeasonID AS season_id,
				COUNT( RC.RedCardPlayerID ) AS reds
				FROM (team_seasons AS S, team_season_names AS SN)
				LEFT OUTER JOIN team_red_cards AS RC ON RC.RedCardPlayerID = '$id'
				AND RC.RedCardSeasonID = S.SeasonID
				WHERE SN.SeasonID = S.SeasonID
				AND S.SeasonPlayerID = '$id'
				GROUP BY season_id
				ORDER BY season_name
			") or die(mysqli_error());
		}
		$i = 0;
		while($data = mysqli_fetch_array($get_seasons)) {
			$season_name[$i] = $data['season_name'];
			$season_id[$i] = $data['season_id'];
			$apps[$i] = $data['apps'];
			$minutes[$i] = 0;

			if ($default_match_type_id == 0) {
				$tdefault_match_type_id = '%';
			} else {
				$tdefault_match_type_id = $default_match_type_id;
			}
			$query = mysqli_query($db_connect, "SELECT
				S.SubstitutionMinute AS substitution_minute,
				M.MatchOvertime AS match_overtime
				FROM team_substitutions AS S, team_matches AS M
				WHERE S.SubstitutionPlayerIDIn = '$id'
				AND S.SubstitutionSeasonID LIKE '$season_id[$i]'
				AND M.MatchID = S.SubstitutionMatchID
				AND M.MatchTypeID LIKE '$tdefault_match_type_id'
			") or die(mysqli_error());
			while($tdata = mysqli_fetch_array($query)) {
				if ($tdata['match_overtime'] == 0) {
					$match_minutes = 90;
				} else {
					$match_minutes = 120;
				}
				$minutes[$i] = $minutes[$i] + ($match_minutes-$tdata['substitution_minute']);
			}
			mysqli_free_result($query);

			$query = mysqli_query($db_connect, "SELECT *
				FROM team_appearances AS A, team_matches AS M
				WHERE A.AppearancePlayerID = '$id'
				AND A.AppearanceSeasonID LIKE '$season_id[$i]'
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
				S.SubstitutionMinute AS substitution_minute,
				M.MatchOvertime AS match_overtime
				FROM team_substitutions AS S, team_matches AS M
				WHERE S.SubstitutionPlayerIDOut = '$id'
				AND S.SubstitutionSeasonID LIKE '$season_id[$i]'
				AND M.MatchID = S.SubstitutionMatchID
				AND M.MatchTypeID LIKE '$tdefault_match_type_id'
			") or die(mysqli_error());
			while($tdata = mysqli_fetch_array($query)) {
				if ($tdata['match_overtime'] == 0) {
					$match_minutes = 90;
				} else {
					$match_minutes = 120;
				}
				$minutes[$i] = $minutes[$i] - ($match_minutes-$tdata['substitution_minute']);
			}
			mysqli_free_result($query);

			$query = mysqli_query($db_connect, "SELECT
				RC.RedCardMinute AS red_card_minute,
				M.MatchOvertime AS match_overtime
				FROM team_red_cards AS RC, team_matches AS M
				WHERE RC.RedCardPlayerID = '$id'
				AND RC.RedCardSeasonID LIKE '$season_id[$i]'
				AND M.MatchID = RC.RedCardMatchID
				AND M.MatchTypeID LIKE '$tdefault_match_type_id'
			") or die(mysqli_error());
			while($tdata = mysqli_fetch_array($query)) {
				if ($tdata['match_overtime'] == 0) {
					$match_minutes = 90;
				} else {
					$match_minutes = 120;
				}
				$minutes[$i] = $minutes[$i] - ($match_minutes-$tdata['red_card_minute']);
			}
			mysqli_free_result($query);
			$i++;
		}
		$i = 0;
		while($data = mysqli_fetch_array($get_goals)) {
			$goals[$i] = $data['goals'];
			$i++;
		}
		$i = 0;
		while($data = mysqli_fetch_array($get_ins)) {
			$ins[$i] = $data['ins'];
			$i++;
		}
		$i = 0;
		while($data = mysqli_fetch_array($get_assists)) {
			$assists[$i] = $data['assists'];
			$i++;
		}
		$i = 0;
		while($data = mysqli_fetch_array($get_yellows)) {
			$yellows[$i] = $data['yellows'];
			$i++;
		}
		$i = 0;
		while($data = mysqli_fetch_array($get_reds)) {
			$reds[$i] = $data['reds'];
			$i++;
		}
		$get_total = mysqli_num_rows($get_seasons);
		mysqli_free_result($get_seasons);
		mysqli_free_result($get_goals);
		mysqli_free_result($get_ins);
		mysqli_free_result($get_assists);
		mysqli_free_result($get_yellows);
		mysqli_free_result($get_reds);

		switch ($sort) {
			case 'season_name':
			array_multisort($season_name, SORT_ASC, SORT_STRING, $goals, $apps, $assists, $yellows, $reds, $ins, $minutes, $season_id);
			break;
			case 'minutes':
			array_multisort($minutes, SORT_DESC, SORT_NUMERIC, $apps, SORT_DESC, SORT_NUMERIC, $season_name, SORT_ASC, SORT_STRING, $goals, $assists, $yellows, $reds, $ins, $season_id);
			break;
			case 'apps':
			array_multisort($apps, SORT_DESC, SORT_NUMERIC, $season_name, SORT_ASC, SORT_STRING, $goals, $assists, $yellows, $reds, $ins, $minutes, $season_id);
			break;
			case 'ins':
			array_multisort($ins, SORT_DESC, SORT_NUMERIC, $season_name, SORT_ASC, SORT_STRING, $apps, $goals, $assists, $yellows, $reds, $minutes, $season_id);
			break;
			case 'goals':
			array_multisort($goals, SORT_DESC, SORT_NUMERIC, $season_name, SORT_ASC, SORT_STRING, $apps, $assists, $yellows, $reds, $ins, $minutes, $season_id);
			break;
			case 'assists':
			array_multisort($assists, SORT_DESC, SORT_NUMERIC, $season_name, SORT_ASC, SORT_STRING, $goals, $apps, $yellows, $reds, $ins, $minutes, $season_id);
			break;
			case 'yellows':
			array_multisort($yellows, SORT_DESC, SORT_NUMERIC, $season_name, SORT_ASC, SORT_STRING, $goals, $apps, $assists, $reds, $ins, $minutes, $season_id);
			break;
			case 'reds':
			array_multisort($reds, SORT_DESC, SORT_NUMERIC, $season_name, SORT_ASC, SORT_STRING, $goals, $apps, $assists, $yellows, $ins, $minutes, $season_id);
			break;
		}
		$total_apps = 0;
		$total_minutes = 0;
		$total_ins = 0;
		$total_goals = 0;
		$total_assists = 0;
		$total_yellows = 0;
		$total_reds = 0;
		$i = 0;
		$j = 1;
		while($i < $get_total) {
			if ($j % 2 == 0) {
				$bg_color = '#'.BGCOLOR1;
			} else {
				$bg_color = '#'.BGCOLOR2;
			}
			echo "<tr>\n";
			echo "<td align='left' valign='middle' bgcolor='".$bg_color."'><a href='player.php?id=".$id."&season_id_page=".$season_id[$i]."&season_name_page=".$season_name[$i]."'>".$season_name[$i]."</a></td>\n";
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
			$total_apps = $total_apps + $apps[$i];
			$total_minutes = $total_minutes + $minutes[$i];
			$total_goals = $total_goals + $goals[$i];
			$total_ins = $total_ins + $ins[$i];
			$total_assists = $total_assists + $assists[$i];
			$total_yellows = $total_yellows + $yellows[$i];
			$total_reds = $total_reds + $reds[$i];
			$i++;
			$j++;
		}
		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_total."</b></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_minutes."</b></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_apps."</b></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_ins."</b></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_goals."</b></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_assists."</b></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_yellows."</b></td>\n";
		echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$total_reds."</b></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td align='center' valign='middle' colspan='8' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
		echo "<a href='?sort=season_name&amp;id=".$id."'>".$locale_by_season."</a> | \n";
		echo "<a href='?sort=minutes&amp;id=".$id."'>".$locale_minutes."</a> | \n";
		echo "<a href='?sort=apps&amp;id=".$id."'>".$locale_in_opening."</a> | \n";
		echo "<a href='?sort=ins&amp;id=".$id."'>".$locale_substituted."</a> | \n";
		echo "<a href='?sort=goals&amp;id=".$id."'>".$locale_goals_long."</a> | \n";
		echo "<a href='?sort=assists&amp;id=".$id."'>".$locale_goal_assists_long."</a> | \n";
		echo "<a href='?sort=yellows&amp;id=".$id."'>".$locale_yellow_cards_long."</a> | \n";
		echo "<a href='?sort=reds&amp;id=".$id."'>".$locale_red_cards_long."</a>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<br><br>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_latest_appearances."</b></td>\n";
		echo "</tr>\n";
		$get_appearances = mysqli_query($db_connect, "SELECT
			M.MatchID AS match_id,
			M.MatchGoals AS goals,
			M.MatchGoalsOpponent AS goals_opponent,
			M.MatchPublish AS publish,
			DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date,
			MT.MatchTypeName AS match_type_name,
			M.MatchAdditionalType AS match_additional_type,
			O.OpponentName AS opponent_name,
			O.OpponentID AS opponent_id,
			M.MatchPlaceID AS match_place_id,
			M.MatchNeutral AS neutral
			FROM team_matches AS M, team_match_types AS MT, team_appearances AS A, team_opponents AS O
			WHERE M.MatchTypeID = MT.MatchTypeID
			AND M.MatchID = A.AppearanceMatchID
			AND A.AppearancePlayerID = '$id'
			AND M.MatchOpponent = O.OpponentID
			ORDER BY match_date DESC
			LIMIT 10
		") or die(mysqli_error());

		if (mysqli_num_rows($get_appearances) == 0) {
			$bg_color = BGCOLOR2;
			echo "<td align='left' valign='middle' bgcolor='".$bg_color."' colspan='5'>".$locale_none."</td>\n";
		} else {
			$j = 1;
			while($data = mysqli_fetch_array($get_appearances)) {
				if ($j % 2 == 0) {
					$bg_color = '#'.BGCOLOR1;
				} else {
					$bg_color = '#'.BGCOLOR2;
				}
				echo "<tr>\n";
				echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_date']."</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if($data['match_additional_type'] == '') {
					echo "".$data['match_type_name']."";
				} else {
					echo "".$data['match_type_name']." / ".$data['match_additional_type']."";
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
				if ($data['neutral'] == 1) {
					echo "".$locale_neutral_short."";
				} else {
					if ($data['match_place_id'] == 1) {
						echo "".$locale_home_short."";
					} else if ($data['match_place_id'] == 2) {
						echo "".$locale_away_short."";
					}
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a></td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
					echo "&nbsp;";
				} else {
					if ($data['publish'] == 1) {
						echo "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
					} else {
						echo "".$data['goals']." - ".$data['goals_opponent']."";
					}
				}
				echo "</td>\n";
				echo "</tr>\n";
				$j++;
			}
		}
		mysqli_free_result($get_appearances);

		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_latest_goals."</b></td>\n";
		echo "</tr>\n";
		$get_goals = mysqli_query($db_connect, "SELECT
			DISTINCT M.MatchID AS match_id,
			M.MatchGoals AS goals,
			M.MatchPublish AS publish,
			M.MatchGoalsOpponent AS goals_opponent,
			DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date,
			MT.MatchTypeName AS match_type_name,
			M.MatchAdditionalType AS match_additional_type,
			O.OpponentName AS opponent_name,
			O.OpponentID AS opponent_id,
			M.MatchPlaceID AS match_place_id,
			M.MatchNeutral AS neutral
			FROM team_matches AS M, team_match_types AS MT, team_goals AS G, team_opponents AS O
			WHERE M.MatchTypeID = MT.MatchTypeID
			AND M.MatchID = G.GoalMatchID
			AND G.GoalPlayerID = '$id'
			AND G.GoalOwn = '0'
			AND M.MatchOpponent = O.OpponentID
			ORDER BY match_date DESC
			LIMIT 10
		") or die(mysqli_error());
		if (mysqli_num_rows($get_goals) == 0) {
			$bg_color = '#'.BGCOLOR2;
			echo "<td align='left' valign='middle' bgcolor='".$bg_color."' colspan='5'>".$locale_none."</td>\n";
		} else {
			$j = 1;
			while($data = mysqli_fetch_array($get_goals)) {
				if ($j % 2 == 0) {
					$bg_color = '#'.BGCOLOR1;
				} else {
					$bg_color = '#'.BGCOLOR2;
				}
				echo "<tr>\n";
				echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_date']."</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['match_additional_type'] == '') {
					echo "".$data['match_type_name']."";
				} else {
					echo "".$data['match_type_name']." / ".$data['match_additional_type']."";
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['neutral'] == 1) {
					echo "".$locale_neutral_short."";
				} else {
					if ($data['match_place_id'] == 1) {
						echo "".$locale_home_short."";
					} else if ($data['match_place_id'] == 2) {
						echo "".$locale_away_short."";
					}
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a></td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
					echo "&nbsp;";
				} else {
					if ($data['publish'] == 1) {
						echo "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
					} else {
						echo "".$data['goals']." - ".$data['goals_opponent']."";
					}
				}
				echo "</td>\n";
				echo "</tr>\n";
				$j++;
			}
		}
		mysqli_free_result($get_goals);

		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_latest_goal_assists."</b></td>\n";
		echo "</tr>\n";
		$get_goal_assists = mysqli_query($db_connect, "SELECT
			DISTINCT M.MatchID AS match_id,
			M.MatchGoals AS goals,
			M.MatchPublish AS publish,
			M.MatchGoalsOpponent AS goals_opponent,
			DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date,
			MT.MatchTypeName AS match_type_name,
			M.MatchAdditionalType AS match_additional_type,
			O.OpponentName AS opponent_name,
			O.OpponentID AS opponent_id,
			M.MatchPlaceID AS match_place_id,
			M.MatchNeutral AS neutral
			FROM team_matches AS M, team_match_types AS MT, team_goal_assists AS GA, team_opponents AS O
			WHERE M.MatchTypeID = MT.MatchTypeID
			AND M.MatchID = GA.GoalAssistMatchID
			AND GA.GoalAssistPlayerID = '$id'
			AND M.MatchOpponent = O.OpponentID
			ORDER BY match_date DESC
			LIMIT 10
		") or die(mysqli_error());

		if (mysqli_num_rows($get_goal_assists) == 0) {
			$bg_color = '#'.BGCOLOR2;
			echo "<td align='left' valign='middle' bgcolor='".$bg_color."' colspan='5'>".$locale_none."</td>\n";
		} else {
			$j = 1;
			while($data = mysqli_fetch_array($get_goal_assists)) {
				if ($j % 2 == 0) {
					$bg_color = '#'.BGCOLOR1;
				} else {
					$bg_color = '#'.BGCOLOR2;
				}
				echo "<tr>\n";
				echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_date']."</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['match_additional_type'] == '') {
					echo "".$data['match_type_name']."";
				} else {
					echo "".$data['match_type_name']." / ".$data['match_additional_type']."";
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['neutral'] == 1) {
					echo "".$locale_neutral_short."";
				} else {
					if ($data['match_place_id'] == 1) {
						echo "".$locale_home_short."";
					} else if ($data['match_place_id'] == 2) {
						echo "".$locale_away_short."";
					}
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a></td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
					echo "&nbsp;";
				} else {
					if ($data['publish'] == 1) {
						echo "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
					} else {
						echo "".$data['goals']." - ".$data['goals_opponent']."";
					}
				}
				echo "</td>\n";
				echo "</tr>\n";
				$j++;
			}
		}
		mysqli_free_result($get_goal_assists);

		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_latest_yellow_cards."</b></td>\n";
		echo "</tr>\n";
		$get_yellow_cards = mysqli_query($db_connect, "SELECT
			DISTINCT M.MatchID AS match_id,
			M.MatchGoals AS goals,
			M.MatchPublish AS publish,
			M.MatchGoalsOpponent AS goals_opponent,
			DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date,
			MT.MatchTypeName AS match_type_name,
			M.MatchAdditionalType AS match_additional_type,
			O.OpponentName AS opponent_name,
			O.OpponentID AS opponent_id,
			M.MatchPlaceID AS match_place_id,
			M.MatchNeutral AS neutral
			FROM team_matches AS M, team_match_types AS MT, team_yellow_cards AS YC, team_opponents AS O
			WHERE M.MatchTypeID = MT.MatchTypeID
			AND M.MatchID = YC.YellowCardMatchID
			AND YC.YellowCardPlayerID = '$id'
			AND M.MatchOpponent = O.OpponentID
			ORDER BY match_date DESC
			LIMIT 10
		") or die(mysqli_error());

		if (mysqli_num_rows($get_yellow_cards) == 0) {
			$bg_color = '#'.BGCOLOR2;
			echo "<td align='left' valign='middle' bgcolor='".$bg_color."' colspan='5'>".$locale_none."</td>\n";
		} else {
			$j = 1;
			while($data = mysqli_fetch_array($get_yellow_cards)) {
				if ($j % 2 == 0) {
					$bg_color = '#'.BGCOLOR1;
				} else {
					$bg_color = '#'.BGCOLOR2;
				}
				echo "<tr>\n";
				echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_date']."</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['match_additional_type'] == '') {
					echo "".$data['match_type_name']."";
				} else {
					echo "".$data['match_type_name']." / ".$data['match_additional_type']."";
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['neutral'] == 1) {
					echo "".$locale_neutral_short."";
				} else {
					if ($data['match_place_id'] == 1) {
						echo "".$locale_home_short."";
					} else if ($data['match_place_id'] == 2) {
						echo "".$locale_away_short."";
					}
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a></td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
					echo "&nbsp;";
				} else {
					if ($data['publish'] == 1) {
						echo "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
					} else {
						echo "".$data['goals']." - ".$data['goals_opponent']."";
					}
				}
				echo "</td>\n";
				echo "</tr>\n";
				$j++;
			}
		}
		mysqli_free_result($get_yellow_cards);

		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_latest_red_cards."</b></td>\n";
		echo "</tr>\n";
		$get_red_cards = mysqli_query($db_connect, "SELECT
			DISTINCT M.MatchID AS match_id,
			M.MatchGoals AS goals,
			M.MatchPublish AS publish,
			M.MatchGoalsOpponent AS goals_opponent,
			DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date,
			MT.MatchTypeName AS match_type_name,
			M.MatchAdditionalType AS match_additional_type,
			O.OpponentName AS opponent_name,
			O.OpponentID AS opponent_id,
			M.MatchPlaceID AS match_place_id,
			M.MatchNeutral AS neutral
			FROM team_matches AS M, team_match_types AS MT, team_red_cards AS RC, team_opponents AS O
			WHERE M.MatchTypeID = MT.MatchTypeID
			AND M.MatchID = RC.RedCardMatchID
			AND RC.RedCardPlayerID = '$id'
			AND M.MatchOpponent = O.OpponentID
			ORDER BY match_date DESC
			LIMIT 10
		") or die(mysqli_error());

		if (mysqli_num_rows($get_red_cards) == 0) {
			$bg_color = '#'.BGCOLOR2;
			echo "<td align='left' valign='middle' bgcolor='".$bg_color."' colspan='5'>".$locale_none."</td>\n";
		} else {
			$j = 1;
			while($data = mysqli_fetch_array($get_red_cards)) {
				if ($j % 2 == 0) {
					$bg_color = '#'.BGCOLOR1;
				} else {
					$bg_color = '#'.BGCOLOR2;
				}
				echo "<tr>\n";
				echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_date']."</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['match_additional_type'] == '') {
					echo "".$data['match_type_name']."";
				} else {
					echo "".$data['match_type_name']." / ".$data['match_additional_type']."";
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['neutral'] == 1) {
					echo "".$locale_neutral_short."";
				} else {
					if ($data['match_place_id'] == 1) {
						echo "".$locale_home_short."";
					} else if ($data['match_place_id'] == 2) {
						echo "".$locale_away_short."";
					}
				}
				echo "</td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a></td>\n";
				echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

				if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
					echo "&nbsp;";
				} else {
					if ($data['publish'] == 1) {
						echo "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
					} else {
						echo "".$data['goals']." - ".$data['goals_opponent']."";
					}
				}
				echo "</td>\n";
				echo "</tr>\n";
				$j++;
			}
		}
		mysqli_free_result($get_red_cards);
		
		echo "</table>\n";
	}
} else {
	$season_id_page = mysqli_real_escape_string($db_connect, $_REQUEST['season_id_page']);
	$season_name_page = mysqli_real_escape_string($db_connect, $_REQUEST['season_name_page']);

	if ($season_id_page == '' || !is_numeric($season_id_page)) {
		$season_id_page = 1;
	}
	if ($season_name_page == '') {
		exit();
	}
	$get_matches = mysqli_query($db_connect, "(SELECT
		DISTINCT M.MatchID AS match_id,
		M.MatchGoals AS goals,
		M.MatchGoalsOpponent AS goals_opponent,
		M.MatchPublish AS publish,
		DATE_FORMAT(M.MatchDateTime,'$how_to_print_in_report') AS match_date,
		MT.MatchTypeName AS match_type_name,
		M.MatchAdditionalType AS match_additional_type,
		O.OpponentName AS opponent_name,
		O.OpponentID AS opponent_id,
		M.MatchPlaceID AS match_place_id
		FROM (team_matches AS M, team_match_types AS MT, team_appearances AS A, team_opponents AS O, team_substitutions AS S)
		WHERE M.MatchTypeID = MT.MatchTypeID
		AND M.MatchSeasonID = '$season_id_page'
		AND (M.MatchID = A.AppearanceMatchID
		AND A.AppearancePlayerID = '$id')
		AND M.MatchOpponent = O.OpponentID)
	    UNION (SELECT DISTINCT M.MatchID AS match_id,
		M.MatchGoals AS goals,
		M.MatchGoalsOpponent AS goals_opponent,
		M.MatchPublish AS publish,
		DATE_FORMAT(M.MatchDateTime,'$how_to_print_in_report') AS match_date,
		MT.MatchTypeName AS match_type_name,
		M.MatchAdditionalType AS match_additional_type,
		O.OpponentName AS opponent_name,
		O.OpponentID AS opponent_id,
		M.MatchPlaceID AS match_place_id
		FROM (team_matches AS M, team_match_types AS MT, team_appearances AS A, team_opponents AS O, team_substitutions AS S)
		WHERE M.MatchTypeID = MT.MatchTypeID
		AND M.MatchSeasonID = '$season_id_page'
		AND (M.MatchID = S.SubstitutionMatchID
		AND SubstitutionPlayerIDIn = '$id')
		AND M.MatchOpponent = O.OpponentID)
		ORDER BY STR_TO_DATE(match_date, '%d.%m.%Y') ASC
	") or die(mysqli_error());
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td colspan='7'><b>".$locale_season_short.": ".$season_name_page." [<a href='player.php?id=".$id."'>".$locale_show_season_stats."</a>]</b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_date_and_time."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_match_type."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_opponent."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_final_score."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_goals."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_assists."</b></td>\n";
	echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_cards."</b></td>\n";
	echo "</tr>\n";
	$j = 0;
	while($data = mysqli_fetch_array($get_matches)) {
		if ($j % 2 == 0) {
			$bg_color = '#'.BGCOLOR1;
		} else {
			$bg_color = '#'.BGCOLOR2;
		}
		if ($data['match_additional_type'] == '') {
			$match_type = "".$data['match_type_name']."";
		} else {
			$match_type = "".$data['match_type_name']." / ".$data['match_additional_type']."";
		}
		if ($data['publish'] == 0) {
			$final_score = "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
		} else {
			$final_score = "".$data['goals']." - ".$data['goals_opponent']."";
		}
		$get_goals = mysqli_query($db_connect, "SELECT
			COUNT( G.GoalPlayerID ) AS goals
			FROM team_goals AS G
			WHERE G.GoalOwn = '0'
			AND G.GoalMatchID = '$data[match_id]'
			AND G.GoalPlayerID = '$id';
		") or die(mysqli_error());
		$goals = mysqli_fetch_array($get_goals);
		mysqli_free_result($get_goals);

		$get_assists = mysqli_query($db_connect, "SELECT
			COUNT( GA.GoalAssistPlayerID ) AS assists
			FROM team_goal_assists AS GA
			WHERE GA.GoalAssistMatchID = '$data[match_id]'
			AND GA.GoalAssistPlayerID = '$id';
		") or die(mysqli_error());
		$assists = mysqli_fetch_array($get_assists);
		mysqli_free_result($get_assists);

		$get_yellows = mysqli_query($db_connect, "SELECT
			COUNT( YC.YellowCardPlayerID ) AS yellows
			FROM team_yellow_cards AS YC
			WHERE YC.YellowCardMatchID = '$data[match_id]'
			AND YC.YellowCardPlayerID = '$id';
		") or die(mysqli_error());
		$yellows = mysqli_fetch_array($get_yellows);
		mysqli_free_result($get_yellows);

		$get_reds = mysqli_query($db_connect, "SELECT
			COUNT( RC.RedCardPlayerID ) AS reds
			FROM team_red_cards AS RC
			WHERE RC.RedCardMatchID = '$data[match_id]'
			AND RC.RedCardPlayerID = '$id';
		") or die(mysqli_error());
		$reds = mysqli_fetch_array($get_reds);
		mysqli_free_result($get_reds);
		
		if ($reds['reds'] > 0) {
			$cards = "<img src='images/reds.png'>";
		} else if ($yellows['yellows'] > 0 && $reds['reds'] == 0) {
			$cards = "<img src='images/yellows.png'>";
		} else {
			$cards = '';
		}
		$j++;
		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_date']."></td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$match_type."></td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a></td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$final_score."></td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$goals['goals']."></td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$assists['assists']."></td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>".$cards."></td>\n";
		echo "</tr>\n";
	}
	mysqli_free_result($get_matches);

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