<?php
include('top.php');
$script_name = "this_day.php?".$_SERVER['QUERY_STRING'];
$team_name = TEAM_NAME;

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
echo "<td align='left' valign='middle' colspan='5'>".$locale_this_date_in_history."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_date_and_time."</b></td>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_match_type."</b></td>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_home_team."</b></td>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_away_team."</b></td>\n";
echo "<td align='center' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><b>".$locale_final_score."</b></td>\n";
echo "</tr>\n";
$day_of_month = date("d");
$month_number = date("m");
$get_matches = mysqli_query($db_connect, "SELECT
	M.MatchID AS match_id,
	M.MatchAdditionalType AS match_additional_type,
	O.OpponentName AS opponent_name,
	O.OpponentID AS opponent_id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPenaltyGoals AS penalty_goals,
	M.MatchPenaltyGoalsOpponent AS penalty_goals_opponent,
	M.MatchOvertime AS match_overtime,
	M.MatchPenaltyShootout AS penalty_shootout,
	DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date,
	M.MatchPlaceID AS match_place_id,
	M.MatchPublish AS publish,
	MT.MatchTypeName AS match_type_name
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID
	AND M.MatchOpponent = O.OpponentID
	AND MONTH(M.MatchDateTime) = '$month_number'
	AND DAYOFMONTH(M.MatchDateTime) = '$day_of_month'
	ORDER BY match_date
") or die(mysqli_error());
$i = 1;
while($data = mysqli_fetch_array($get_matches)) {
	if ($i % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	if ($data['match_place_id'] == 1) {
		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_date']."</td>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_type_name']."";

		if ($data['match_additional_type'] != '') {
			echo " / ".$data['match_additional_type']."";
		}
		echo "</td>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$team_name."</td>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>";

		if ($data['opponent_id'] == 1) {
			echo "".$data['opponent_name']."";
		} else {
			echo "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a>";
		}
		echo "</td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

		if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
			if ($data['preview_text'] == '') {
				echo "&nbsp;";
			} else {
				echo "<a href='preview.php?id=".$data['match_id']."'>".$locale_preview."</a>";
			}
		} else {
			if ($data['publish'] == 1) {
				if ($data['penalty_goals'] == NULL || $data['penalty_goals_opponent'] == NULL) {
					echo "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";

					if ($data['match_overtime'] == 1) {
						echo " ".$locale_overtime_short."";
					}
				} else {
					echo "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals']." - ".$data['goals_opponent']." (".$data['penalty_goals']." - ".$data['penalty_goals_opponent']." ".$locale_penalty_shootout_short.")</a>";
				}
			} else {
				if ($data['penalty_goals'] == NULL || $data['penalty_goals_opponent'] == NULL) {
					echo "".$data['goals']." - ".$data['goals_opponent']."";

					if ($data['match_overtime'] == 1) {
						echo " ".$locale_overtime_short."";
					}
				} else {
					echo "".$data['goals']." - ".$data['goals_opponent']." (".$data['penalty_goals']." - ".$data['penalty_goals_opponent']." ".$locale_penalty_shootout_short.")";
				}
			}
		}
		echo "</td>\n";
		echo "</tr>\n";
	} else {
		echo "<tr>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_date']."</td>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['match_type_name']."";

		if ($data['match_additional_type'] != '') {
			echo " / ".$data['match_additional_type']."";
		}
		echo "</td>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>";

		if ($data['opponent_id'] == 1) {
			echo "".$data['opponent_name']."";
		} else {
			echo "<a href='opponent.php?id=".$data['opponent_id']."'>".$data['opponent_name']."</a>";
		}
		echo "</td>\n";
		echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$team_name."</td>\n";
		echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";

		if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
			if ($data['preview_text'] == '') {
				echo "&nbsp;";
			} else {
				echo "<a href='preview.php?id=".$data['match_id']."'>".$locale_preview."</a>";
			}
		} else {
			if ($data['publish'] == 1) {
				if ($data['penalty_goals'] == NULL || $data['penalty_goals_opponent'] == NULL) {
					echo "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals_opponent']." - ".$data['goals']."</a>";

					if ($data['match_overtime'] == 1) {
						echo " ".$locale_overtime_short."";
					}
				} else {
					echo "<a href='match_details.php?id=".$data['match_id']."'>".$data['goals_opponent']." - ".$data['goals']." (".$data['penalty_goals_opponent']." - ".$data['penalty_goals']." ".$locale_penalty_shootout_short.")</a>";
				}
			} else {
				if ($data['penalty_goals'] == NULL || $data['penalty_goals_opponent'] == NULL) {
					echo "".$data['goals_opponent']." - ".$data['goals']."";

					if ($data['match_overtime'] == 1) {
						echo " ".$locale_overtime_short."";
					}
				} else {
					echo "".$data['goals_opponent']." - ".$data['goals']." (".$data['penalty_goals_opponent']." - ".$data['penalty_goals']." ".$locale_penalty_shootout_short.")";
				}
			}
		}
		echo "</td>\n";
		echo "</tr>\n";
	}
	$i++;
}
if (mysqli_num_rows($get_matches) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' colspan='5'>".$locale_not_matches_in_this_date_in_match_history."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($get_matches);

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