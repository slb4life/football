<?php
include('top.php');
$script_name = "comments.php?".$_SERVER['QUERY_STRING'];

$team_name  = TEAM_NAME;

if (isset($_REQUEST['id'])){ $id = $_REQUEST['id']; }

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
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='5' border='0'>\n";
echo "<tr>\n";
echo "<td bgcolor='#".(CELLBGCOLORBOTTOM)."' align='center'>\n";
$get_details = mysqli_query($db_connect, "SELECT
	O.OpponentName AS opponent,
	O.OpponentID AS opponent_id,
	M.MatchID AS match_id,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPenaltyGoals AS penalty_goals,
	M.MatchPenaltyGoalsOpponent AS penalty_goals_opponent,
	M.MatchOvertime AS overtime,
	M.MatchPenaltyShootout AS penalty_shootout,
	DATE_FORMAT(M.MatchDateTime, '$how_to_print_in_report') AS time,
	M.MatchPlaceID AS place,
	M.MatchReport AS report,
	M.MatchReferee AS referee,
	M.MatchAttendance AS att,
	M.MatchStadium AS stadium,
	M.MatchReport AS report,
	M.MatchAdditionalType AS add_type,
	MT.MatchTypeName AS type_name,
	M.MatchPublishOptional AS publish_optional,
	P.PreviewText AS prewtext
	FROM (team_matches M, team_match_types MT, team_opponents O)
	LEFT OUTER JOIN team_previews P ON M.MatchID = P.PreviewMatchID
	WHERE M.MatchID = '$id'
	AND M.MatchTypeID = MT.MatchTypeID
	AND M.MatchOpponent = O.OpponentID
	LIMIT 1
")or die(mysqli_error());

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

if (isset($mdata['goal_scorers_opponent']) == '')
	$mdata['goal_scorers_opponent'] = "$locale_none";

if (isset($mdata['substitutions_opponent']) == '')
	$mdata['substitutions_opponent'] = "$locale_none";

if (isset($mdata['yellow_cards_opponent']) == '')
	$mdata['yellow_cards_opponent'] = "$locale_none";

if (isset($mdata['red_cards_opponent']) == '')
	$mdata['red_cards_opponent'] = "$locale_none";

echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr bgcolor='".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='middle' width='50%'><b>".$mdata['time']." / ".$locale_stadium.": ".$mdata['stadium']."</b></td>\n";
echo "<td align='right' valign='middle' width='50%'><b>".$locale_attendance.":</b> ".$mdata['att']."</td>\n";
echo "</tr>\n";
echo "</table>\n";

if ($mdata['place'] == 1) {
	echo "<table width='100%' align='center' cellspacing='2' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' width='45%'>";
	if ($logos == 1) {
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
		echo "<tr>\n";
		echo "<td width='5%' valign='middle' align='center'>";
		if (file_exists($image_url_1)) {
			echo "<img src='".$image_url_1."' alt=''>";
		}
		else {
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
	echo "<td align='center' valign='middle' width='10%' bgcolor='".(BGCOLOR1)."'>";
	echo "<font class='bigname'>";
	if ($mdata['penalty_goals'] == NULL || $mdata['penalty_goals_opponent'] == NULL) {
		echo "".$mdata['goals']." - ".$mdata['goals_opponent']."";
		if ($mdata['overtime'] == 1) {
			echo " ".$locale_overtime_short."";
		}
	} else {
		echo "".$mdata['goals']." - ".$mdata['goals_opponent']."<br>(".$mdata['penalty_goals']." - ".$mdata['penalty_goals_opponent']." ".$locale_penalty_shootout_short.")";
	}
	echo "</font>";
	echo "</td>\n";
	echo "<td align='right' valign='middle' width='45%'>";
	if ($logos == 1) {
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
		echo "<tr>\n";
		echo "<td width='95%' valign='middle' align='right'><font class='bigname'>$mdata[opponent]</font></td>\n";
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
		echo "<font class='bigname'>".$mdata['opponent']."</font>";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr bgcolor='".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle' width='50%'><b>".$locale_referee.":</b> ".$mdata['referee']."</td>\n";
	echo "<td align='right' valign='middle' width='50%'>";
	if ($mdata['add_type'] == '') {
		echo "$mdata[type_name]-$locale_match";
	} else {
		echo "$mdata[type_name] / $mdata[add_type]-$locale_match";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	echo "<br>\n";
	} else {
	echo "<table width='100%' align='center' cellspacing='2' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle' width='45%'>";
	if ($logos == 1) {
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
		echo "<tr>\n";
		echo "<td width='5%' valign='middle' align='center'>";
		if (file_exists($image_url_3)) {
			echo "<img src='$image_url_3' alt=''>";
		} else {
			echo "<img src='$image_url_4' alt=''>";
		}
		echo "</td>\n";
		echo "<td width='95%' valign='middle' align='left'><font class='bigname'>$mdata[opponent]</font></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} else {
		echo "<font class='bigname'>$mdata[opponent]</font>";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' width='10%' bgcolor='".(BGCOLOR1)."'>";
	echo "<font class='bigname'>";
	if ($mdata['penalty_goals'] == NULL || $mdata['penalty_goals_opponent'] == NULL) {
		echo "$mdata[goals_opponent] - $mdata[goals]";

		if ($mdata['overtime'] == 1) {
			echo" $locale_overtime_short";
		}
	} else {
		echo "$mdata[goals_opponent] - $mdata[goals]<br>($mdata[penalty_goals_opponent] - $mdata[penalty_goals] $locale_penalty_shootout_short)";
	}
	echo "</font>";
	echo "</td>\n";
	echo "<td align='right' valign='middle' width='45%'>";
	if ($logos == 1) {
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
		echo "<tr>\n";
		echo "<td width='95%' valign='middle' align='right'><font class='bigname'>".$team_name."</font></td>\n";
		echo "<td width='5%' valign='middle' align='center'>";
		if(file_exists($image_url_1)) {
			echo "<img src='$image_url_1' alt=''>";
		} else {
			echo "<img src='$image_url_2' alt=''>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>";
	} else {
		echo "<font class='bigname'>".$team_name."</font>";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr bgcolor='".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle' width='50%'><b>".$locale_referee.":</b> ".$mdata['referee']."</td>\n";
	echo "<td align='right' valign='middle' width='50%'>";
	if ($mdata['add_type'] == '') {
		echo "$mdata[type_name]-$locale_match";
	} else {
		echo "$mdata[type_name] / $mdata[add_type]-$locale_match";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	echo "<br>\n";
}
$show_pictures = 0;
$query = mysqli_query($db_connect, "SELECT
	PictureID AS id,
	PictureName AS picture_name,
	PictureText AS picture_text
	FROM team_picture_gallery
	WHERE PictureMatchID = '$mdata[match_id]'
	LIMIT 1
") or die(mysqli_error());

if (mysqli_num_rows($query) > 0) {
	$show_pictures = 1;
	$picture_data = mysqli_fetch_array($query);
}
mysqli_free_result($query);

echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr align='left' bgcolor='".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='middle'><a href='match_details.php?id=".$id."'>".$locale_report."</a> | <b>".$locale_fan_comments."</b>";
if ($show_pictures == 1) {
	echo" | <a href='picture_gallery.php?id=".$id."'>".$locale_match_pictures."</a>";
}
if ($mdata['prewtext'] != '') {
	echo " | <a href='preview.php?id=".$id."'>".$locale_preview."</a>";
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr bgcolor='".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='top'>\n";
echo "<form method='POST' action='add_comment.php'>\n";
echo "<input name='script_name' type='hidden' value='".$script_name."'>".$locale_name.":<br>\n";
echo "<input type='text' name='name' size='40'><br><br>".$locale_comment.":<br>\n";
echo "<textarea cols='40' rows='5' name='comments'></textarea><br><br>\n";
echo "<input type='submit' name='submit' value='".$locale_add_comment."'>\n";
echo "<input type='hidden' name='id' value='".$id."'><br><br>".$locale_name_and_comments_required."\n";
echo "</form>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr align='left' bgcolor='".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='middle'><b>".$locale_comments."</b></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr bgcolor='".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='top'>";
$query = mysqli_query($db_connect, "SELECT Name,
	Comments,
	DATE_FORMAT(Time, '$how_to_print_in_report') AS t
	FROM team_comments
	WHERE MatchID = '$id'
	ORDER BY Time DESC
") or die(mysqli_error());

if (mysqli_num_rows($query) == 0) {
	echo "".$locale_no_comments_yet."";
} else {
	while($data = mysqli_fetch_array($query)) {
		echo "<b>- ".$data['Name']." -</b> ".$data['t']."<br>".$data['Comments']."<br><br>";
	}
}
mysqli_free_result($query);

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

include('bottom.php');
?>