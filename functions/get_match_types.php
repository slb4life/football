<?php
function GetMatchTypes($db_connect, $default_match_type_id) {
	$get_match_types = mysqli_query($db_connect, "SELECT * FROM team_match_types ORDER BY MatchTypeName") or die(mysqli_error());
	while($data = mysqli_fetch_array($get_match_types)) {
		if ($data['MatchTypeID'] == $default_match_type_id) {
			echo "<option value='".$data['MatchTypeID']."' SELECTED>".$data['MatchTypeName']."</option>\n";
		} else {
			echo "<option value='".$data['MatchTypeID']."'>".$data['MatchTypeName']."</option>\n";
		}
	}
}
?>