<?php
session_start();

if (isset($_POST['submit'])){ $submit = $_POST['submit']; }

if ($submit) {
	include('admin/user.php');
	$db_connect = mysqli_connect($db_host, $db_user, $db_password, $db_name) or die(mysqli_error());
	$id = mysqli_real_escape_string($db_connect, trim($_POST['id']));
	$name = mysqli_real_escape_string($db_connect, trim($_POST['name']));
	$comments = mysqli_real_escape_string($db_connect, str_replace("\r\n", '<br>', trim($_POST['comments'])));
	$HTTP_REFERER = $_POST['script_name'];
	$IP = $_SERVER['REMOTE_ADDR'];
	if (!get_magic_quotes_gpc()) {
		$name = addslashes($name);
		$comments = addslashes($comments);
	}
	if ($_SESSION['comment_made'] == 1) {
		echo "You have already commented...";
		exit();
	}
	if (!isset($HTTP_REFERER)) {
		header("Location: index.php");
		exit();
	}
	if ($name == '' || $comments == '') {
		header("Location: ".$HTTP_REFERER."");
		exit();
	} else {
		$_SESSION['comment_made'] = '1';
		mysqli_query($db_connect, "INSERT INTO 
			team_comments SET
			MatchID = '".$id."',
			Name = '".$name."',
			Comments = '".$comments."',
			Time = NOW(),
			IP = '".$IP."'
		") or die(mysqli_error());
		header("Location: ".$HTTP_REFERER."");
	}
}
?>