<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host", "$db_user", "$db_password", "$db_name") or die(mysqli_error());

	$season_id = $_SESSION['season_id'];
	$season_name = $_SESSION['season_name'];

	$PHP_SELF = $_SERVER['PHP_SELF'];
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
	
	if (isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
	if (isset($_POST['add_submit'])){ $add_submit = $_POST['add_submit']; }
	if (isset($_POST['modify_submit'])){ $modify_submit = $_POST['modify_submit']; }
	if (isset($_POST['delete_submit'])){ $delete_submit = $_POST['delete_submit']; }

	if (isset($add_submit)) {
		$page_title = trim($_POST['page_title']);
		$page_content = trim($_POST['page_content']);
		if (isset($_POST['publish'])){ $publish = $_POST['publish']; }

		if (!get_magic_quotes_gpc()) {
			$page_title = addslashes($page_title);
			$page_content = addslashes($page_content);
		}
		if (!isset($publish)){ $publish = 0; }

		if ($page_title != '') {
			mysqli_query($db_connect, "INSERT INTO team_pages SET PageTitle = '$page_title', PageContent = '$page_content', PagePublish = '$publish'") or die(mysqli_error());
			header("Location: $PHP_SELF?session_id=$session");
		}
	} else if (isset($modify_submit)) {
		$page_id = $_POST['page_id'];
		$page_title = trim($_POST['page_title']);
		$page_content = trim($_POST['page_content']);
		$publish = $_POST['publish'];

		if (!get_magic_quotes_gpc()) {
			$page_title = addslashes($page_title);
			$page_content = addslashes($page_content);
		}
		if (!isset($publish)){ $publish = 0; }

		if ($page_title != '') {
			mysqli_query($db_connect, "UPDATE team_pages SET PageTitle = '$page_title', PageContent = '$page_content', PagePublish = '$publish' WHERE PageID = '$page_id'") or die(mysqli_error());
		}
		header("Location: $HTTP_REFERER");

	} else if (isset($delete_submit)) {
		$page_id = $_POST['page_id'];
		mysqli_query($db_connect, "DELETE FROM team_pages WHERE PageID = '$page_id'") or die(mysqli_error());
		header("Location: $PHP_SELF?session_id=$session");
	}
	echo "<!DOCTYPE html>\n";
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');
	echo "<table align='center' width='800'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	
	if (!isset($action)) {
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Add Page</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>Page Title:</td>\n";
		echo "<td align='left' valign='top'><input type='text' name='page_title'></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Page Content:</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'><textarea name='page_content' cols='50' rows='30'></textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Published:</td>\n";
		echo "<td align='left' valign='top'><input type='checkbox' name='publish' value='1' CHECKED></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='add_submit' value='Add Page'>\n";
		echo "</form>\n";
	} else if ($action == 'modify') {
		$page_id = $_REQUEST['page_id'];
		$get_page = mysqli_query($db_connect,"SELECT PageID AS page_id, PageTitle AS page_title, PageContent AS page_content, PagePublish AS publish FROM team_pages WHERE PageID = '$page_id' LIMIT 1") or die(mysqli_error());
		$data = mysqli_fetch_array($get_page);
		echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
		echo "<h1>Modify / Delete Page</h1>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'>\n";
		echo "<tr>\n";
		echo "<td align='left' valign='top'>Page Title:</td>\n";
		echo "<td align='left' valign='top'>";
		echo "<input type='text' name='page_title' value='".$data['page_title']."'>";
		echo "<input type='hidden' name='page_id' value='".$data['page_id']."'>";
		echo "</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'>Page Content:</td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top' colspan='2'><textarea name='page_content' cols='50' rows='30'>".$data['page_content']."</textarea></td>\n";
		echo "</tr><tr>\n";
		echo "<td align='left' valign='top'>Published:</td>\n";
		echo "<td align='left' valign='top'>";

		if ($data['publish'] == 1) {
			echo "<input type='checkbox' name='publish' value='1' CHECKED>";
		} else {
			echo "<input type='checkbox' name='publish' value='1'>";
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<input type='submit' name='modify_submit' value='Modify Page'>\n";
		echo "<input type='submit' name='delete_submit' value='Delete Page'>\n";
		echo "</form>\n";
		mysqli_free_result($get_page);
	}
	echo "</td>\n";
	echo "<td align='left' valign='top'>";
	$get_pages = mysqli_query($db_connect, "SELECT PageID AS page_id, PageTitle AS page_title, PagePublish AS publish FROM team_pages ORDER BY page_title") or die(mysqli_error());

	if (mysqli_num_rows($get_pages) < 1) {
		echo "<b>No Pages So Far In Database</b>";
	} else {
		echo "<b>Pages So Far In Database:</b><br><br>";
		while($data = mysqli_fetch_array($get_pages)) {
			echo "<a href='".$PHP_SELF."?session_id=".$session."&amp;action=modify&amp;page_id=".$data['page_id']."'>".$data['page_title']."</a>";

			if ($data['publish'] == 0) {
				echo " (NB)<br>";
			} else {
				echo "<br>";
			}
		}
	}
	echo "<br>NB = This Page Is Not Published Yet.";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>