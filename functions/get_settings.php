<?php
session_start();
include("admin/user.php");

$db_connect = mysqli_connect($db_host, $db_user, $db_password, $db_name) or die(mysqli_error());
$get_preferences = mysqli_query($db_connect, "SELECT * FROM team_preferences WHERE id = '1' LIMIT 1") or die(mysqli_error());
$data = mysqli_fetch_array($get_preferences);

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
define("SHOW_TOP_ASSISTS", $data['show_top_assists']);
define("SHOW_TOP_BOOKINGS", $data['show_top_bookings']);
define("SHOW_CONTACT", $data['show_contact']);
mysqli_free_result($get_preferences);

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
?>