<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session") {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host", "$db_user", "$db_password", "$db_name") or die(mysqli_error());

	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
	$picture_name = $_REQUEST['picture_name'];
	$picture_id = $_REQUEST['picture_id'];
	$match_id = $_REQUEST['match_id'];
	$picture_number = $_REQUEST['picture_number'];

	unlink("../images/$picture_name");

	mysqli_query($db_connect, "DELETE FROM team_picture_gallery WHERE PictureID = '$picture_id' LIMIT 1") or die(mysqli_error());
	mysqli_query($db_connect, "UPDATE team_picture_gallery SET PictureNumber = PictureNumber - 1 WHERE PictureNumber > $picture_number AND PictureMatchID = '$match_id'") or die(mysqli_error());
	header("Location: $HTTP_REFERER");
}
?>
