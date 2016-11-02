<?php
if (file_exists('installer.php')) {
	echo "Remove installer.php from your Server.";
	//exit();
}

include('user.php');
$db_connect = mysqli_connect("$db_host","$db_user","$db_password", "$db_name") or die(mysqli_error());

$PHP_SELF = $_SERVER['PHP_SELF'];

if (isset($_POST['submit'])){ $submit = $_POST['submit']; }

$kysely = mysqli_query($db_connect, "SELECT * FROM team_passwords WHERE PasswordID = '1'") or die(mysqli_error());
$data = mysqli_fetch_array($kysely);
$check = 0;

if (isset($submit)) {
	$db_user = $_POST['user'];
	$db_password = $_POST['password'];
	$season = $_POST['season'];
	if ($db_user == "".$data['PasswordUser']."" && md5($db_password) == "".$data['PasswordPassword']."") {
		session_start();
		unset($_SESSION['session']);
		srand((double)microtime()*1000000);
		$session = md5(rand(0,9999));
		$tmp = explode("___",$season);
		$_SESSION['season_id'] = $tmp[0];
		$_SESSION['season_name'] = $tmp[1];
		$_SESSION['session'] = $session;
		header("Location:players.php?session_id=$session");
	} else {
		$check = 1;
	}
}
echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";
echo "<title>Admin Area</title>\n";
echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
echo "</head>\n";
echo "<body>\n";

if ($check == 1) {
	echo "<font color='red'>WRONG PASSWORD OR USERNAME</font>";
}
echo "<h1>Log In</h1>\n";
echo "<form action='".$PHP_SELF."' method='post'>Username:<br>\n";
echo "<input type='text' name='user' size='20'><br><br>Password:<br>\n";
echo "<input type='password' name='password'><br><br>Select Season:<br>\n";
echo "<select name='season'>\n";
$get_seasons = mysqli_query($db_connect, "SELECT * FROM team_season_names ORDER BY SeasonName DESC") or die(mysqli_error());
while($sdata = mysqli_fetch_array($get_seasons)) {
	echo "<option value='".$sdata['SeasonID']."___".$sdata['SeasonName']."'>".$sdata['SeasonName']."</option>\n";
}
mysqli_free_result($get_seasons);

echo "</select>\n";
echo "<br><br><input type='submit' name='submit' value='Log in'>\n";
echo "</form>\n";
echo "</body>\n";
echo "</html>\n";