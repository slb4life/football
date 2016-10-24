<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session") {
	echo "Authorization Failed.<br> <a href='index.php'>Restart, Please</a>";
} else {
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
	$action = $_REQUEST['action'];
	$player_id = $_REQUEST['player_id'];
	$manager_id = $_REQUEST['manager_id'];
	$opponent_id = $_REQUEST['opponent_id'];
	$news_id = $_REQUEST['news_id'];
	$type = $_REQUEST['type'];

	switch($action) {
		case 1: {
			$path = "../images/".$player_id.".".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;

		case 2: {
			$path = "../images/".$player_id."_1.".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;

		case 3: {
			$path = "../images/".$player_id."_2.".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;

		case 4: {
			$path = "../images/header.".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;

		case 5: {
			$path = "../images/team_logo.".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;

		case 6: {
			$path = "../images/opponent_logo_".$opponent_id.".".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;

		case 7: {
			$path = "../images/manager".$manager_id.".".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;

		case 8: {
			$path = "../images/manager".$manager_id."_1.".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;

		case 9: {
			$path = "../images/manager".$manager_id."_2.".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;

		case 10: {
			$path = "../images/news_picture".$news_id.".".$type;
			unlink("$path");
			header("Location: $HTTP_REFERER");
		}
		break;
	}
}
?>
