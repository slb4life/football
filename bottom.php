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
	$get_matches = mysqli_query($db_connect, "SELECT
		O.OpponentName AS opponent_name,
		O.OpponentID AS opponent_id,
		M.MatchID AS match_id,
		M.MatchPlaceID AS match_place_id,
		MT.MatchTypeName AS match_type_name,
		M.MatchAdditionalType AS match_additional_type,
		DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS match_date
		FROM (team_matches M, team_match_types MT, team_opponents O)
		WHERE M.MatchDateTime > CURRENT_TIMESTAMP
		AND MT.MatchTypeID = M.MatchTypeID
		AND O.OpponentID = M.MatchOpponent
		ORDER BY match_date
		LIMIT 1
	") or die(mysqli_error());		
	$team_name = TEAM_NAME;

	if (mysqli_num_rows($get_matches) == 0) {
		echo "<b>".$locale_season_ended."</b>";
	} else {
		while ($data = mysqli_fetch_array($get_matches)) {
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
			$query = mysqli_query($db_connect, "SELECT							
				P.PreviewText AS preview_text
				FROM team_previews P
				WHERE P.PreviewMatchID = '$id'
				LIMIT 1
			") or die(mysqli_error());
			$data = mysqli_fetch_array($query);

			if ($data['preview_text'] != '') {
				echo "<a href='preview.php?id=".$data['match_id']."'>".$locale_preview."</a><br><br>\n";
			} else {
				echo "<br>";
			}									
			mysqli_free_result($query);

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
	mysqli_free_result($get_matches);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
}

if (SHOW_TOP_SCORERS == 1) {
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_top_scorers."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";

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
	$max_show = 5;
	$query = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		COUNT( G.GoalPlayerID ) AS goals
		FROM team_seasons S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID LIKE '$tdefault_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		LEFT OUTER JOIN team_goals G ON G.GoalPlayerID = S.SeasonPlayerID AND G.GoalMatchID = M.MatchID AND G.GoalOwn = '0'
		WHERE P.PlayerID != ''
		GROUP BY player_id
		ORDER BY goals DESC, player_name
		LIMIT $max_show
	") or die(mysqli_error());
	$i = 1;
	while ($data = mysqli_fetch_array($query)) {
		echo "<tr>\n";
		echo "<td align='right' valign='top'>".$i.".</td>\n";
		echo "<td align='left' valign='top' width='90%'><a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a></td>\n";
		echo "<td align='right' valign='top'>".$data['goals']."</td>\n";
		echo "</tr>\n";
		$i++;
	}
	mysqli_free_result($query);

	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

if (SHOW_TOP_APPS == 1) {
	echo "<br>\n";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_top_appearances."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";

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
	$max_show = 5;
	$query = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		COUNT( A.AppearancePlayerID ) AS appearance_player_id
		FROM team_seasons S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID LIKE '$tdefault_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		LEFT OUTER JOIN team_appearances A ON A.AppearancePlayerID = S.SeasonPlayerID AND A.AppearanceMatchID = M.MatchID
		WHERE P.PlayerID != ''
		GROUP BY player_id
		ORDER BY appearance_player_id DESC, player_name
		LIMIT $max_show
	") or die(mysqli_error());
	$i = 1;
	while ($data = mysqli_fetch_array($query)) {
		echo "<tr>\n";
		echo "<td align='right' valign='top'>".$i.".</td>\n";
		echo "<td align='left' valign='top' width='90%'><a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a></td>\n";
		echo "<td align='right' valign='top'>".$data['appearance_player_id']."</td>\n";
		echo "</tr>\n";
		$i++;
	}
	mysqli_free_result($query);

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
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";

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
	$max_show = 5;
	$query = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		COUNT( Y.YellowCardPlayerID ) AS yellows
		FROM team_seasons S
		LEFT OUTER JOIN team_players P ON P.PlayerID = S.SeasonPlayerID AND S.SeasonID LIKE '$tdefault_season_id'
		LEFT OUTER JOIN team_matches M ON M.MatchSeasonID = S.SeasonID AND M.MatchTypeID LIKE '$tdefault_match_type_id'
		LEFT OUTER JOIN team_yellow_cards Y ON Y.YellowCardPlayerID = S.SeasonPlayerID AND Y.YellowCardMatchID = M.MatchID
		WHERE P.PlayerID != ''
		GROUP BY player_id
		ORDER BY yellows DESC, player_name
		LIMIT $max_show
	") or die(mysqli_error());
	$i = 1;
	while ($data = mysqli_fetch_array($query)) {
		echo "<tr>\n";
		echo "<td align='right' valign='top'>".$i.".</td>\n";
		echo "<td align='left' valign='top' width='90%'><a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a></td>\n";
		echo "<td align='right' valign='top'>".$data['yellows']."</td>\n";
		echo "</tr>\n";
		$i++;
	}
	mysqli_free_result($query);

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