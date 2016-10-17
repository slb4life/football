<?php
include('top.php');
$script_name = "record_book.php?".$_SERVER['QUERY_STRING'];

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
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."'><font class='bigname'><b>".$locale_record_book.": ".$data['team_name']."</b></font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle'>".$locale_match_type_filter.": \n";
echo "<select name='match_type_player'>\n";
echo "<option value='0'>".$locale_all."</option>\n";
while ($data = mysqli_fetch_array($get_types)) {
	if ($data['MatchTypeID'] == $default_match_type_id) {
		echo "<option value='".$data['MatchTypeID']."' selected>".$data['MatchTypeName']."</option>\n";
	} else {
		echo "<option value='".$data['MatchTypeID']."'>".$data['MatchTypeName']."</option>\n";
	}
}
mysqli_free_result($get_types);

echo "</select>\n";
echo "<input type='submit' name='submit2' value='".$locale_change."'>\n";
if ($default_match_type_id == 0) {
	$default_match_type_id = '%';
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_biggest_win."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	MAX( MatchGoals - MatchGoalsOpponent ) AS maxwin,
	MAX( MatchGoalsOpponent - MatchGoals ) AS maxloss,
	MAX( MatchGoals + MatchGoalsOpponent ) AS maxgoals
	FROM team_matches
	WHERE MatchGoals IS NOT NULL AND
	MatchTypeID LIKE '".$default_match_type_id."'
") or die(mysqli_error());
$data = mysqli_fetch_array($query);
$maxwin = $data['maxwin'];
$maxloss = $data['maxloss'];
$maxaggregate = $data['maxgoals'];
mysqli_free_result($query);

$query = mysqli_query($db_connect, "SELECT
	MAX( MatchGoals - MatchGoalsOpponent ) AS maxwin,
	MAX( MatchGoalsOpponent - MatchGoals ) AS maxloss
	FROM team_matches
	WHERE MatchGoals IS NOT NULL AND
	MatchPlaceID = '1' AND
	MatchNeutral = '0' AND
	MatchTypeID LIKE '".$default_match_type_id."'
") or die(mysqli_error());
$data = mysqli_fetch_array($query);
$maxhomewin = $data['maxwin'];
$maxhomeloss = $data['maxloss'];
mysqli_free_result($query);

$query = mysqli_query($db_connect, "SELECT
	MAX( MatchGoals - MatchGoalsOpponent ) AS maxwin,
	MAX( MatchGoalsOpponent - MatchGoals ) AS maxloss
	FROM team_matches
	WHERE MatchGoals IS NOT NULL AND
	MatchPlaceID = '2' AND
	MatchNeutral = '0' AND
	MatchTypeID LIKE '".$default_match_type_id."'
") or die(mysqli_error());
$data = mysqli_fetch_array($query);
$maxawaywin = $data['maxwin'];
$maxawayloss = $data['maxloss'];
mysqli_free_result($query);

$query = mysqli_query($db_connect, "SELECT
	MAX( MatchGoals + MatchGoalsOpponent ) AS maxdraw
	FROM team_matches
	WHERE MatchGoals IS NOT NULL AND
	MatchGoals =  MatchGoalsOpponent AND
	MatchTypeID LIKE '".$default_match_type_id."'
") or die(mysqli_error());
$data = mysqli_fetch_array($query);
$maxdraw = $data['maxdraw'];
mysqli_free_result($query);

$query = mysqli_query($db_connect, "SELECT
	MAX( MatchAttendance ) AS maxatt,
	MIN( MatchAttendance ) AS minatt
	FROM team_matches
	WHERE MatchGoals IS NOT NULL AND
	MatchPlaceID = '1' AND
	MatchNeutral = '0' AND
	MatchTypeID LIKE '".$default_match_type_id."'
") or die(mysqli_error());
$data = mysqli_fetch_array($query);
$maxhomeatt = $data['maxatt'];
$minhomeatt = $data['minatt'];
mysqli_free_result($query);

$query = mysqli_query($db_connect, "SELECT
	MAX( MatchAttendance ) AS maxatt,
	MIN( MatchAttendance ) AS minatt
	FROM team_matches
	WHERE MatchGoals IS NOT NULL AND
	MatchPlaceID = '2' AND
	MatchNeutral = '0' AND
	MatchTypeID LIKE '".$default_match_type_id."'
") or die(mysqli_error());
$data = mysqli_fetch_array($query);
$maxawayatt = $data['maxatt'];
$minawayatt = $data['minatt'];
mysqli_free_result($query);

$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	(M.MatchGoals - M.MatchGoalsOpponent) = '".$maxwin."' AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchOpponent = O.OpponentID AND
	M.MatchGoals > M.MatchGoalsOpponent
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
		} else {
			echo "".$data['goals'] - $data['goals_opponent']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0){
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_biggest_defeat."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	(M.MatchGoalsOpponent - M.MatchGoals) = '".$maxloss."' AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchOpponent = O.OpponentID AND
	M.MatchGoals < M.MatchGoalsOpponent
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
		} else {
			echo "".$data['goals']." - ".$data['goals_opponent']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_most_goals_in_one_game."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchOpponent = O.OpponentID AND
	(M.MatchGoals + M.MatchGoalsOpponent) = '".$maxaggregate."'
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
		} else {
			echo "".$data['goals']." - ".$data['goals_opponent']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_biggest_home_win."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	(M.MatchGoals - M.MatchGoalsOpponent) = '".$maxhomewin."' AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchPlaceID = '1' AND M.MatchNeutral = '0' AND
	M.MatchOpponent = O.OpponentID AND
	M.MatchGoals > M.MatchGoalsOpponent
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
		} else {
			echo "".$data['goals']." - ".$data['goals_opponent']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_biggest_home_defeat."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	(M.MatchGoalsOpponent - M.MatchGoals) = '".$maxhomeloss."' AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchPlaceID = '1' AND M.MatchNeutral = '0' AND
	M.MatchOpponent = O.OpponentID AND
	M.MatchGoals < M.MatchGoalsOpponent
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
		} else {
			echo "".$data['goals']." - ".$data['goals_opponent']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_biggest_away_win."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	(M.MatchGoals - M.MatchGoalsOpponent) = '".$maxawaywin."' AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchPlaceID = '2' AND M.MatchNeutral = '0' AND
	M.MatchOpponent = O.OpponentID AND
	M.MatchGoals > M.MatchGoalsOpponent
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
		} else {
			echo "".$data['goals']." - ".$data['goals_opponent']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_biggest_away_defeat."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	(M.MatchGoalsOpponent - M.MatchGoals) = '".$maxawayloss."' AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchPlaceID = '2' AND M.MatchNeutral = '0' AND
	M.MatchOpponent = O.OpponentID AND
	M.MatchGoals < M.MatchGoalsOpponent
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
		} else {
			echo "".$data['goals']." - ".$data['goals_opponent']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_most_goals_in_tie."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	M.MatchGoals = M.MatchGoalsOpponent AND
	(M.MatchGoals + M.MatchGoalsOpponent) = '".$maxdraw."' AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchOpponent = O.OpponentID AND
	M.MatchGoals = M.MatchGoalsOpponent
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['goals']." - ".$data['goals_opponent']."</a>";
		} else {
			echo "".$data['goals']." - ".$data['goals_opponent']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_biggest_crowd_in_home."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchAttendance AS attendance,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchPlaceID = '1' AND
	M.MatchNeutral = '0' AND
	M.MatchAttendance != '' AND
	M.MatchAttendance = '".$maxhomeatt."' AND
	M.MatchOpponent = O.OpponentID
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['attendance']."</a>";
		} else {
			echo "".$data['attendance']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_biggest_crowd_in_away."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchAttendance AS attendance,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchPlaceID = '2' AND
	M.MatchNeutral = '0' AND
	M.MatchAttendance != '' AND
	M.MatchAttendance = '".$maxawayatt."' AND
	M.MatchOpponent = O.OpponentID
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['attendance']."</a>";
		} else {
			echo "".$data['attendance']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_lowest_crowd_in_home."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchAttendance AS attendance,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchPlaceID = '1' AND
	M.MatchNeutral = '0' AND
	M.MatchAttendance != '' AND
	M.MatchAttendance = '".$minhomeatt."' AND
	M.MatchOpponent = O.OpponentID
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['attendance']."</a>";
		} else {
			echo "".$data['attendance']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

echo "<tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORTOP)."' colspan='5'><b>".$locale_lowest_crowd_in_away."</b></td>\n";
echo "</tr>\n";
$query = mysqli_query($db_connect, "SELECT
	M.MatchID AS id,
	M.MatchAttendance AS attendance,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPublish AS publish,
	DATE_FORMAT(M.MatchDateTime, '".$how_to_print_in_report."') AS time,
	MT.MatchTypeName AS type,
	M.MatchAdditionalType AS add_type,
	O.OpponentName AS opponent,
	O.OpponentID AS oppid,
	M.MatchPlaceID AS place,
	M.MatchNeutral AS neutral
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchTypeID = MT.MatchTypeID AND
	M.MatchTypeID LIKE '".$default_match_type_id."' AND
	M.MatchPlaceID = '2' AND
	M.MatchNeutral = '0' AND
	M.MatchAttendance != '' AND
	M.MatchAttendance = '".$minawayatt."' AND
	M.MatchOpponent = O.OpponentID
	ORDER BY M.MatchDateTime DESC
") or die(mysqli_error());
$j = 1;
$diff = 1000000;
while ($data = mysqli_fetch_array($query)) {
	if ($j % 2 == 0) {
		$bg_color = BGCOLOR1;
	} else {
		$bg_color = BGCOLOR2;
	}
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='".$bg_color."'>".$data['time']."</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['add_type'] == '') {
		echo "".$data['type']."";
	} else {
		echo "".$data['type']." / ".$data['add_type']."";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['neutral'] == 1) {
		echo "".$locale_neutral_short."";
	} else {
		if ($data['place'] == 1) {
			echo "".$locale_home_short."";
		} else if ($data['place'] == 2) {
			echo "".$locale_away_short."";
		}
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'><a href='opponent.php?id=".$data['oppid']."'>".$data['opponent']."</a></td>\n";
	echo "<td align='center' valign='middle' bgcolor='".$bg_color."'>";
	if ($data['goals'] == NULL || $data['goals_opponent'] == NULL) {
		echo "&nbsp;";
	} else {
		if ($data['publish'] == 1) {
			echo "<a href='match_details.php?id=".$data['id']."'>".$data['attendance']."</a>";
		} else {
			echo "".$data['attendance']."";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	$j++;
}
if (mysqli_num_rows($query) == 0) {
	echo "<tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(BGCOLOR2)."' colspan='5'>".$locale_none."</td>\n";
	echo "</tr>\n";
}
mysqli_free_result($query);

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