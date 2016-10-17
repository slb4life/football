<?php
switch ($default_language) {
	case 1:
	include('locale/english.php');
	break;
	case 2:
	include('locale/portuguese.php');
	break;
	default:
	include('locale/english.php');
	break;
}

function GetLanguages($default_language) {
	if ($default_language == 1) {
		echo "<option value='1' selected>English</option>\n";
	} else {
		echo "<option value='1'>English</option>\n";
	}
	if ($default_language == 2) {
		echo "<option value='2' selected>Portuguese</option>\n";
	} else {
		echo "<option value='2'>Portuguese</option>\n";
	}
}
?>