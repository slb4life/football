<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization failed.<br><a href='index.php'>Restart, please</a>";
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host","$db_user","$db_password", "$db_name") or die(mysqli_error());

	$season_id = $_SESSION['season_id'];
	$season_name = $_SESSION['season_name'];

	$PHP_SELF = $_SERVER['PHP_SELF'];
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];

	if (isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
	if (isset($_POST['add_submit'])){ $add_submit = $_POST['add_submit']; }
	if (isset($_POST['modify_submit'])){ $modify_submit = $_POST['modify_submit']; }
	if (isset($_POST['delete_submit'])){ $delete_submit = $_POST['delete_submit']; }	

	if (isset($add_submit)) {
		$news_subject = trim($_POST['news_subject']);
		$news_content = trim($_POST['news_content']);
		$news_content = str_replace("\r\n", '<br>', $news_content);

		if (!get_magic_quotes_gpc()) {
			$news_subject = addslashes($news_subject);
			$news_content = addslashes($news_content);
		}

		if ($news_subject != '') {
			mysqli_query($db_connect, "INSERT INTO team_news SET news_subject = '$news_subject', news_content = '$news_content', news_date = CURRENT_TIMESTAMP()")or die(mysqli_error());
			header("Location: $PHP_SELF?session_id=$session");
		}
	} else if (isset($modify_submit)) {
		$news_subject = trim($_POST['news_subject']);
		$news_picture_text = trim($_POST['news_picture_text']);
		$news_content = trim($_POST['news_content']);
		$news_id = $_POST['news_id'];
		$news_content = str_replace("\r\n", '<br>', $news_content);

		if (!get_magic_quotes_gpc()) {
			$news_subject = addslashes($news_subject);
			$news_picture_text = addslashes($news_picture_text);
			$news_content = addslashes($news_content);
		}

		if ($news_subject != '') {
			mysqli_query($db_connect, "UPDATE team_news SET news_subject = '$news_subject', news_content = '$news_content', news_picture_text = '$news_picture_text', news_date = CURRENT_TIMESTAMP() WHERE news_id = '$news_id'") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");

	} else if (isset($delete_submit)) {
		$news_id = $_POST['news_id'];
		mysqli_query($db_connect, "DELETE FROM team_news WHERE news_id = '$news_id'") or die(mysqli_error());
		header("Location: $PHP_SELF?session_id=$session");
	}
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');
	echo "<table align='center' width='600'>\n";
	echo "<tr>\n";
	echo "<td>\n";

	if (!isset($action)) {
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Add News</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>News Subject:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='news_subject'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>News Content:<br><textarea name='news_content' cols='40' rows='10'></textarea></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='add_submit' value='Add News'>\n";
		echo "</form>\n";
	} else if ($action == 'modify') {
		$news_id = $_REQUEST['news_id'];
		$get_news = mysqli_query($db_connect, "SELECT * FROM team_news WHERE news_id = '$news_id' LIMIT 1") or die(mysqli_error());
		$data = mysqli_fetch_array($get_news);

		$data['news_content'] = str_replace('<br>', "\r\n", $data['news_content']);
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Modify / Delete News</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>News Subject:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='news_subject' value='".$data['news_subject']."'><input type='hidden' name='news_id' value='".$data['news_id']."'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>News Content:<br><textarea name='news_content' cols='40' rows='10'>".$data['news_content']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Picture Text:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='news_picture_text' value='".$data['news_picture_text']."'></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='modify_submit' value='Modify News'>\n";
		echo "<input type='submit' name='delete_submit' value='Delete News'>\n";
		echo "</form>\n";
		echo "<a href='".$PHP_SELF."?session_id=".$session."'>Add News</a>\n";
		mysqli_free_result($get_news);

		echo "<hr width='100%'>\n";
		echo "<form enctype='multipart/form-data' method='post' action='image_upload.php?session_id=".$session."'>\n";
		echo "<b>Upload Picture to this News</b><br>\n";
		echo "<input type='hidden' name='MAX_FILE_SIZE' value='8000000'>\n";
		echo "<input name='image_file' type='file'>\n";
		echo "<input name='action' type='hidden' value='10'>\n";
		echo "<input type='submit' name='submit' value='Upload'>\n";
		echo "<input type='hidden' name='news_id' value='".$news_id."'>\n";
		echo "</form>\n";
		$image_url = "../images/news_picture".$news_id.".jpg";
		$image_url2 = "../images/news_picture".$news_id.".png";

		if (file_exists($image_url)) {
			echo "<img src='".$image_url."' alt=''>";
			echo "<br><a href='delete_picture.php?session_id=".$session."&amp;news_id=".$news_id."&amp;action=10&amp;type=jpg'>Delete this Picture</a>";
		} else if (file_exists($image_url2)) {
			echo "<img src='".$image_url2."' alt=''>";
			echo "<br><a href='delete_picture.php?session_id=".$session."&amp;news_id=".$news_id."&amp;action=10&amp;type=png'>Delete this Picture</a>";
		} else {
				echo "<img src='../images/no_image.png' alt='' width='100' height='100'>";
		}
	}

	echo "</td>\n";
	echo "<td align='left' valign='top'>\n";
	$get_news = mysqli_query($db_connect, "SELECT * FROM team_news ORDER BY news_date DESC") or die(mysqli_error());
	
	if (mysqli_num_rows($get_news) < 1) {
		echo "<b>No News so far in Database</b>";
	} else {
		echo "<b>News so far in Database:</b><br><br>";
		while($data = mysqli_fetch_array($get_news)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;news_id=".$data['news_id']."'>".$data['news_subject']."</a><br>\n";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}