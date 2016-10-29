<?php
include('top.php');
$script_name = "match_details.php?".$_SERVER['QUERY_STRING'];
$team_name  = TEAM_NAME;

echo "<form method='post' action='change.php'>\n";
echo "<input name='script_name' type='hidden' value='".$script_name."'>\n";
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td align='center' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";

$id = $_REQUEST['id'];

if ($id == '' || !is_numeric($id)) {
	$id = 1;
}

switch (PRINT_DATE) {
	case 1: {
		$how_to_print_in_report = "%d.%m.%Y $locale_at %H:%i";
	}
	break;
	case 2: {
		$how_to_print_in_report = "%m.%d.%Y $locale_at %H:%i";
	}
	break;
	case 3: {
		$how_to_print_in_report = "%b %D %Y $locale_at %H:%i";
	}
	break;
}
$get_details = mysqli_query($db_connect, "SELECT
	O.OpponentName AS opponent_name,
	O.OpponentID AS opponent_id,
	M.MatchID AS match_id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPenaltyGoals AS penalty_goals,
	M.MatchPenaltyGoalsOpponent AS penalty_goals_opponent,
	M.MatchOvertime AS match_overtime,
	M.MatchPenaltyShootout AS penalty_shootout,
	M.MatchGoalscorersOpponent AS goal_scorers_opponent,
	M.MatchOpeningOpponent AS opening_opponent,
	M.MatchSubstitutesOpponent AS substitutes_opponent,
	M.MatchSubstitutionsOpponent AS substitutions_opponent,
	M.MatchGoalAssistsOpponent AS goal_assists_opponent,
	M.MatchYellowCardsOpponent AS yellow_cards_opponent,
	M.MatchRedCardsOpponent AS red_cards_opponent,
	M.MatchShots AS shots,
	M.MatchShotsOpponent AS shots_opponent,
	M.MatchShotsOnGoal AS shots_on_goal,
	M.MatchShotsOnGoalOpponent AS shots_on_goal_opponent,
	M.MatchOffsides AS offsides,
	M.MatchOffsidesOpponent AS offsides_opponent,
	M.MatchCorners AS corners,
	M.MatchCornersOpponent AS corners_opponent,
	M.MatchFreekicks AS freekicks,
	M.MatchFreekicksOpponent AS freekicks_opponent,
	M.MatchPenalties AS penalties,
	M.MatchPenaltiesOpponent AS penalties_opponent,
	DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date,
	M.MatchPlaceID AS match_place_id,
	M.MatchReport AS match_report,
	M.MatchReferee AS match_referee,
	M.MatchAttendance AS match_attendance,
	M.MatchStadium AS match_stadium,
	M.MatchReport AS match_report,
	M.MatchAdditionalType AS match_additional_type,
	MT.MatchTypeName AS match_type_name,
	M.MatchPublishOptional AS publish_optional,
	P.PreviewText AS preview_text
	FROM (team_matches M, team_match_types MT, team_opponents O)
	LEFT OUTER JOIN team_previews P ON M.MatchID = P.PreviewMatchID
	WHERE M.MatchID = '$id'
	AND M.MatchTypeID = MT.MatchTypeID
	AND M.MatchOpponent = O.OpponentID
	LIMIT 1
") or die(mysqli_error());
$mdata = mysqli_fetch_array($get_details);
mysqli_free_result($get_details);

$logos = 0;
$image_url_1 = "images/team_logo.png";
$image_url_2 = "images/team_logo.jpg";
$image_url_3 = "images/opponent_logo_".$mdata['opponent_id'].".png";
$image_url_4 = "images/opponent_logo_".$mdata['opponent_id'].".jpg";

if ((file_exists($image_url_1) && file_exists($image_url_3)) || (file_exists($image_url_2) && file_exists($image_url_4)) || (file_exists($image_url_1) && file_exists($image_url_4)) || (file_exists($image_url_2) && file_exists($image_url_3))) {
	$logos = 1;
}
if ($mdata['goal_scorers_opponent'] == ''){ $mdata['goal_scorers_opponent'] = "".$locale_none.""; }
if ($mdata['opening_opponent'] == ''){ $mdata['opening_opponent'] = "".$locale_none.""; }
if ($mdata['substitutes_opponent'] == ''){ $mdata['substitutes_opponent'] = "".$locale_none.""; }
if ($mdata['substitutions_opponent'] == ''){ $mdata['substitutions_opponent'] = "".$locale_none.""; }
if ($mdata['goal_assists_opponent'] == ''){ $mdata['goal_assists_opponent'] = "".$locale_none.""; }
if ($mdata['yellow_cards_opponent'] == ''){ $mdata['yellow_cards_opponent'] = "".$locale_none.""; }
if ($mdata['red_cards_opponent'] == ''){ $mdata['red_cards_opponent'] = "".$locale_none.""; }

echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='middle' width='50%'><b>".$mdata['match_date']." / ".$locale_stadium.": ".$mdata['match_stadium']."</b></td>\n";
echo "<td align='right' valign='middle' width='50%'><b>".$locale_attendance.":</b> ".$mdata['match_attendance']."</td>\n";
echo "</tr>\n";
echo "</table>\n";

if ($mdata['match_place_id'] == 1) { 
	echo "<table width='100%' align='center' cellspacing='2' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' width='45%'>\n";

	if ($logos == 1) {
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
		echo "<tr>\n";
		echo "<td width='5%' valign='middle' align='center'>";

		if (file_exists($image_url_1)) {
			echo "<img src='".$image_url_1."' alt=''>";
		} else {
			echo "<img src='".$image_url_2."' alt=''>";
		}
		echo "</td>\n";
		echo "<td width='95%' valign='middle' align='left'><font class='bigname'>".$team_name."</font></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} else {
		echo "<font class='bigname'>".$team_name."</font>";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' width='10%' bgcolor='#".(BGCOLOR1)."'><font class='bigname'>";

	if ($mdata['penalty_goals'] == NULL || $mdata['penalty_goals_opponent'] == NULL) {
		echo "".$mdata['goals']." - ".$mdata['goals_opponent']."";
		if ($mdata['match_overtime'] == 1) {
			echo " ".$locale_overtime_short."";
		}
	} else {
		echo "".$mdata['goals']." - ".$mdata['goals_opponent']."<br>(".$mdata['penalty_goals']." - ".$mdata['penalty_goals_opponent']." ".$locale_penalty_shootout_short.")";
	}
	echo "</font></td>\n";
	echo "<td align='right' valign='middle' width='45%'>\n";

	if ($logos == 1) {
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
		echo "<tr>\n";
		echo "<td width='95%' valign='middle' align='right'><font class='bigname'>".$mdata['opponent_name']."</font></td>\n";
		echo "<td width='5%' valign='middle' align='center'>";

		if (file_exists($image_url_3)) {
			echo "<img src='".$image_url_3."' alt=''>";
		} else {
			echo "<img src='".$image_url_4."' alt=''>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} else {
		echo "<font class='bigname'>".$mdata['opponent_name']."</font>";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle' width='50%'><b>".$locale_referee.":</b> ".$mdata['match_referee']."</td>\n";
	echo "<td align='right' valign='middle' width='50%'>";

	if ($mdata['match_additional_type'] == '') {
		echo "".$mdata['match_type_name']."-".$locale_match."";
	} else {
		echo "".$mdata['match_type_name']." / ".$mdata['match_additional_type']."-".$locale_match."";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td><b>".$locale_goal_scorers."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>";
	$get_goal_scorers = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerPublish AS publish,
		G.GoalMinute AS goal_minute,
		G.GoalOwn AS goal_own,
		G.GoalOwnScorer AS goal_own_scorer,
		G.GoalPlayerID AS player_id,
		G.GoalPenalty AS goal_penalty
		FROM team_players P, team_goals G
		WHERE G.GoalMatchID = '$id'
		AND P.PlayerID = G.GoalPlayerID
		ORDER BY goal_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_goal_scorers) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_goal_scorers)) {
			if ($data['goal_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['goal_minute'].")";
			}
			if ($data['goal_own'] == 1) {
				echo "".$data['goal_own_scorer']." (".$locale_own_goal_short.")".$check_minute."<br>\n";
			} else if ($data['goal_penalty'] == 1) {
				if ($data['publish'] == 1) {
					echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a> (".$locale_penalty_short.")".$check_minute."<br>\n";
				} else {
					echo "".$data['player_name']." (".$locale_penalty_short.")".$check_minute."<br>\n";
				}
			} else {
				if ($data['publish'] == 1) {
					echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>".$check_minute."<br>\n";
				} else {
					echo "".$data['player_name']."".$check_minute."<br>\n";
				}
			}
		}
	}
	mysqli_free_result($get_goal_scorers);

	echo "</td>\n";
	echo "<td align='right' valign='top' width='50%'>".$mdata['goal_scorers_opponent']."</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	
	echo "<td align='left' valign='middle'><b>".$locale_opening_squads."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>\n";
	$get_appearances = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPositionID AS player_position_id,
		P.PlayerNumber AS player_number,
		P.PlayerPublish AS publish
		FROM team_players P, team_appearances A
		WHERE A.AppearanceMatchID = '$id'
		AND P.PlayerID = A.AppearancePlayerID
		ORDER BY player_position_id, player_number
	") or die(mysqli_error());

	if (mysqli_num_rows($get_appearances) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_appearances)) {
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a><br>\n";
			} else {
				echo "".$data['player_name']."<br>\n";
			}
		}
	}
	mysqli_free_result($get_appearances);

	echo "</td>\n";
	echo "<td align='right' valign='top' width='50%'>".$mdata['opening_opponent']."</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_substitutes."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>";
	$get_substitutes = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPositionID AS player_position_id,
		P.PlayerNumber AS player_number,
		P.PlayerPublish AS publish
		FROM team_players P, team_substitutes S
		WHERE S.SubstituteMatchID = '$id'
		AND P.PlayerID = S.SubstitutePlayerID
		ORDER BY player_position_id, player_number
	") or die(mysqli_error());

	if (mysqli_num_rows($get_substitutes) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_substitutes)) {
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a><br>\n";
			} else {
				echo "".$data['player_name']."<br>\n";
			}
		}
	}
	mysqli_free_result($get_substitutes);

	echo "</td>\n";
	echo "<td align='right' valign='top' width='50%'>".$mdata['substitutes_opponent']."</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_substitutions."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>";
	$get_substitutions = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		CONCAT(PL.PlayerFirstName, ' ', PL.PlayerLastName) AS player_name2,
		P.PlayerID AS player_id,
		PL.PlayerID AS player_id2,
		P.PlayerPublish AS publish,
		PL.PlayerPublish AS publish2,
		S.SubstitutionMinute AS substitution_minute
		FROM team_players P, team_players PL, team_substitutions S
		WHERE S.SubstitutionMatchID = '$id'
		AND P.PlayerID = S.SubstitutionPlayerIDIn
		AND PL.PlayerID = S.SubstitutionPlayerIDOut
		ORDER BY substitution_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_substitutions) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_substitutions)) {
			if ($data['substitution_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['substitution_minute'].")";
			}
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
			} else {
				echo "".$data['player_name']."";
				echo " -> ";
			}
			if ($data['publish2'] == 1) {
				echo "<a href='player.php?id=".$data['player_id2']."'>".$data['player_name2']."</a>".$check_minute."<br>\n";
			} else {
				echo "".$data['player_name2']."".$check_minute."<br>\n";
			}
		}
	}
	mysqli_free_result($get_substitutions);

	echo "</td>\n";
	echo "<td align='right' valign='top' width='50%'>".$mdata['substitutions_opponent']."</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_goal_assists_long."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>";
	$get_goal_assists = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPublish AS publish,
		GA.GoalAssistMinute AS goal_assist_minute
		FROM team_players P, team_goal_assists GA
		WHERE GA.GoalAssistMatchID = '$id'
		AND P.PlayerID = GA.GoalAssistPlayerID
		ORDER BY goal_assist_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_goal_assists) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_goal_assists)) {
			if ($data['goal_assist_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['goal_assist_minute'].")";
			}
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>".$check_minute."<br>\n";
			} else {
				echo "".$data['player_name']."".$check_minute."<br>\n";
			}
		}
	}
	mysqli_free_result($get_goal_assists);

	echo "</td>\n";
	echo "<td align='right' valign='top' width='50%'>".$mdata['goal_assists_opponent']."</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_yellow_cards_long."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>";
	$get_yellows = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPublish AS publish,
		Y.YellowCardMinute AS yellow_card_minute
		FROM team_players P, team_yellow_cards Y
		WHERE Y.YellowCardMatchID = '$id'
		AND P.PlayerID = Y.YellowCardPlayerID
		ORDER BY yellow_card_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_yellows) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_yellows)) {
			if ($data['yellow_card_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['yellow_card_minute'].")";
			}
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>".$check_minute."<br>\n";
			} else {
				echo "".$data['player_name']."".$check_minute."<br>\n";
			}
		}
	}
	mysqli_free_result($get_yellows);

	echo "</td>\n";
	echo "<td align='right' valign='top' width='50%'>".$mdata['yellow_cards_opponent']."</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_red_cards_long."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>";
	$get_reds = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPublish AS publish,
		R.RedCardMinute AS red_card_minute
		FROM team_players P, team_red_cards R
		WHERE R.RedCardMatchID = '$id'
		AND P.PlayerID = R.RedCardPlayerID
		ORDER BY red_card_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_reds) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_reds)) {
			if ($data['red_card_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['red_card_minute'].")";
			}
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>".$check_minute."<br>\n";
			} else {
				echo "".$data['player_name']."".$check_minute."<br>\n";
			}
		}
	}
	mysqli_free_result($get_reds);

	echo "</td>\n";
	echo "<td align='right' valign='top' width='50%'>".$mdata['red_cards_opponent']."</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	if ($mdata['publish_optional'] == 1) {
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
		echo "<td align='left' valign='middle'><b>".$locale_other_statistics."</b></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['shots']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_shots."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['shots_opponent']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['shots_on_goal']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_shots_on_goal."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['shots_on_goal_opponent']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['offsides']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_offsides."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['offsides_opponent']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['corners']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_corners."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['corners_opponent']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['freekicks']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_freekicks."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['freekicks_opponent']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['penalties']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_penalties."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['penalties_opponent']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
} else {
	echo "<table width='100%' align='center' cellspacing='2' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' width='45%'>";

	if ($logos == 1) {
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
		echo "<tr>\n";
		echo "<td width='5%' valign='middle' align='center'>";

		if (file_exists($image_url_3)) {
			echo "<img src='".$image_url_3."' alt=''>";
		} else {
			echo "<img src='".$image_url_4."' alt=''>";
		}
		echo "</td>\n";
		echo "<td width='95%' valign='middle' align='left'><font class='bigname'>".$mdata['opponent_name']."</font></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} else {
		echo "<font class='bigname'>".$mdata['opponent_name']."</font>\n";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' width='10%' bgcolor='".(BGCOLOR1)."'>";
	echo "<font class='bigname'>";

	if ($mdata['penalty_goals'] == NULL || $mdata['penalty_goals_opponent'] == NULL) {
		echo "".$mdata['goals_opponent']." - ".$mdata['goals']."";

		if ($mdata['match_overtime'] == 1) {
			echo " ".$locale_overtime_short."";
		}
	} else {
		echo "".$mdata['goals_opponent']." - ".$mdata['goals']."<br>(".$mdata['penalty_goals_opponent']." - ".$mdata['penalty_goals']." ".$locale_penalty_shootout_short.")";
	}
	echo "</font>\n";
	echo "</td>\n";
	echo "<td align='right' valign='middle' width='45%'>";

	if ($logos == 1) {
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
		echo "<tr>\n";
		echo "<td width='95%' valign='middle' align='right'><font class='bigname'>".$team_name."</font></td>\n";
		echo "<td width='5%' valign='middle' align='center'>";

		if (file_exists($image_url_1)) {
			echo "<img src='".$image_url_1."' alt=''>";
		} else {
			echo "<img src='".$image_url_2."' alt=''>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>";
	} else {
		echo "<font class='bigname'>".$team_name."</font>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle' width='50%'><b>".$locale_referee.":</b> ".$mdata['match_referee']."</td>\n";
	echo "<td align='right' valign='middle' width='50%'>\n";

	if ($mdata['match_additional_type'] == '') {
		echo "".$mdata['match_type_name']."-".$locale_match."";
	} else {
		echo "".$mdata['match_type_name']." / ".$mdata['match_additional_type']."-".$locale_match."";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td><b>".$locale_goal_scorers."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>".$mdata['goal_scorers_opponent']."</td>\n";
	echo "<td align='right' valign='top' width='50%'>";
	$get_goal_scorers = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPublish AS publish,
		G.GoalMinute AS goal_minute,
		G.GoalOwn AS goal_own,
		G.GoalOwnScorer AS goal_own_scorer,
		G.GoalPenalty AS goal_penalty
		FROM team_players P, team_goals G
		WHERE G.GoalMatchID = '$id'
		AND P.PlayerID = G.GoalPlayerID
		ORDER BY goal_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_goal_scorers) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_goal_scorers)) {
			if ($data['goal_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['goal_minute'].")";
			}
			if ($data['goal_own'] == 1) {
				echo "".$data['goal_own_scorer']." (".$locale_own_goal_short.")".$check_minute."<br>\n";
			} else if ($data['goal_penalty'] == 1) {
				if ($data['publish'] == 1) {
					echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a> (".$locale_penalty_short.")".$check_minute."<br>\n";
				} else {
					echo "".$data['player_name']." (".$locale_penalty_short.")".$check_minute."<br>\n";
				}
			} else {
				if ($data['publish'] == 1) {
					echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>".$check_minute."<br>\n";
				} else {
					echo "".$data['player_name']."".$check_minute."<br>\n";
				}
			}
		}
	}
	mysqli_free_result($get_goal_scorers);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_opening_squads."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>".$mdata['opening_opponent']."</td>\n";
	echo "<td align='right' valign='top' width='50%'>";
	$get_appearances = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPositionID AS player_position_id,
		P.PlayerNumber AS player_number,
		P.PlayerPublish AS publish
		FROM team_players P, team_appearances A
		WHERE A.AppearanceMatchID = '$id'
		AND P.PlayerID = A.AppearancePlayerID
		ORDER BY player_position_id, player_number
	") or die(mysqli_error());

	if (mysqli_num_rows($get_appearances) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_appearances)) {
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a><br>\n";
			} else {
				echo "".$data['player_name']."<br>\n";
			}
		}
	}
	mysqli_free_result($get_appearances);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_substitutes."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>".$mdata['substitutes_opponent']."</td>\n";
	echo "<td align='right' valign='top' width='50%'>";
	$get_substitutes = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPositionID AS player_position_id,
		P.PlayerNumber AS player_number,
		P.PlayerPublish AS publish
		FROM team_players P, team_substitutes S
		WHERE S.SubstituteMatchID = '$id'
		AND P.PlayerID = S.SubstitutePlayerID
		ORDER BY player_position_id, player_number
	") or die(mysqli_error());

	if (mysqli_num_rows($get_substitutes) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_substitutes)) {
			if($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a><br>\n";
			} else {
				echo "".$data['player_name']."<br>\n";
			}
		}
	}
	mysqli_free_result($get_substitutes);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_substitutions."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>".$mdata['substitutions_opponent']."</td>\n";
	echo "<td align='right' valign='top' width='50%'>";
	$get_substitutions = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		CONCAT(PL.PlayerFirstName, ' ', PL.PlayerLastName) AS player_name2,
		P.PlayerID AS player_id,
		PL.PlayerID AS player_id2,
		P.PlayerPublish AS publish,
		PL.PlayerPublish AS publish2,
		S.SubstitutionMinute AS substitution_minute
		FROM team_players P, team_players PL, team_substitutions S
		WHERE S.SubstitutionMatchID = '$id'
		AND P.PlayerID = S.SubstitutionPlayerIDIn
		AND PL.PlayerID = S.SubstitutionPlayerIDOut
		ORDER BY substitution_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_substitutions) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_substitutions)) {
			if ($data['substitution_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['substitution_minute'].")";
			}
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>";
			} else {
				echo "".$data['player_name']."";
				echo " -> ";
			}
			if ($data['publish2'] == 1) {
				echo "<a href='player.php?id=".$data['player_id2']."'>".$data['player_name2']."</a>".$check_minute."<br>\n";
			} else {
				echo "".$data[player_name2]."".$check_minute."<br>\n";
			}
		}
	}
	mysqli_free_result($get_substitutions);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_goal_assists_long."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>".$mdata['goal_assists_opponent']."</td>\n";
	echo "<td align='right' valign='top' width='50%'>";
	$get_goal_assists = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPublish AS publish,
		GA.GoalAssistMinute AS goal_assist_minute
		FROM team_players P, team_goal_assists GA
		WHERE GA.GoalAssistMatchID = '$id'
		AND P.PlayerID = GA.GoalAssistPlayerID
		ORDER BY goal_assist_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_goal_assists) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_goal_assists)) {
			if ($data['goal_assist_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['goal_assist_minute'].")";
			}
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>".$check_minute."<br>\n";
			} else {
				echo "".$data['player_name']."".$check_minute."<br>\n";
			}
		}
	}
	mysqli_free_result($get_goal_assists);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_yellow_cards_long."</b></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>".$mdata['yellow_cards_opponent']."</td>\n";
	echo "<td align='right' valign='top' width='50%'>";
	$get_yellows = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPublish AS publish,
		Y.YellowCardMinute AS yellow_card_minute
		FROM team_players P, team_yellow_cards Y
		WHERE Y.YellowCardMatchID = '$id'
		AND P.PlayerID = Y.YellowCardPlayerID
		ORDER BY yellow_card_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_yellows) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_yellows)) {
			if ($data['yellow_card_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['yellow_card_minute'].")";
			}
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>".$check_minute."<br>\n";
			} else {
				echo "".$data['player_name']."".$check_minute."<br>\n";
			}
		}
	}
	mysqli_free_result($get_yellows);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_red_cards_long."</b>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' width='50%'>".$mdata['red_cards_opponent']."</td>\n";
	echo "<td align='right' valign='top' width='50%'>";
	$get_reds = mysqli_query($db_connect, "SELECT
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerID AS player_id,
		P.PlayerPublish AS publish,
		R.RedCardMinute AS red_card_minute
		FROM team_players P, team_red_cards R
		WHERE R.RedCardMatchID = '$id'
		AND P.PlayerID = R.RedCardPlayerID
		ORDER BY red_card_minute
	") or die(mysqli_error());

	if (mysqli_num_rows($get_reds) == 0) {
		echo "".$locale_none."";
	} else {
		while ($data = mysqli_fetch_array($get_reds)) {
			if ($data['red_card_minute'] == 0) {
				$check_minute = '';
			} else {
				$check_minute = " (".$data['red_card_minute'].")";
			}
			if ($data['publish'] == 1) {
				echo "<a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a>".$check_minute."<br>\n";
			} else {
				echo "".$data['player_name']."".$check_minute."<br>\n";
			}
		}
	}
	mysqli_free_result($get_reds);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	if ($mdata['publish_optional'] == 1) {
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
		echo "<td align='left' valign='middle'><b>".$locale_other_statistics."</b></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['shots_opponent']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_shots."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['shots']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['shots_on_goal_opponent']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_shots_on_goal."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['shots_on_goal']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['offsides_opponent']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_offsides."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['offsides']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['corners_opponent']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_corners."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['corners']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['freekicks_opponent']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_freekicks."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['freekicks']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
		echo "<tr align='left'>\n";
		echo "<td align='left' valign='middle' width='10%'>".$mdata['penalties_opponent']."</td>\n";
		echo "<td align='center' valign='middle' width='80%'>".$locale_penalties."</td>\n";
		echo "<td align='right' valign='middle' width='10%'>".$mdata['penalties']."</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
}
$show_pictures = 0;
$query = mysqli_query($db_connect, "SELECT
	PictureID AS picture_id,
	PictureName AS picture_name,
	PictureText AS picture_text
	FROM team_picture_gallery
	WHERE PictureMatchID = '$id'
	LIMIT 1
") or die(mysqli_error());

if (mysqli_num_rows($query) > 0) {
	$show_pictures = 1;
	$picture_data = mysqli_fetch_array($query);
}
mysqli_free_result($query);

if ($mdata['match_report'] != '') {
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle'><b>".$locale_report."</b>";

	if (SHOW_COMMENTS == 1) {
		echo " | <a href='comments.php?id=".$id."'>".$locale_fan_comments."</a>";
	}
	if ($mdata['preview_text'] != '') {
		echo " | <a href='preview.php?id=".$id."'>".$locale_preview."</a>";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top'>\n";

	if ($show_pictures == 1) {
		echo "<table width='10%' align='right' cellspacing='3' cellpadding='0' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='center' valign='top'>";
		echo "<img src='images/".$picture_data['picture_name']."' alt=''><br>";
		echo "<small>".$picture_data['picture_text']."<br><a href='picture_gallery.php?id=".$mdata['match_id']."'>".$locale_match_pictures."</a></small>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
	echo "".$mdata['match_report']."";
	echo "</td>\n";
	echo "</tr>\n";
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