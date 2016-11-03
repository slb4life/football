<?php
echo "<center>";
echo "<a href='news.php?session_id=".$session."'>News</a> |\n";
echo "<a href='seasons.php?session_id=".$session."'>Seasons</a> |\n";
echo "<a href='match_types.php?session_id=".$session."'>Match Types</a> |\n";
echo "<a href='player_positions.php?session_id=".$session."'>Player Positions</a> |\n";
echo "<a href='opponents.php?session_id=".$session."'>Opponents</a> |\n";
echo "<a href='players.php?session_id=".$session."'>Players</a> |\n";
echo "<a href='managers.php?session_id=".$session."'>Managers</a> |\n";
echo "<a href='matches.php?session_id=".$session."'>Matches</a> |\n";
echo "<a href='previews.php?session_id=".$session."'>Previews</a> |\n";
echo "<a href='preferences.php?session_id=".$session."'>Preferences</a> |\n";
echo "<a href='password.php?session_id=".$session."'>Password</a> |\n";
echo "<a href='logout.php'>Log Out</a> |\n";
echo "<a href='../index.php'>Statistics</a><br>\n";
echo "<a href='injured.php?session_id=".$session."'>Injured</a> |\n";
echo "<a href='suspended.php?session_id=".$session."'>Suspended</a> |\n";
echo "<a href='transfers.php?session_id=".$session."'>Transfers</a> |\n";
echo "<a href='match_picture_upload.php?session_id=".$session."'>Upload Match Pictures</a> |\n";
echo "<a href='pages.php?session_id=".$session."'>Additional Pages</a> |\n";
echo "<a href='comments.php?session_id=".$session."'>Fan Comments</a>\n";
echo "<hr width='100%'>\n";

if (!isset($_SESSION['season_name']) || !isset($_SESSION['season_id'])) {
	echo "<form method='post' action='season_select.php?session_id=".$session."'>\n";
	echo "<b>Please Choose Season: </b>";
	echo "<select name='season_select'>\n";
	$get_seasons = mysqli_query($db_connect, "SELECT * FROM team_season_names ORDER BY SeasonName") or die(mysqli_error());
	while($sdata = mysqli_fetch_array($get_seasons)) {
		echo "<option value='".$sdata['SeasonID']."___".$sdata['SeasonName']."'>".$sdata['SeasonName']."</option>\n";
	}
	echo "</select>\n<input type='submit' name='submit' value='Go'></form>\n";
	mysqli_free_result($get_seasons);

} else {
	$season_name = $_SESSION['season_name'];
	echo "<form method='post' action='season_select.php?session_id=".$session."'>\n";
	echo "<b>Selected Season: ".$season_name."</b><br><br>";
	echo "You may change Season by selecting new Season from Dropdown Menu: ";
	echo "<select name='season_select'>\n";
	$get_seasons = mysqli_query($db_connect, "SELECT * FROM team_season_names ORDER BY SeasonName") or die(mysqli_error());
	while($sdata = mysqli_fetch_array($get_seasons)) {
		if ($sdata['SeasonID'] == $season_id) {
			echo "<option value='".$sdata['SeasonID']."___".$sdata['SeasonName']."' SELECTED>".$sdata['SeasonName']."</option>\n";
		} else {
			echo "<option value='".$sdata['SeasonID']."___".$sdata['SeasonName']."'>".$sdata['SeasonName']."</option>\n";
		}
	}
	echo "</select>\n";
	echo "<input type='submit' name='submit' value='Go'></form>\n";
	mysqli_free_result($get_seasons);

}
echo "<hr width='100%'>\n";
echo "</center>\n";
?>