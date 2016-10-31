<?php
include('top.php');
$script_name = "manager.php?".$_SERVER['QUERY_STRING'];

if (isset($_REQUEST['sort'])) {
	$sort = $_REQUEST['sort'];
}
if (!isset($sort)) {
	$sort = 'season_name';
}
$id = $_REQUEST['id'];

if ($id == '' || !is_numeric($id)) {
	$id = 1;
}
$get_manager = mysqli_query($db_connect, "SELECT
	CONCAT(M.ManagerFirstName, ' ', M.ManagerLastName) AS manager_name,
	M.ManagerID AS manager_id,
	M.ManagerProfile AS manager_description,
	M.ManagerPC AS manager_pc,
	DATE_FORMAT(M.ManagerDOB, '$how_to_print_in_manager') AS manager_dob,
	M.ManagerPOB AS manager_pob,
	M.ManagerPlayerID AS manager_player_id
	FROM team_managers AS M
	WHERE M.ManagerID = '$id'
	LIMIT 1
") or die(mysqli_error());
$manager_data = mysqli_fetch_array($get_manager);
mysqli_free_result($get_manager);

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
echo "<td><b>".$locale_player_profile.": ".$manager_data['manager_name']."</b></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='top'>\n";
$image_url = "images/manager".$id.".jpg";
$image_url2 = "images/manager".$id.".png";
$image_url3 = "images/manager".$id."_1.jpg";
$image_url4 = "images/manager".$id."_1.png";
$image_url5 = "images/manager".$id."_2.jpg";
$image_url6 = "images/manager".$id."_2.png";

if (isset($_REQUEST["show_all"])) {
	$show_all = $_REQUEST["show_all"];
}
if (!isset($show_all)) {
	if (strlen($manager_data['manager_description']) > 2000) {
		$manager_data['manager_description'] = substr($manager_data['manager_description'], 0, 2000);
		$manager_data['manager_description'] .= "...[<a href='manager.php?id=".$id."&amp;show_all=1'>".$locale_read_more."</a>]";
	}
}
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' valign='top' width='20%'>\n";

if (file_exists($image_url) || file_exists($image_url2)) {
	if (file_exists($image_url)) {
		echo "<img src='".$image_url."'>";
	}
	if (file_exists($image_url2)) {
		echo "<img src='".$image_url2."'>";
	}
	if (file_exists($image_url3)) {
		echo "<br><br><img src='".$image_url3."'>";
	}
	if (file_exists($image_url4)) {
		echo "<br><br><img src='".$image_url4."'>";
	}
	if (file_exists($image_url5)) {
		echo "<br><br><img src='".$image_url5."'>";
	}
	if (file_exists($image_url6)) {
		echo "<br><br><img src='".$image_url6."'>";
	}
} else {
	echo "<img src='images/no_image.png'>";
}
echo "</td>\n";
echo "<td align='left' valign='top' width='50%'>".$manager_data['manager_description']."</td>\n";
echo "<td align='left' valign='top' width='30%'>";
echo "<p><b>".$locale_pob."</b><br>".$manager_data['manager_pob']."</p>";
echo "<p><b>".$locale_dob."</b><br>".$manager_data['manager_dob']."</p>";

if ($manager_data['manager_pc'] == 1) {
	echo "<p><b>".$locale_playing_career."</b><br>".$manager_data['manager_pc']."</p>";
}
if ($manager_data['manager_player_id'] != 0) {
	$get_player_info = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id
		FROM team_players AS P
		WHERE P.PlayerID = '$id'
		LIMIT 1
	") or die(mysqli_error());
	$player_data = mysqli_fetch_array($get_player_info);
	mysqli_free_result($get_player_info);

	echo "<p><b>".$locale_link_to_stats."</b><br><a href='player.php?id=".$player_data['player_id']."'>".$player_data['player_name']."</p>\n";
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='top'><br><br>".$locale_match_type_filter.": \n";
echo "<select name='match_type_manager'>\n";
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
echo "<input type='submit' name='submit3' value='".$locale_change."'>";
$sql = "SELECT
	M.ManagerID AS manager_id,
	CONCAT(M.ManagerFirstName, ' ', M.ManagerLastName) AS manager_name,
	M.ManagerPublish AS publish
	FROM team_seasons AS S
	LEFT OUTER JOIN team_managers M ON M.ManagerID = S.SeasonManagerID
";
if ($default_season_id == 0) {
	$sql .= "WHERE M.ManagerID != ''
		AND M.ManagerPublish = '1'
		GROUP BY manager_id
		ORDER BY manager_name
	";
} else {
	$sql .= "AND S.SeasonID = '$default_season_id'
		WHERE M.ManagerID != ''
		AND M.ManagerPublish = '1'
		GROUP BY manager_id
		ORDER BY manager_name
	";
}
$get_managers = mysqli_query($db_connect, $sql) or die(mysqli_error());
echo "<br>".$locale_change_manager.":";
echo "<select name='manager_id'>\n";
while($data = mysqli_fetch_array($get_managers)) {
	if ($data['manager_id'] == $id) {
		echo "<option value='".$data['manager_id']."' SELECTED>".$data['manager_name']."</option>\n";
	} else {
		echo "<option value='".$data['manager_id']."'>".$data['manager_name']."</option>\n";
	}
}
echo "</select>\n";
echo "<input type='submit' name='change_manager' value='".$locale_change."'>\n";
mysqli_free_result($get_managers);

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='middle'><b>".$locale_season_short."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$locale_wins_short."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$locale_draws_short."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$locale_loses_short."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$locale_win_proc_short."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$locale_draw_proc_short."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$locale_lose_proc_short."</b></td>\n";
echo "</tr>\n";
$get_seasons = mysqli_query($db_connect, "SELECT
	SN.SeasonName AS season_name,
	SN.SeasonID AS season_id
	FROM team_season_names AS SN, team_seasons AS S
	WHERE SN.SeasonID = S.SeasonID
	AND S.SeasonManagerID = '$id'
	ORDER BY season_name
") or die(mysqli_error());

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
$total_all = 0;
$total_wins = 0;
$total_draws = 0;
$total_loses = 0;
$total_win_pros = 0.00;
$total_draw_pros = 0.00;
$total_lose_pros = 0.00;
$j = 1;
while($data = mysqli_fetch_array($get_seasons)) {
	$season_id = $data['season_id'];

	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	$wins = 0;
	$draws = 0;
	$loses = 0;
	$streak = 0;
	$streak2 = 0;
	$record = 0;
	$record2 = 0;
	$get_timeline = mysqli_query($db_connect, "SELECT
		MT.StartDate AS start_date,
		MT.EndDate AS end_date
		FROM team_managers_time AS MT, team_managers AS M
		WHERE MT.ManagerID = M.ManagerID
		AND M.ManagerID = '$id'
		ORDER BY start_date
	") or die(mysqli_error());
	$y = mysqli_num_rows($get_timeline);

	if ($y > 0) {
		$timeline = " AND ";
		$x = 1;
		while($date_time = mysqli_fetch_array($get_timeline)) {
			$timeline .= "(M.MatchDateTime <= '$date_time[end_date] 00:00:00' AND M.MatchDateTime >= '$date_time[start_date] 00:00:00')";
			if ($x != $y) {
				$timeline .= " OR ";
			}
			$x++;
		}
	}
	mysqli_free_result($get_timeline);

	$timeline = " ";
	$get_matches = mysqli_query($db_connect, "SELECT
		M.MatchGoals AS goals,
		M.MatchGoalsOpponent AS goals_opponent,
		M.MatchDateTime AS match_date
		FROM team_matches AS M
		WHERE M.MatchSeasonID LIKE '$season_id'
		AND M.MatchTypeID LIKE '$tdefault_match_type_id' $timeline
		AND M.MatchGoals IS NOT NULL
		AND M.MatchGoalsOpponent IS NOT NULL
		ORDER BY match_date
	") or die(mysqli_error());
	$k = 0;
	while($match_data = mysqli_fetch_array($get_matches)) {
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
		if ($match_data['goals'] > $match_data['goals_opponent']) {
			$wins = $wins + 1;
			$streak++;
			$streak2++;
			$t = $k + 1;
			$track = 1;
			$track2 = 1;
		}
		if ($match_data['goals'] == $match_data['goals_opponent']) {
			$draws = $draws + 1;
			$streak = 0;
			$track = 0;
			$track2 = 1;
			$streak2++;
		}
		if ($match_data['goals'] < $match_data['goals_opponent']) {
			$loses = $loses + 1;
			$streak = 0;
			$streak2 = 0;
			$track = 0;
			$track2 = 0;
		}
		$k = $k + 1;
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
	$total_all = $total_all + $all;
	$total_wins = $total_wins + $wins;
	$total_draws = $total_draws + $draws;
	$total_loses = $total_loses + $loses;
	echo "<tr align='center' valign='middle' bgcolor='".$bg_color."'>\n";
	echo "<td align='left' valign='middle'>".$data['season_name']."</td>\n";
	echo "<td>".$wins."</td>\n";
	echo "<td>".$draws."</td>\n";
	echo "<td>".$loses."</td>\n";
	echo "<td>".$win_pros." %</td>\n";
	echo "<td>".$draw_pros." %</td>\n";
	echo "<td>".$lose_pros." %</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($get_seasons);

$total_win_pros = round(100*(isset($total_wins)/isset($total_all)), 2);
$total_draw_pros = round(100*(isset($total_draws)/isset($total_all)), 2);
$total_lose_pros = round(100*(isset($total_loses)/isset($total_all)), 2);
echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='middle'><b>".$locale_total."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$total_wins."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$total_draws."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$total_loses."</b></td>\n";
echo "<td align='center' valign='middle'><b>".$total_win_pros." %</b></td>\n";
echo "<td align='center' valign='middle'><b>".$total_draw_pros." %</b></td>\n";
echo "<td align='center' valign='middle'><b>".$total_lose_pros." %</b></td>\n";
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