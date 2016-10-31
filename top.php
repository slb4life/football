<?php
include('functions/get_settings.php');
include('functions/get_date_time.php');
include('functions/get_language.php');
include('functions/get_seasons.php');
include('functions/get_match_types.php');

$get_seasons = mysqli_query($db_connect, "SELECT * FROM team_season_names WHERE SeasonPublish = '1' ORDER BY SeasonName DESC") or die(mysqli_error());
$get_match_types = mysqli_query($db_connect, "SELECT * FROM team_match_types ORDER BY MatchTypeName") or die(mysqli_error());
$team_name = TEAM_NAME;

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";
echo "<title>".(SITE_TITLE)."</title>\n";
echo "<link rel='stylesheet' type='text/css' href='css/style.css'>\n";
echo "</head>\n";
echo "<body bgcolor='".(BGCOLOR)."' leftmargin='0' topmargin='0' marginheight='0' marginwidth='0'>\n";
$image_header_url = "images/header.jpg";
$image_header_url2 = "images/header.png";

if (file_exists($image_header_url) || file_exists($image_header_url2)) {
	echo "<table width='100%' cellspacing='0' cellpadding='0' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='middle'>";

	if (file_exists($image_header_url)) {
		echo "<img src='".$image_header_url."' alt=''><br>";
	} else if (file_exists($image_header_url2)) {
		echo "<img src='".$image_header_url2."' alt=''><br>";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
echo "<form method='post' action='change.php'>\n";
echo "<input name='script_name' type='hidden' value='index.php'>\n";
echo "<table width='100%' cellspacing='2' cellpadding='0' border='0' bgcolor='#".(CELLBGCOLORTOP)."'>\n";
echo "<tr>\n";
echo "<td align='left' valign='middle'>\n";
echo "<table width='100%'>\n";
echo "<tr>\n";
echo "<td valign='middle' align='left'>";

if (ACCEPT_ML == 1) {
	echo "".$locale_language.": \n";
	echo "<select name='language'>\n";
	echo "".GetLanguages($default_language)."";
	echo "</select>\n";
}
echo "".$locale_change.": \n";
echo "<select name='season'>\n";
echo "<option value='0'>".$locale_all."</option>\n";
echo "".GetSeasons($db_connect, $default_season_id)."";
echo "</select>\n";
echo "<select name='match_type'>\n";
echo "<option value='0'>".$locale_all."</option>\n";
echo "".GetMatchTypes($db_connect, $default_match_type_id)."";
echo "</select>\n";
echo "<input type='submit' name='submit' value='Change'>\n";
echo "</td>\n";
echo "<td valign='middle' align='right'>".$locale_manual_text."</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>\n";
echo "<table width='100%' cellspacing='0' cellpadding='3' border='0'>\n";
echo "<tr>\n";
echo "<td align='left' valign='top' width='20%'>\n";

if (SHOW_LATEST_MATCH == 1) {
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_latest_match."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' bgcolor='#".(CELLBGCOLORBOTTOM)."'>";
	$get_lastest_match = mysqli_query($db_connect, "SELECT
		M.MatchID AS match_id,
		M.MatchGoals AS goals,
		M.MatchGoalsOpponent AS goals_opponent,
		M.MatchDateTime AS match_date,
		MT.MatchTypeName AS match_type,
		O.OpponentName AS opponent_name
		FROM team_matches AS M, team_opponents AS O, team_match_types AS MT
		WHERE M.MatchDateTime < CURRENT_TIMESTAMP
		AND M.MatchGoals IS NOT NULL
		AND M.MatchGoalsOpponent IS NOT NULL
		AND M.MatchTypeID = MT.MatchTypeID
		AND O.OpponentID = M.MatchOpponent
		ORDER BY match_date DESC
		LIMIT 1
	") or die(mysqli_error());

	if (mysqli_num_rows($get_lastest_match) == 0) {
		echo "".$locale_start_of_season."";
	} else {
		while($data = mysqli_fetch_array($get_lastest_match)) {
			echo "".$locale_latest_match.": <a href='match_details.php?id=".$data['match_id']."'>VS. ".$data['opponent_name']."</a>";
		}
	}
	mysqli_free_result($get_lastest_match);

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
}
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_quick_links."</i></b></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='top' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<a href='index.php'>".$locale_news."</a><br>\n";
echo "<a href='stats.php'>".$locale_player_stats."</a><br>\n";
echo "<a href='players.php'>".$locale_players."</a><br>\n";
echo "<a href='matches.php'>".$locale_matches."</a><br>\n";
echo "<a href='this_season.php'>".$locale_this_season."</a><br>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<br>\n";
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_special_links."</i></b></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td align='left' valign='top' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<a href='record_book.php'>".$locale_record_book."</a><br>\n";
echo "<a href='timeline.php'>".$locale_timeline."</a><br>\n";
echo "<a href='this_day.php'>".$locale_this_day."</a><br>\n";
echo "<a href='opponent_list.php'>".$locale_opponent_list."</a><br>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
$get_pages = mysqli_query($db_connect, "SELECT page_id, page_title FROM team_pages WHERE publish = '1' ORDER BY page_title") or die(mysqli_error());

if (mysqli_num_rows($get_pages) > 0) {
	echo "<br>\n";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_additional_pages."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	while($data = mysqli_fetch_array($get_pages)) {
		echo "<a href='additional_page.php?id=".$data['page_id']."'>".$data['page_title']."</a><br>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
mysqli_free_result($get_pages);

if (SHOW_FEATURED_PLAYER == 1) {
	echo "<br>\n";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_featured_player."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' bgcolor='#".(CELLBGCOLORBOTTOM)."'>";
	$get_featured_player = mysqli_query($db_connect, "SELECT
		P.PlayerID AS player_id,
		CONCAT(P.PlayerFirstName, ' ', P.PlayerLastName) AS player_name,
		P.PlayerDescription AS player_description,
		P.PlayerNumber AS player_number
		FROM team_players AS P
		WHERE P.PlayerDescription != ''
		AND P.PlayerPublish = '1'
		ORDER BY RAND()
		LIMIT 1
	") or die(mysqli_error());
	$data = mysqli_fetch_array($get_featured_player);
	$small_text = substr($data['player_description'], 0, 150);
	mysqli_free_result($get_featured_player);

	echo "<b><a href='player.php?id=".$data['player_id']."'>".$data['player_name']."</a></b><br>".$small_text."...</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
if (SHOW_SEARCH == 1) {
	echo "<br>\n";
	echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_search_top."</i></b></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' valign='top' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
	echo "<form method='post' action='search.php'>\n";
	echo "<input type='text' name='string'> \n";
	echo "<input type='submit' name='submit' value='".$locale_search."'>\n";
	echo "</form>\n";
	echo "".$locale_search_more."\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
echo "</td>\n";
echo "<td align='left' valign='top' width='60%'>\n";
?>>