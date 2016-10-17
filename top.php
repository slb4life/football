<?php
session_start();

include("admin/user.php");
$db_connect = mysqli_connect($db_host, $db_user, $db_password,$db_name) or die(mysqli_error());
$query = mysqli_query($db_connect, "SELECT * FROM team_preferences WHERE id = '1' LIMIT 1") or die(mysqli_error());
$data = mysqli_fetch_array($query);

define("TEAM_NAME", $data['team_name']);
define("SITE_TITLE", $data['site_title']);
define("BGCOLOR", $data['bgcolor']);
define("BGCOLOR1", $data['bgcolor1']);
define("BGCOLOR2", $data['bgcolor2']);
define("CELLBGCOLORTOP", $data['cellbgcolortop']);
define("CELLBGCOLORBOTTOM", $data['cellbgcolorbottom']);
define("BORDERCOLOR", $data['bordercolor']);
define("SHOW_STAFF", $data['show_staff']);
define("SHOW_COMMENTS", $data['show_comments']);
define("DEFAULT_SEASON", $data['default_season']);
define("DEFAULT_MATCHTYPE", $data['default_match_type']);
define("PRINT_DATE", $data['print_date']);
define("DEFAULT_LANGUAGE", $data['default_language']);
define("ACCEPT_ML", $data['accept_multi_language']);
define("CONTACT_INFO", $data['contact']);
define("SHOW_LATEST_MATCH", $data['show_latest_match']);
define("SHOW_FEATURED_PLAYER", $data['show_featured_player']);
define("SHOW_SEARCH", $data['show_search']);
define("SHOW_NEXT_MATCH", $data['show_next_match']);
define("SHOW_TOP_SCORERS", $data['show_top_scorers']);
define("SHOW_TOP_APPS", $data['show_top_apps']);
define("SHOW_TOP_BOOKINGS", $data['show_top_bookings']);
define("SHOW_CONTACT", $data['show_contact']);
mysqli_free_result($query);

if (!isset($_SESSION['default_season_id_team']) || !isset($_SESSION['default_match_type_id_team']) || !isset($_SESSION['default_language_team'])) {
	$_SESSION['default_season_id_team'] = DEFAULT_SEASON;
	$_SESSION['default_match_type_id_team'] = DEFAULT_MATCHTYPE;
	$_SESSION['default_language_team'] = DEFAULT_LANGUAGE;
	$default_season_id = $_SESSION['default_season_id_team'];
	$default_match_type_id = $_SESSION['default_match_type_id_team'];
	$default_language = $_SESSION['default_language_team'];
} else {
	$default_season_id = $_SESSION['default_season_id_team'];
	$default_match_type_id = $_SESSION['default_match_type_id_team'];
	$default_language = $_SESSION['default_language_team'];
}

include('functions/get_language.php');
include('functions/get_seasons.php');
include('functions/get_types.php');

switch (PRINT_DATE) {
	case 1: {
		$how_to_print = "%d.%m.%Y";
		$how_to_print_in_report = "%d.%m.%Y $locale_at %H:%i";
	}
	break;

	case 2: {
		$how_to_print = "%m.%d.%Y";
		$how_to_print_in_report = "%m.%d.%Y $locale_at %H:%i";
	}
	break;

	case 3: {
		$how_to_print = "%b %D %Y";
		$how_to_print_in_report = "%b %D %Y $locale_at %H:%i";
	}
	break;
}

$get_seasons = mysqli_query($db_connect, "SELECT * FROM team_season_names WHERE SeasonPublish = '1' ORDER BY SeasonName DESC") or die(mysqli_error());
$get_types = mysqli_query($db_connect, "SELECT * FROM team_match_types ORDER BY MatchTypeName") or die(mysqli_error());

echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>\n";
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
echo "".GetTypes($db_connect, $default_match_type_id)."";
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
	$query = mysqli_query($db_connect, "SELECT
		M.MatchID AS match_id,
		M.MatchGoals AS goals,
		M.MatchGoalsOpponent AS goals_opponent,
		MT.MatchTypeName AS match_type,
		O.OpponentName AS opponent_name
		FROM team_matches M, team_opponents O, team_match_types MT
		WHERE M.MatchDateTime < CURRENT_TIMESTAMP
		AND M.MatchGoals IS NOT NULL
		AND M.MatchGoalsOpponent IS NOT NULL
		AND M.MatchTypeID = MT.MatchTypeID
		AND O.OpponentID = M.MatchOpponent
		ORDER BY M.MatchDateTime DESC
		LIMIT 1
	") or die(mysqli_error());
	$data = mysqli_fetch_array($query);
	mysqli_free_result($query);

	echo "".$locale_latest_match.": <a href='match_details.php?id=".$data['match_id']."'>VS. ".$data['opponent_name']."</a></td>\n";
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

$query = mysqli_query($db_connect, "SELECT page_id, page_title FROM team_pages WHERE publish = '1' ORDER BY page_title") or die(mysqli_error());

if (mysqli_num_rows($query) > 0) {
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
	while ($data = mysqli_fetch_array($query)) {
		echo "<a href='additional_page.php?id=".$data['page_id']."'>".$data['page_title']."</a><br>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}
mysqli_free_result($query);

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
	$query = mysqli_query($db_connect, "SELECT
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
	$data = mysqli_fetch_array($query);
	$small_text = substr($data['player_description'], 0, 150);
	mysqli_free_result($query);

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
?>