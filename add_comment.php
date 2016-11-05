<?php
session_start();

if (isset($_POST['submit'])){ $submit = $_POST['submit']; }

if ($submit) {
	include('admin/user.php');
	$db_connect = mysqli_connect("$db_host", "$db_user", "$db_password", "$db_name") or die(mysqli_error());
	$comment_match_id = mysqli_real_escape_string($db_connect, trim($_POST['comment_match_id']));
	$comment_name = mysqli_real_escape_string($db_connect, trim($_POST['comment_name']));
	$comment_content = mysqli_real_escape_string($db_connect, str_replace("\r\n", '<br>', trim($_POST['comment_content'])));
	$comment_ip = $_SERVER['REMOTE_ADDR'];

	$HTTP_REFERER = $_POST['script_name'];

	if (!get_magic_quotes_gpc()) {
		$comment_name = addslashes($comment_name);
		$comment_content = addslashes($comment_content);
	}
	if ($_SESSION['comment_made'] == 1) {
		echo "You Have Already Commented...";
		exit();
	}
	if (!isset($HTTP_REFERER)) {
		header("Location: index.php");
		exit();
	}
	if ($comment_name == '' || $comment_content == '') {
		header("Location: $HTTP_REFERER");
		exit();
	} else {
		$_SESSION['comment_made'] = '1';
		mysqli_query($db_connect, "INSERT INTO team_comments SET
			CommentMatchID = '$comment_match_id',
			CommentName = '$comment_name',
			CommentContent = '$comment_content',
			CommentDateTime = NOW(),
			CommentIP = '$comment_ip'
		") or die(mysqli_error());
		header("Location: $HTTP_REFERER");
	}
}
?>