<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
	$season = explode("___",$_POST['season_select']);
	$_SESSION['season_id'] = $season[0];
	$_SESSION['season_name'] = $season[1];
	header("Location: ".$HTTP_REFERER."");
}
?>