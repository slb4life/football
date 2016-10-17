<?php
include('top.php');
$script_name = "picture_gallery.php?".$_SERVER['QUERY_STRING'];

$team_name  = TEAM_NAME;
$id = mysqli_real_escape_string($db_connect, $_REQUEST['id']);

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
	M.MatchReferee AS referee,
	M.MatchAttendance AS att,
	M.MatchStadium AS stadium,
	M.MatchAdditionalType AS add_type,
	MT.MatchTypeName AS type_name,
	M.MatchPublishOptional AS publish_optional
	FROM team_matches M, team_match_types MT, team_opponents O
	WHERE M.MatchID = '$id' AND
	M.MatchTypeID = MT.MatchTypeID AND
	M.MatchOpponent = O.OpponentID
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
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<td align='left' valign='middle' width='50%'><b>".$mdata['time']." / ".$locale_stadium.": ".$mdata['stadium']."</b></td>\n";
echo "<td align='right' valign='middle' width='50%'><b>".$locale_attendance.":</b> ".$mdata['att']."</td>\n";
echo "</tr>\n";
echo "</table>\n";
if ($mdata['place'] == 1) {
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
	echo "<td align='center' valign='middle' width='10%' bgcolor='#".(BGCOLOR1)."'>";
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
		echo "<font class='bigname'>$mdata[opponent]</font>";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr bgcolor='#".(CELLBGCOLORTOP)."'>\n";
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
		echo "<font class='bigname'>".$mdata['opponent']."</font>";
	}
	echo "</td>\n";
	echo "<td align='center' valign='middle' width='10%' bgcolor='#".(BGCOLOR1)."'>\n";
	echo "<font class='bigname'>";
	if ($mdata['penalty_goals'] == NULL || $mdata['penalty_goals_opponent'] == NULL) {
		echo "".$mdata['goals_opponent']." - ".$mdata['goals']."";
		if ($mdata['overtime'] == 1) {
			echo " ".$locale_overtime_short."";
		}
	} else {
		echo "".$mdata['penalty_goals_opponent']." - ".$mdata['penalty_goals']." ".$locale_penalty_shootout_short."<br>(".$mdata['goals_opponent']." - ".$mdata['goals'].")<br>";
	}
	echo "</font>";
	echo "</td>\n";
	echo "<td align='right' valign='middle' width='45%'>";
	if ($logos == 1) {
		echo "<table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
		echo "<tr>\n";
		echo "<td width='95%' valign='middle' align='right'><font class='bigname'>".$team_name."</font></td>\n";
		echo "<td width='5%' valign='middle' align='center'>";
		if (file_exists($image_url_1)) {
			echo "<img src='$image_url_1' alt=''>";
		} else {
			echo "<img src='$image_url_2' alt=''>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	} else {
		echo "<font class='bigname'>".$team_name."</font>";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr bgcolor='#".(CELLBGCOLORTOP)."'>\n";
	echo "<td align='left' valign='middle' width='50%'><b>".$locale_referee.":</b> ".$mdata['referee']."</td>\n";
	echo "<td align='right' valign='middle' width='50%'>";
	if ($mdata['add_type'] == '') {
		echo "".$mdata['type_name']."-".$locale_match."";
	} else {
		echo "".$mdata['type_name']." / ".$mdata['add_type']."-".$locale_match."";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
}
$show_pictures = 0;
$query = mysqli_query($db_connect, "SELECT
	PictureName AS picture_name,
	PictureText AS picture_text
	FROM team_picture_gallery
	WHERE PictureMatchID = '$mdata[match_id]'
	ORDER BY PictureNumber
") or die(mysqli_error());

if (mysqli_num_rows($query) > 0) {
	$show_pictures = 1;
}
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr align='left' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<td><a href='match_details.php?id=".$id."'>".$locale_report."</a>";
if (SHOW_COMMENTS == 1) {
	echo " | <a href='comments.php?id=".$id."'>".$locale_fan_comments."</a>";
}
echo " | <b>".$locale_match_pictures."</b></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr bgcolor='#">(CELLBGCOLORBOTTOM)."'>\n";
echo "<td align='center' valign='top'>";
echo "<table width='60%' cellspacing='0' cellpadding='0' border='0' align='center'>\n";
echo "<tr bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<td align='center' valign='top'>";
if ($show_pictures == 1) {
	while($data = mysqli_fetch_array($query)) {
		echo "<img src='images/".$data['picture_name']."' alt=''><br>".$data['picture_text']."<br><br>";
	}
} else {
	echo "".$locale_no_pictures."";
}
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
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