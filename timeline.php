<?php
include('top.php');
$script_name = "timeline.php?".$_SERVER['QUERY_STRING'];

if (isset($_POST['get_stats'])) {
	$get_stats = $_POST['get_stats'];
}
if (isset($_POST['start_day']) || isset($_POST['start_month']) || isset($_POST['start_year'])) {
	$start_day = $_POST['start_day'];
	$start_month = $_POST['start_month'];
	$start_year = $_POST['start_year'];
}
if (isset($_POST['end_day']) || isset($_POST['end_month']) || isset($_POST['end_year'])) {
	$end_day = $_POST['end_day'];
	$end_month = $_POST['end_month'];
	$end_year = $_POST['end_year'];
}
if (isset($start_year) || isset($start_month)  || isset($start_day)) {
	$match_start_date = $start_year."-".$start_month."-".$start_day."00:00:00";
}
if (isset($end_year) || isset($end_month)  || isset($end_day)) {
	$match_end_date = $end_year."-".$end_month."-".$end_day."00:00:00";
}
if (isset($_POST['match_place_id'])) {
	$match_place_id = $_POST['match_place_id'];
}
echo "<form method='post' action='".$script_name."'>\n";
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='4'><font class='bigname'>".$locale_timeline."</font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='2' width='50%'><b>".$locale_start_date."</b></td>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='2' width='50%'>\n";
echo "<select name='start_day'>\n";
for ($i = 1 ; $i < 32 ; $i++) {
	if ($i < 10) {
		$i = "0".$i;
	}
	if (isset($get_stats)) {
		if ($i == $start_day) {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	} else {
		if ($i == "01") {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	}
}
echo "</select>\n";
echo "<select name='start_month'>\n";
for ($i = 1 ; $i < 13 ; $i++) {
	if ($i < 10) {
		$i = "0".$i;
	}
	if (isset($get_stats)) {
		if ($i == $start_month) {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	} else {
		if ($i == "01") {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	}
}
echo "</select>\n";
echo "<select name='start_year'>\n";
for ($i = 2000 ; $i < 2016 ; $i++) {
	if ($i < 10) {
		$i = "0".$i;
	}
	if (isset($get_stats)) {
		if ($i == $start_year) {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	} else {
		if ($i == "2014") {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	}
}
echo "</select>\n";
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='2' width='50%'><b>".$locale_end_date."</b></td>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='2' width='50%'>\n";
echo "<select name='end_day'>\n";
for ($i = 1 ; $i < 32 ; $i++) {
	if ($i < 10) {
		$i = "0".$i;
	}
	if (isset($get_stats)) {
		if ($i == $end_day) {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	} else {
		if ($i == "31") {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	}
}
echo "</select>\n";
echo "<select name='end_month'>\n";
for ($i = 1 ; $i < 13 ; $i++) {
	if ($i < 10) {
		$i = "0".$i;
	}
	if (isset($get_stats)) {
		if ($i == $end_month) {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	} else {
		if ($i == "12") {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	}
}
echo "</select>\n";
echo "<select name='end_year'>\n";
for ($i = 2000 ; $i < 2016 ; $i++) {
	if ($i < 10) {
		$i = "0".$i;
	}
	if (isset($get_stats)) {
		if ($i == $end_year) {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	} else {
		if ($i == "2014") {
			echo "<option value='".$i."' selected>".$i."</option>\n";
		} else {
			echo "<option value='".$i."'>".$i."</option>\n";
		}
	}
}
echo "</select>\n";
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='2' width='50%'><b>".$locale_place."</b></td>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='2' width='50%'>\n";
echo "<select name='match_place_id'>\n";
if (isset($get_stats)) {
	if ($match_place_id == "%") {
		echo "<option value='%' selected>".$locale_all."</option>\n";
	} else {
		echo "<option value='%'>".$locale_all."</option>\n";
	}
	if ($match_place_id == "1") {
		echo "<option value='1' selected>".$locale_home."</option>\n";
	} else {
		echo "<option value='1'>".$locale_home."</option>\n";
	}
	if ($match_place_id == "2") {
		echo "<option value='2' selected>".$locale_away."</option>\n";
	} else {
		echo "<option value='2'>".$locale_away."</option>\n";
	}
} else {
	echo "<option value='%'>".$locale_all."</option>\n";
	echo "<option value='1'>".$locale_home."</option>\n";
	echo "<option value='2'>".$locale_away."</option>\n";
}
echo "</select>\n";
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' colspan='4'><input type='submit' name='get_stats' value='".$locale_get_stats."'></td>\n";
echo "</tr>\n";

if (!isset($get_stats)) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' colspan='4'>".$locale_info_timeline."</td>\n";
	echo "</tr>\n";
} else {
	$start_day = mysqli_real_escape_string($db_connect, $_POST['start_day']);
	$start_month = mysqli_real_escape_string($db_connect, $_POST['start_month']);
	$start_year = mysqli_real_escape_string($db_connect, $_POST['start_year']);
	$end_day = mysqli_real_escape_string($db_connect, $_POST['end_day']);
	$end_month = mysqli_real_escape_string($db_connect, $_POST['end_month']);
	$end_year = mysqli_real_escape_string($db_connect, $_POST['end_year']);
	$match_place_id = mysqli_real_escape_string($db_connect, $_POST['match_place_id']);
	$match_start_date = mysqli_real_escape_string($db_connect, $match_start_date);
	$match_end_date = mysqli_real_escape_string($db_connect, $match_end_date);
	$wins = 0;
	$draws = 0;
	$loses = 0;
	$goals_for = 0;
	$goals_against = 0;
	$streak = 0;
	$streak2 = 0;
	$record = 0;
	$record2 = 0;

	if ($default_match_type_id == 0) {
		$tdefault_match_type_id = '%';
	} else {
		$tdefault_match_type_id = $default_match_type_id;
	}
	if ($default_season_id == 0) {
		$tdefault_season_id = '%';
	} else {
		$tdefault_season_id = $default_season_id;
	}
	$get_matches = mysqli_query($db_connect, "SELECT
		M.MatchGoals AS goals,
		M.MatchGoalsOpponent AS goals_opponent
		FROM team_matches M
		WHERE M.MatchTypeID LIKE '$tdefault_match_type_id'
		AND M.MatchDateTime <= '$match_end_date'
		AND M.MatchDateTime >= '$match_start_date'
		AND M.MatchPlaceID LIKE '$match_place_id'
		AND M.MatchGoals IS NOT NULL
		AND M.MatchGoalsOpponent IS NOT NULL
		ORDER BY M.MatchDateTime
	") or die(mysqli_error());
	$k = 0;
	while($data = mysqli_fetch_array($get_matches)) {
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
		AND S.SeasonID LIKE '%'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID
		AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		AND M.MatchDateTime <= '$match_end_date'
		AND M.MatchDateTime >= '$match_start_date'
		AND M.MatchPlaceID LIKE '$match_place_id'
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
		AND S.SeasonID LIKE '%'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID
		AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		AND M.MatchDateTime <= '$match_end_date'
		AND M.MatchDateTime >= '$match_start_date'
		AND M.MatchPlaceID LIKE '$match_place_id'
		LEFT OUTER JOIN team_goals G ON G.GoalPlayerID = S.SeasonPlayerID
		AND G.GoalMatchID = M.MatchID AND G.GoalOwn = '0'
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
		AND S.SeasonID LIKE '%'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID
		AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		AND M.MatchDateTime <= '$match_end_date'
		AND M.MatchDateTime >= '$match_start_date'
		AND M.MatchPlaceID LIKE '$match_place_id'
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
		AND S.SeasonID LIKE '%'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID
		AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		AND M.MatchDateTime <= '$match_end_date'
		AND M.MatchDateTime >= '$match_start_date'
		AND M.MatchPlaceID LIKE '$match_place_id'
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