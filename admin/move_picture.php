<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host", "$db_user", "$db_password", "$db_name") or die(mysqli_error());

	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
	$order = $_REQUEST['order'];
	$picture_id = $_REQUEST['picture_id'];
	$match_id = $_REQUEST['match_id'];
	$picture_number = $_REQUEST['picture_number'];
	
	if ($order == 1) {
		$picture_number_ = $picture_number - 1;
		$query = mysqli_query($db_connect, "SELECT PictureID FROM team_picture_gallery WHERE PictureMatchID = '$match_id' AND PictureNumber = '$picture_number_' LIMIT 1") or die(mysqli_error());
		$data = mysqli_fetch_array($query);
		$rows = mysqli_num_rows($query);
		$upper_id = $data['PictureID'];
		mysqli_free_result($query);

		$picture_number_ = $picture_number_ + 1;

		if ($rows > 0) {
			mysqli_query($db_connect, "UPDATE team_picture_gallery SET PictureNumber = '$picture_number_' WHERE PictureID = '$upper_id' LIMIT 1") or die(mysqli_error());
			mysqli_query($db_connect, "UPDATE team_picture_gallery SET PictureNumber = PictureNumber - 1 WHERE PictureID = '$picture_id' LIMIT 1") or die(mysqli_error());
		}
	} else if ($order == 0) {
		$picture_number_ = $picture_number + 1;
		$query = mysqli_query($db_connect, "SELECT PictureID FROM team_picture_gallery WHERE PictureMatchID = '$match_id' AND PictureNumber = '$picture_number_' LIMIT 1") or die(mysqli_error());
		$data = mysqli_fetch_array($query);
		$rows = mysqli_num_rows($query);
		$upper_id = $data['PictureID'];
		mysqli_free_result($query);

		$picture_number_ = $picture_number_ - 1;

		if ($rows > 0) {
			mysqli_query($db_connect, "UPDATE team_picture_gallery SET PictureNumber = '$picture_number_' WHERE PictureID = '$upper_id' LIMIT 1") or die(mysqli_error());
			mysqli_query($db_connect, "UPDATE team_picture_gallery SET PictureNumber = PictureNumber + 1 WHERE PictureID = '$picture_id' LIMIT 1") or die(mysqli_error());
		}
	}
	header("Location: $HTTP_REFERER");
}
?>
