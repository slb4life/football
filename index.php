<?php
include('top.php');

echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_latest_match."</i></b></td>\n";
echo "</tr><tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>";
$get_latest_match = mysqli_query($db_connect, "SELECT
	O.OpponentName AS opponent_name,
	O.OpponentID AS opponent_id,
	M.MatchID AS match_id,
	M.MatchDateTime AS match_date,
	M.MatchGoals AS goals,
	M.MatchGoalsOpponent AS goals_opponent,
	M.MatchPenaltyGoals AS penalty_goals,
	M.MatchPenaltyGoalsOpponent AS penalty_goals_opponent,
	M.MatchOvertime AS match_overtime,
	M.MatchPenaltyShootout AS penalty_shootout,
	M.MatchPlaceID AS match_place_id,
	MT.MatchTypeName AS match_type_name
	FROM (team_matches AS M, team_match_types AS MT, team_opponents AS O)
	WHERE M.MatchDateTime < CURRENT_TIMESTAMP 
	AND M.MatchGoals IS NOT NULL
	AND M.MatchGoalsOpponent IS NOT NULL
	AND M.MatchTypeID = MT.MatchTypeID
	AND M.MatchOpponent = O.OpponentID
	ORDER BY match_date DESC
	LIMIT 1
") or die(mysqli_error());

if (mysqli_num_rows($get_latest_match) == 0) {
	echo "<b>".$locale_start_of_season."</b>";
} else {
	while($data = mysqli_fetch_array($get_latest_match)) {
		$logos = 0;
		$image_url_1 = "images/team_logo.png";
		$image_url_2 = "images/team_logo.jpg";
		$image_url_3 = "images/opponent_logo_".$data['opponent_id'].".png";
		$image_url_4 = "images/opponent_logo_".$data['opponent_id'].".jpg";

		if ((file_exists($image_url_1) && file_exists($image_url_3)) || (file_exists($image_url_2) && file_exists($image_url_4)) || (file_exists($image_url_1) && file_exists($image_url_4)) || (file_exists($image_url_2) && file_exists($image_url_3))) {
			$logos = 1;
		}
		if ($data['match_place_id'] == 1) {
			echo "<a href='match_details.php?id=".$data['match_id']."'>\n";
			echo "<table width='100%' align='center' cellspacing='0' cellpadding='0' border='0'>\n";
			echo "<tr>\n";
			echo "<td align='left' valign='middle' width='45%'>";

			if ($logos == 1) {
				echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n";
				echo "<tr>\n";
				echo "<td width='5%' valign='middle' align='center'>";

				if (file_exists($image_url_1)) {
					echo "<img src='".$image_url_1."' alt='' border='0'>";
				} else {
					echo "<img src='".$image_url_2."' alt='' border='0'>";
				}
				echo "</td>\n";
				echo "<td width='95%' valign='middle' align='left'><font class='bigname'>".$team_name."</font></td>\n";
				echo "</tr>\n";
				echo "</table>\n";
			} else {
				echo "<font class='bigname'>".$team_name."</font>\n";
			}
			echo "</td>\n";
			echo "<td align='center' valign='middle' width='10%'>";
			echo "<font class='bigname'>";

			if ($data['penalty_goals'] == NULL || $data['penalty_goals_opponent'] == NULL) {
				echo "".$data['goals']." - ".$data['goals_opponent']."";

				if ($data['match_overtime'] == 1) {
					echo "".$locale_overtime_short."";
				}
			} else {
				echo "".$data['goals']." - ".$data['goals_opponent']."<br>(".$data['penalty_goals']." - ".$data['penalty_goals_opponent']." ".$locale_penalty_shootout_short.")";
			}
			echo "</font>";
			echo "</td>\n";
			echo "<td align='right' valign='middle' width='45%'>\n";

			if ($logos == 1) {
				echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n";
				echo "<tr>\n";
				echo "<td width='95%' valign='middle' align='right'><font class='bigname'>".$data['opponent_name']."</font></td>\n";
				echo "<td width='5%' valign='middle' align='center'>";

				if (file_exists($image_url_3)) {
					echo "<img src='".$image_url_3."' alt='' border='0'>";
				} else {
					echo "<img src='".$image_url_4."' alt='' border='0'>";
				}
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
			} else {
				echo "<font class='bigname'>".$data['opponent_name']."</font>\n";
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</a>\n";
		} else {
			echo "<a href='match_details.php?id=".$data['match_id']."'>\n";
			echo "<table width='100%' align='center' cellspacing='0' cellpadding='0' border='0'>\n";
			echo "<tr>\n";
			echo "<td align='left' valign='middle' width='45%'>";

			if ($logos == 1) {
				echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n";
				echo "<tr>\n";
				echo "<td width='5%' valign='middle' align='center'>";

				if (file_exists($image_url_3)) {
					echo "<img src='".$image_url_3."' alt='' border='0'>";
				} else {
					echo "<img src='".$image_url_4."' alt='' border='0'>";
				}
				echo "</td>\n";
				echo "<td width='95%' valign='middle' align='left'><font class='bigname'>".$data['opponent_name']."</font></td>\n";
				echo "</tr>\n";
				echo "</table>\n";
			} else {
				echo "<font class='bigname'>".$data['opponent_name']."</font>";
			}
			echo "</td>\n";
			echo "<td align='center' valign='middle' width='10%'>";
			echo "<font class='bigname'>";

			if ($data['penalty_goals'] == NULL || $data['penalty_goals_opponent'] == NULL) {
				echo "".$data['goals_opponent']." - ".$data['goals']."";

				if ($data['match_overtime'] == 1) {
					echo "".$locale_overtime_short."";
				}
			} else {
				echo "".$data['goals_opponent']." - ".$data['goals']."<br>(".$data['penalty_goals_opponent']." - ".$data['penalty_goals']." ".$locale_penalty_shootout_short.")";
			}
			echo "</font>\n";
			echo "</td>\n";
			echo "<td align='right' valign='middle' width='45%'>\n";

			if ($logos == 1) {
				echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n";
				echo "<tr>\n";
				echo "<td width='95%' valign='middle' align='right'><font class='bigname'>".$team_name."</font></td>\n";
				echo "<td width='5%' valign='middle' align='center'>";

				if (file_exists($image_url_1)) {
					echo "<img src='".$image_url_1."' alt='' border='0'>";
				} else {
					echo "<img src='".$image_url_2."' alt='' border='0'>";
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
			echo "</a>\n";
		}
	}
}
mysqli_free_result($get_latest_match);

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

if (isset($_REQUEST['news_id'])){ $news_id = $_REQUEST['news_id']; }

if (!isset($news_id)) {
	echo "<br>";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_latest_news."</i></b></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	$get_news = mysqli_query($db_connect, "SELECT
		DATE_FORMAT(NewsDateTime, '$how_to_print') AS news_date,
		NewsID AS news_id,
		NewsSubject AS news_subject,
		NewsContent AS news_content
		FROM team_news
		ORDER BY news_date DESC, news_id DESC
		LIMIT 5
	") or die(mysqli_error());
	
	if (mysqli_num_rows($get_news) == 0) {
		echo "".$locale_no_news."";
	} else {
		$i = 0;
		while($data = mysqli_fetch_array($get_news)) {
			echo "<tr>\n";
			echo "<td align='right' valign='top'><i>".$data['news_date']."</i></td>\n";
			echo "<td align='left' valign='top' width='85%'><b><a href='index.php?news_id=".$data['news_id']."'>".$data['news_subject']."</a></b>";

			if ($i < 1) {
				$data['news_content'] = str_replace('\r\n', '<br>', $data['news_content']);
				echo "<br>\n".$data['news_content']."\n";
			}
			echo "</td>\n";
			echo "</tr>\n";
			$i++;
		}
	}
	mysqli_free_result($get_news);

	echo "<tr>\n";
	echo "<td align='left' valign='top'><br><a href='news_archive.php'>".$locale_news_archive."</a></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
} else {
	if ($news_id == '' || !is_numeric($news_id)) {
		$news_id = 1;
	}
	$get_news = mysqli_query($db_connect, "SELECT
		DATE_FORMAT(NewsDateTime, '$how_to_print') AS news_date,
		NewsID AS news_id,
		NewsSubject AS news_subject,
		NewsContent AS news_content,
		NewsPictureInfo AS news_picture_info
		FROM team_news
		WHERE NewsID = '$news_id'
		LIMIT 1
	") or die(mysqli_error());
	$data = mysqli_fetch_array($get_news);
	$data['news_content'] = str_replace('\r\n', '<br>', $data['news_content']);
	mysqli_free_result($get_news);

	echo "<br>";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_news."</i></b></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top'>\n";
	$image_url_1 = "images/news_picture".$data['news_id'].".jpg";
	$image_url_2 = "images/news_picture".$data['news_id'].".png";

	if (file_exists($image_url_1) || file_exists($image_url_2)) {
		if (file_exists($image_url)) {
			$image_url = $image_url_1;
		} else {
			$image_url = $image_url_2;
		}
		echo "<table width='10%' align='right' cellspacing='5' cellpadding='5' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='center' valign='top'><img src='".$image_url."' alt=''><br><small>".$data['news_picture_info']."</small></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
	echo "<i>".$data['news_date']."</i><br><br><b>".$data['news_subject']."</a></b><br>";
	echo "".$data['news_content']."";
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'><br><br><a href='news_archive.php'>".$locale_news_archive."</a></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
include('bottom.php');
?>