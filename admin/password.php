<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	include('user.php');
	$db_connect = mysqli_connect("$db_host","$db_user","$db_password","$db_name") or die(mysqli_error());

	$season_id = $_SESSION['season_id'];
	$season_name = $_SESSION['season_name'];
	
	$PHP_SELF = $_SERVER['PHP_SELF'];
	
	if (isset($_POST['submit'])){ $submit = $_POST['submit']; }

	if (isset($submit)) {
		$get_password = mysqli_query($db_connect, "SELECT * FROM team_passwords WHERE PasswordID = '1'") or die(mysqli_error());
		$db_password = mysqli_fetch_array($get_password);
		$new = $_POST['new'];
		$new2 = $_POST['new2'];
		$old = $_POST['old'];
	
		if ($old == '' || $new == '' || $new2 == '') {
			$check = "You must fill all fields.";
		} else {
			$old = md5($old);
			if ($db_password['PasswordPassword'] != "$old") {
				$check = 'Your old password was wrong.';
			} else {
				if ($new != "$new2") {
					$check = "You didn't retype correctly.";
				} else {
					$new = md5($new);
					mysqli_query($db_connect, "UPDATE team_passwords SET PasswordPassword = '$new' WHERE PasswordID = '1'") or die(mysqli_error());
					$check = "Password changed succesfully!";
				}
			}
		}
		mysqli_free_result($get_password);

	}
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Admin Area</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	include('menu.php');

	if (isset($check) && ($check!=null)) {
		echo "<center><font color='red'>".$check."</font></center>";
	}
	echo "<table align='center' width='600'><tr>\n";
	echo "<td>\n";
	echo "<form method='post' action='".$PHP_SELF."?session_id=".$session."'>\n";
	echo "<h1>Change password</h1>\n";
	echo "<table width='100%' cellspacing='3' cellpadding='3' border='0'><tr>\n";
	echo "<td align='left' valign='top'>Old Password:</td>\n";
	echo "<td align='left' valign='top'><input type='password' name='old'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>New Password:</td>\n";
	echo "<td align='left' valign='top'><input type='password' size='20' name='new'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='top'>Retype New Password:</td>\n";
	echo "<td align='left' valign='top'><input type='password' name='new2'></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<input type='submit' name='submit' value='Change Password'>\n";
	echo "</form>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	mysqli_close($db_connect);
}
?>