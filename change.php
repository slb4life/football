<?php
session_start();

$HTTP_REFERER = $_POST['script_name'];

if (isset($_POST['submit']) || isset($_POST['submit2']) || isset($_POST['submit3'])) {
    $submit = $_POST['submit'];
    $submit2 = $_POST['submit2'];
    $submit3 = $_POST['submit3'];
}

if (isset($_POST['change_page']) || isset($_POST['change_opponent']) || isset($_POST['change_player']) || isset($_POST['change_manager'])) {
    $change_page = $_POST['change_page'];
    $change_opponent = $_POST['change_opponent'];
    $change_player = $_POST['change_player'];
    $change_manager = $_POST['change_manager'];
}

if (isset($submit)) {
	$season = $_POST['season'];
	$match_type = $_POST['match_type'];
	$language = $_POST['language'];

	if (!isset($language)) {
		$language = $_SESSION['default_language_team'];
	}
	$_SESSION['default_language_team'] = $language;
	$_SESSION['default_season_id_team'] = $season;
	$_SESSION['default_match_type_id_team'] = $match_type;
	header("Location: $HTTP_REFERER");

} else if (isset($submit2)) {
	$match_type_player = $_POST['match_type_player'];
	$_SESSION['default_match_type_id_team'] = $match_type_player;
	header("Location: $HTTP_REFERER");

} else if (isset($submit3)) {
	$match_type_manager = $_POST['match_type_manager'];
	$_SESSION['default_match_type_id_team'] = $match_type_manager;
	header("Location: $HTTP_REFERER");

} else if (isset($change_opponent)) {
	$id = $_POST['opponent_id'];
	header("Location: opponent.php?id=$id");

} else if (isset($change_player)) {
	$id = $_POST['player_id'];
	header("Location: player.php?id=$id");

} else if (isset($change_manager)) {
	$id = $_POST['manager_id'];
	header("Location: manager.php?id=$id");

} else {
	header("Location: $HTTP_REFERER");
}
?>