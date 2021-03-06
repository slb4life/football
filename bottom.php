<?php
echo "</td>\n";
echo "<td align='left' valign='top' width='20%'>\n";

if (SHOW_NEXT_MATCH == 1) {
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_next_match."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>";
	$max_show = 1;
	$get_next_match = mysqli_query($db_connect, "SELECT
		O.OpponentName AS opponent_name,
		O.OpponentID AS opponent_id,
		M.MatchID AS match_id,
		M.MatchPlaceID AS match_place_id,
		MT.MatchTypeName AS match_type_name,
		M.MatchAdditionalType AS match_additional_type,
		DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date
		FROM team_matches AS M, team_match_types AS MT, team_opponents AS O
		WHERE M.MatchDateTime > CURRENT_TIMESTAMP
		AND MT.MatchTypeID = M.MatchTypeID
		AND O.OpponentID = M.MatchOpponent
		ORDER BY match_date
		LIMIT 1
	") or die(mysqli_error());

	if (mysqli_num_rows($get_next_match) == 0) {
		echo "".$locale_end_of_season."";
	} else {
		while($data = mysqli_fetch_array($get_next_match)) {
			$logos = 0;
			$image_url_1 = "images/team_logo.png";
			$image_url_2 = "images/team_logo.jpg";
			$image_url_3 = "images/opponent_logo_".$data['opponent_id'].".png";
			$image_url_4 = "images/opponent_logo_".$data['opponent_id'].".jpg";

			if ((file_exists($image_url_1) && file_exists($image_url_3)) || (file_exists($image_url_2) && file_exists($image_url_4)) || (file_exists($image_url_1) && file_exists($image_url_4)) || (file_exists($image_url_2) && file_exists($image_url_3))) {
				$logos = 1;
			}
			echo "".$data['match_date']."<br>".$data['match_type_name']."";

			if ($data['match_additional_type'] != '') {
				echo " / ".$data['match_additional_type']."";
			}
			echo "<br>\n";
			$get_preview = mysqli_query($db_connect, "SELECT							
				PreviewText AS preview_text
				FROM team_previews
				WHERE PreviewMatchID = '$data[match_id]'
				LIMIT 1
			") or die(mysqli_error());
			$data = mysqli_fetch_array($get_preview);

			if ($data['preview_text'] != '') {
				echo "<a href='preview.php?id=".$data['match_id']."'>".$locale_preview."</a><br><br>\n";
			} else {
				echo "<br>";
			}									
			mysqli_free_result($get_preview);

			if ($data['match_place_id'] == 1) {
				if ($logos == 1) {
					echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
					echo "<tr>\n";
					echo "<td align='center' align ='left' valign='middle' width='45%'>";

					if (file_exists($image_url_1)) {
						echo "<img src='".$image_url_1."'><br>".$team_name."</td>\n";
					} else {
						echo "<img src='".$image_url_2."'><br>".$team_name."</td>\n";
					}
					echo "<td align='center' valign='middle' width='10%'>Vs.</td>\n";
					echo "<td align='center' align ='right' valign='middle' width='45%'>";

					if (file_exists($image_url_3)) {
						echo "<img src='".$image_url_3."'><br>".$data['opponent_name']."</td>\n";
					} else {
						echo "<img src='".$image_url_4."'><br>".$data['opponent_name']."</td>\n";
					}
					echo "</tr>\n";
					echo "</table>\n";
				} else {
					echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
					echo "<tr>\n";
					echo "<td align='center' align ='left' valign='middle' width='45%'>".$team_name."</td>\n";
					echo "<td align='center' valign='middle' width='10%'>Vs.</td>\n";
					echo "<td align='center' align ='right' valign='middle' width='45%'>".$data['opponent_name']."</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
				}
			} else if ($data['match_place_id'] == 2) {
				if ($logos == 1) {
					echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
					echo "<tr>\n";
					echo "<td align='center' align ='left' valign='middle' width='45%'>";

					if (file_exists($image_url_3)) {
						echo "<img src='".$image_url_3."'><br>".$data['opponent_name']."</td>\n";
					} else {
						echo "<img src='".$image_url_4."'><br>".$data['opponent_name']."</td>\n";
					}
					echo "<td align='center' valign='middle' width='10%'>Vs.</td>\n";
					echo "<td align='center' align ='right' valign='middle' width='45%'>";

					if (file_exists($image_url_1)) {
						echo "<img src='".$image_url_1."'><br>".$team_name."</td>\n";
					} else {
						echo "<img src='".$image_url_2."'><br>".$team_name."</td>\n";
					}
					echo "</tr>\n";
					echo "</table>\n";
				} else {
					echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
					echo "<tr>\n";
					echo "<td align='center' align ='left' valign='middle' width='45%'>".$data['opponent_name']."</td>\n";
					echo "<td align='center' valign='middle' width='10%'>Vs.</td>\n";
					echo "<td align='center' align ='right' valign='middle' width='45%'>".$team_name."</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
				}
			}
		}
	}
	mysqli_free_result($get_next_match);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
}
if (SHOW_TOP_APPS == 1) {
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_top_appearances."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	echo "<table width='100%' cellspacing='0' cellpadding='1' border='0'>\n";
	$max_show = 5;
	$get_top_apps = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		COUNT( A.AppearancePlayerID ) AS appearance_player_id
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID LIKE '$tdefault_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		LEFT OUTER JOIN team_appearances A ON A.AppearancePlayerID = S.SeasonPlayerID AND A.AppearanceMatchID = M.MatchID
		WHERE P.PlayerID != ''
		GROUP BY player_id
		ORDER BY appearance_player_id DESC, player_name
		LIMIT $max_show
	") or die(mysqli_error());
	$i = 1;
	while($data = mysqli_fetch_array($get_top_apps)) {
		echo "<tr>\n";
		echo "<td align='right' valign='top'>".$i.".</td>\n";
		echo "<td align='left' valign='top' width='90%'><a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a></td>\n";
		echo "<td align='right' valign='top'>".$data['appearance_player_id']."</td>\n";
		echo "</tr>\n";
		$i++;
	}
	if ($i == 1) {
		echo "<tr>\n";
		echo "<td>".$locale_none."</td>\n";
		echo "</tr>\n";
	}
	mysqli_free_result($get_top_apps);

	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
if (SHOW_TOP_SCORERS == 1) {
	echo "<br>\n";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_top_scorers."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	echo "<table width='100%' cellspacing='0' cellpadding='1' border='0'>\n";
	$max_show = 5;
	$get_top_scorers = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		COUNT( G.GoalPlayerID ) AS goals
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID LIKE '$tdefault_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		LEFT OUTER JOIN team_goals G ON G.GoalPlayerID = S.SeasonPlayerID AND G.GoalMatchID = M.MatchID AND G.GoalOwn = '0'
		WHERE P.PlayerID != ''
		GROUP BY player_id
		ORDER BY goals DESC, player_name
		LIMIT $max_show
	") or die(mysqli_error());
	$i = 1;
	while($data = mysqli_fetch_array($get_top_scorers)) {
		echo "<tr>\n";
		echo "<td align='right' valign='top'>".$i.".</td>\n";
		echo "<td align='left' valign='top' width='90%'><a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a></td>\n";
		echo "<td align='right' valign='top'>".$data['goals']."</td>\n";
		echo "</tr>\n";
		$i++;
	}
	if ($i == 1) {
		echo "<tr>\n";
		echo "<td>".$locale_none."</td>\n";
		echo "</tr>\n";
	}
	mysqli_free_result($get_top_scorers);

	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
if (SHOW_TOP_ASSISTS == 1) {
	echo "<br>\n";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_top_assists."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	echo "<table width='100%' cellspacing='0' cellpadding='1' border='0'>\n";
	$max_show = 5;
	$get_top_assists = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		COUNT( GA.GoalAssistPlayerID ) AS goal_assists
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID LIKE '$tdefault_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		LEFT OUTER JOIN team_goal_assists GA ON GA.GoalAssistPlayerID = S.SeasonPlayerID AND GA.GoalAssistMatchID = M.MatchID
		WHERE P.PlayerID != ''
		GROUP BY player_id
		ORDER BY goal_assists DESC, player_name
		LIMIT $max_show
	") or die(mysqli_error());
	$i = 1;
	while($data = mysqli_fetch_array($get_top_assists)) {
		echo "<tr>\n";
		echo "<td align='right' valign='top'>".$i.".</td>\n";
		echo "<td align='left' valign='top' width='90%'><a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a></td>\n";
		echo "<td align='right' valign='top'>".$data['goal_assists']."</td>\n";
		echo "</tr>\n";
		$i++;
	}
	if ($i == 1) {
		echo "<tr>\n";
		echo "<td>".$locale_none."</td>\n";
		echo "</tr>\n";
	}
	mysqli_free_result($get_top_assists);

	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
if (SHOW_TOP_BOOKINGS == 1) {
	echo "<br>\n";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_top_bookings."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	echo "<table width='100%' cellspacing='0' cellpadding='1' border='0'>\n";
	$max_show = 5;
	$get_top_bookings = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		COUNT( YC.YellowCardPlayerID ) AS yellows
		FROM team_seasons AS S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID LIKE '$tdefault_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		LEFT OUTER JOIN team_yellow_cards YC ON YC.YellowCardPlayerID = S.SeasonPlayerID AND YC.YellowCardMatchID = M.MatchID
		WHERE P.PlayerID != ''
		GROUP BY player_id
		ORDER BY yellows DESC, player_name
		LIMIT $max_show
	") or die(mysqli_error());
	$i = 1;
	while($data = mysqli_fetch_array($get_top_bookings)) {
		echo "<tr>\n";
		echo "<td align='right' valign='top'>".$i.".</td>\n";
		echo "<td align='left' valign='top' width='90%'><a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a></td>\n";
		echo "<td align='right' valign='top'>".$data['yellows']."</td>\n";
		echo "</tr>\n";
		$i++;
	}
	if ($i == 1) {
		echo "<tr>\n";
		echo "<td>".$locale_none."</td>\n";
		echo "</tr>\n";
	}
	mysqli_free_result($get_top_bookings);

	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
if (SHOW_CONTACT == 1) {
	echo "<br>\n";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_contact."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' bgcolor='#".(CELLBGCOLORBOTTOM)."'>".(CONTACT_INFO)."</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</body>\n";
echo "</html>";
?>