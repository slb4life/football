<?php
switch (PRINT_DATE) {
	case 1: {
		$how_to_print = "%d.%m.%Y";
		$how_to_print_in_player = "%d.%m.%Y";
		$how_to_print_in_manager = "%d.%m.%Y";
		$how_to_print_in_report = "%d.%m.%Y $locale_at %H:%i";
	}
	break;
	case 2: {
		$how_to_print = "%m.%d.%Y";
		$how_to_print_in_player = "%m.%d.%Y";
		$how_to_print_in_manager = "%m.%d.%Y";
		$how_to_print_in_report = "%m.%d.%Y $locale_at %H:%i";
	}
	break;
	case 3: {
		$how_to_print = "%b %D %Y";
		$how_to_print_in_player = "%b %D %Y";
		$how_to_print_in_manager = "%b %D %Y";
		$how_to_print_in_report = "%b %D %Y $locale_at %H:%i";
	}
	break;
}
?>