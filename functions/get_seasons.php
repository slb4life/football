<?php
function GetSeasons($db_connect, $default_season_id) {
	$get_seasons = mysqli_query($db_connect, "SELECT * FROM team_season_names WHERE SeasonPublish = '1' ORDER BY SeasonName DESC") or die(mysqli_error());
	while($data = mysqli_fetch_array($get_seasons)) {
		if ($data['SeasonID'] == $default_season_id) {
			echo "<option value='".$data['SeasonID']."' selected>".$data['SeasonName']."</option>\n";
		} else {
			echo "<option value='".$data['SeasonID']."'>".$data['SeasonName']."</option>\n";
		}
	}
}
?>