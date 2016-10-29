<?php
function GetTypes($db_connect, $default_match_type_id) {
	$get_types = mysqli_query($db_connect, "SELECT * FROM team_match_types ORDER BY MatchTypeName") or die(mysqli_error());
	while($data = mysqli_fetch_array($get_types)) {
		if ($data['MatchTypeID'] == $default_match_type_id) {
			echo "<option value='".$data['MatchTypeID']."' selected>".$data['MatchTypeName']."</option>\n";
		} else {
			echo "<option value='".$data['MatchTypeID']."'>".$data['MatchTypeName']."</option>\n";
		}
	}
}
?>