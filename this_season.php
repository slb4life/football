<?php
include('top.php');
$script_name = "this_season.php?".$_SERVER['QUERY_STRING'];

echo "<form method='post' action='".$script_name."'>\n";
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
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
$default_season_id = DEFAULT_SEASON;
$wins = 0;
$draws = 0;
$loses = 0;
$goals_for = 0;
$goals_against = 0;
$streak = 0;
$streak2 = 0;
$record = 0;
$record2 = 0;
$get_matches = mysqli_query($db_connect, "SELECT
	M.MatchDateTime AS match_date,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent
	FROM team_matches M
	WHERE M.MatchSeasonID  = '$default_season_id'
	AND M.MatchGoals IS NOT NULL
	AND M.MatchGoalsOpponent IS NOT NULL
	ORDER BY match_date
") or die(mysqli_error());
$k = 0;
while($data = mysqli_fetch_array($get_matches)) {
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
	if ($data['goals'] > $data['goals_opponent']) {
		$wins = $wins + 1;
		$streak++;
		$streak2++;
		$t = $k + 1;
		$track = 1;
		$track2 = 1;
	}
	if ($data['goals'] == $data['goals_opponent']) {
		$draws = $draws + 1;
		$streak = 0;
		$track = 0;
		$track2 = 1;
		$streak2++;
	}
	if ($data['goals'] < $data['goals_opponent']) {
		$loses = $loses + 1;
		$streak = 0;
		$streak2 = 0;
		$track = 0;
		$track2 = 0;
	}
	$goals_for = $goals_for + $data['goals'];
	$goals_against = $goals_against + $data['goals_opponent'];
	$k = $k +1;
}
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
mysqli_free_result($get_matches);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='4'><b>".$locale_this_season."</b></td>\n";
echo "</tr>\n";
$get_players = mysqli_query($db_connect, "SELECT
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	P.PlayerID AS player_id,
	P.PlayerPositionID AS player_position_id,
	P.PlayerNumber AS player_number,
	P.PlayerPublish AS publish,
	DATE_FORMAT(P.PlayerDOB, '$how_to_print_in_report') AS player_dob
	FROM (team_players P, team_seasons S)
	WHERE P.PlayerID = S.SeasonPlayerID
	AND S.SeasonID = '$default_season_id'
	AND P.PlayerInSquadList = '1'
	ORDER BY player_position_id, player_number
") or die(mysqli_error());
$n_of_players = mysqli_num_rows($get_players);
$all_players_txt = "";
$k = 1;
while($data = mysqli_fetch_array($get_players)) {
	if ($data['publish'] == 1) {
		$all_players_txt .= "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
	} else {
		$all_players_txt .= "".$data['player_name']."</a>";
	}
	if ($k < $n_of_players) {
		$all_players_txt .= "<br>\n";
	}
	$k++;
}
mysqli_free_result($get_players);

$get_players = mysqli_query($db_connect, "SELECT
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	P.PlayerID AS player_id,
	P.PlayerPublish AS publish,
	DATE_FORMAT(P.PlayerDOB, '$how_to_print_in_report') AS player_dob
	FROM (team_players P, team_seasons S)
	WHERE P.PlayerID = S.SeasonPlayerID
	AND S.SeasonID = '$default_season_id'
	AND P.PlayerDOB NOT LIKE '1900-01-01'
	ORDER BY player_dob DESC
	LIMIT 1
") or die(mysqli_error());
$data = mysqli_fetch_array($get_players);

if ($data['publish'] == 1) {
	$youngest = "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a> (".$locale_dob.": ".$data['player_dob'].")";
} else {
	$youngest = "".$data['player_name']."";
}
mysqli_free_result($get_players);

$get_players = mysqli_query($db_connect, "SELECT
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	P.PlayerID AS player_id,
	P.PlayerPublish AS publish,
	DATE_FORMAT(P.PlayerDOB, '$how_to_print_in_report') AS player_dob
	FROM (team_players P, team_seasons S)
	WHERE P.PlayerID = S.SeasonPlayerID
	AND S.SeasonID = '$default_season_id'
	AND P.PlayerDOB NOT LIKE '1900-01-01'
	ORDER BY player_dob ASC
	LIMIT 1
") or die(mysqli_error());
$data = mysqli_fetch_array($get_players);

if ($data['publish'] == 1) {
	$oldest = "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a> (".$locale_dob.": ".$data['player_dob'].")";
} else {
	$oldest = "".$data['player_name']."";
}
mysqli_free_result($get_players);

echo "<tr>\n";
echo "<td align='left' valign='top' colspan='2'>".$locale_all_players." (".$n_of_players.")<br><a href='stats.php'>".$locale_see_player_stats."</a></td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$all_players_txt."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_youngest_player."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$youngest."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_oldest_player."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$oldest."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='4'><b>".$locale_match_stats."</b></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_all_matches."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$all."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_wins."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$wins."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_draws."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$draws."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_loses."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$loses."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_goals_for."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$goals_for."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_goals_against."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$goals_against."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_longest_winning_streak."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$record."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_longest_undef_streak."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$record2."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='4'><b>".$locale_matchematical."</b></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_winning_percent."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$win_pros." %</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_draw_percent."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$draw_pros." %</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_losing_percent."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>".$lose_pros." %</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='4'><b>".$locale_individual_leaders."</b></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_most_appearances."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>";
$get_appearances = mysqli_query($db_connect, "SELECT
	P.PlayerID AS player_id,
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	COUNT( A.AppearancePlayerID ) AS appearance_player_id
	FROM team_seasons S
	LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
	AND S.SeasonID = '$default_season_id'
	LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID
	LEFT OUTER JOIN team_appearances A ON A.AppearancePlayerID = S.SeasonPlayerID
	AND A.AppearanceMatchID = M.MatchID
	WHERE P.PlayerID != ''
	GROUP BY player_id
	ORDER BY appearance_player_id DESC, player_name
") or die(mysqli_error());
$i = 0;
$check = 92892892892;
while($data = mysqli_fetch_array($get_appearances)) {
	if ($i == 0) {
		echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
		$most_appearances = $data['appearance_player_id'];
	}
	if ($i > 0) {
		if ($data['appearance_player_id'] == $check) {
			echo ", <a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
		} else {
			break;
		}
	}
	$check = $data['appearance_player_id'];
	$i++;
}

if ($data['appearance_player_id'] == 0) {
	echo " (".$most_appearances.")";
} else {
	echo " (0)";
}
mysqli_free_result($get_appearances);

echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_most_goals."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>";
$get_goals = mysqli_query($db_connect, "SELECT
	P.PlayerID AS player_id,
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	COUNT( G.GoalPlayerID ) AS goal_player_id
	FROM team_seasons S
	LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
	AND S.SeasonID LIKE '$default_season_id'
	LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID
	LEFT OUTER JOIN team_goals G ON G.GoalPlayerID = S.SeasonPlayerID
	AND G.GoalMatchID = M.MatchID
	AND G.GoalOwn = '0'
	WHERE P.PlayerID != ''
	GROUP BY player_id
	ORDER BY goal_player_id DESC, player_name
") or die(mysqli_error());
$i = 0;
$check = 92892892892;
while($data = mysqli_fetch_array($get_goals)) {
	if ($i == 0) {
		echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
		$most_goals = $data['goal_player_id'];
	}
	if ($i > 0) {
		if ($data['goal_player_id'] == $check) {
			echo ", <a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
		} else {
			break;
		}
	}
	$check = $data['goal_player_id'];
	$i++;
}
if ($data['goal_player_id'] == 1) {
	echo " (".$most_goals.")";
} else {
	echo " (0)";
}
mysqli_free_result($get_goals);

echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_most_assists."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>";
$get_assists = mysqli_query($db_connect, "SELECT
	P.PlayerID AS player_id,
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	COUNT( GA.GoalAssistPlayerID ) AS goal_assist_player_id
	FROM team_seasons S
	LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
	AND S.SeasonID = '$default_season_id'
	LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID
	LEFT OUTER JOIN team_goal_assists GA ON GA.GoalAssistPlayerID = S.SeasonPlayerID
	AND GA.GoalAssistMatchID = M.MatchID
	WHERE P.PlayerID != ''
	GROUP BY player_id
	ORDER BY goal_assist_player_id DESC, player_name
") or die(mysqli_error());
$i = 0;
$check = 92892892892;
while($data = mysqli_fetch_array($get_assists)) {
	if ($i == 0) {
		echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
		$most_assists = $data['goal_assist_player_id'];
	}
	if ($i > 0) {
		if ($data['goal_assist_player_id'] == $check) {
			echo ", <a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
		} else {
			break;
		}
	}
	$check = $data['goal_assist_player_id'];
	$i++;
}
if ($data['goal_assist_player_id'] == 1) {
	echo " (".$most_assists.")";
} else {
	echo " (0)";
}
mysqli_free_result($get_assists);

echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='2'>".$locale_most_booked."</td>\n";
echo "<td align='left' valign='middle' colspan='2'>";
$get_yellows = mysqli_query($db_connect, "SELECT
	P.PlayerID AS player_id,
	CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
	COUNT( Y.YellowCardPlayerID ) AS yellow_card_player_id
	FROM team_seasons S
	LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID
	AND S.SeasonID = '$default_season_id'
	LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID
	LEFT OUTER JOIN team_yellow_cards Y ON Y.YellowCardPlayerID = S.SeasonPlayerID
	AND Y.YellowCardMatchID = M.MatchID
	WHERE P.PlayerID != ''
	GROUP BY player_id
	ORDER BY yellow_card_player_id DESC, player_name
") or die(mysqli_error());
$i = 0;
$check = 92892892892;
while($data = mysqli_fetch_array($get_yellows)) {
	if ($i == 0) {
		echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
		$most_yellows = $data['yellow_card_player_id'];
	}
	if ($i > 0) {
		if ($data['yellow_card_player_id'] == $check) {
			echo ", <a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
		} else {
			break;
		}
	}
	$check = $data['yellow_card_player_id'];
	$i++;
}
if ($data['yellow_card_player_id'] == 1) {
	echo " (".$most_yellows.")";
} else {
	echo " (0)";
}
mysqli_free_result($get_yellows);

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