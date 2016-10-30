<?php
$PHP_SELF = $_SERVER['PHP_SELF'];

if (isset($_POST['submit'])) { $submit = $_POST['submit']; }

if (isset($submit)) {
	include('user.php');
	$db_connect = mysqli_connect("$db_host","$db_user","$db_password","$db_name") or die(mysqli_error($db_connect));

	$db_username = trim($_POST['username']);
	$db_password = trim($_POST['password']);
	$db_password2 = trim($_POST['password2']);
	$season_name = trim($_POST['season_name']);
	$team_name = trim($_POST['team_name']);
	
	if ($db_username == '' || $db_password == '' || $db_password2 == '' || $season_name == '' || $team_name == '') {
		echo "Fill All Fields.";
		exit();
	}
	if (!get_magic_quotes_gpc()) {
		$team_name = addslashes($team_name);
	}
	if ($db_password != "$db_password2") {
		echo "You didn't retype correctly.";
		exit();
	}
	if (strlen($db_password) < 6) {
		echo "Your password must be at least six characters long.";
		exit();
	}
	mysqli_query($db_connect, "CREATE TABLE `team_appearances` (
		`AppearanceID` int(10) unsigned NOT NULL auto_increment,
		`AppearancePlayerID` int(10) unsigned NOT NULL default '0',
		`AppearanceMatchID` int(10) unsigned NOT NULL default '0',
		`AppearanceSeasonID` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`AppearanceID`),
		KEY `AppearancePlayerID` (`AppearancePlayerID`),
		KEY `AppearanceMatchID` (`AppearanceMatchID`),
		KEY `AppearanceSeasonID` (`AppearanceSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_comments` (
		`ID` int(11) NOT NULL auto_increment,
		`MatchID` int(11) NOT NULL default '0',
		`Name` varchar(128) NOT NULL default '',
		`Comments` text NOT NULL,
		`Time` datetime NOT NULL default '2000-01-01 00:00:00',
		`IP` varchar(64) NOT NULL default '',
		PRIMARY KEY  (`ID`),
		KEY `MatchID` (`MatchID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_goals` (
		`GoalID` int(10) unsigned NOT NULL auto_increment,
		`GoalPlayerID` int(10) unsigned NOT NULL default '0',
		`GoalMatchID` int(10) unsigned NOT NULL default '0',
		`GoalSeasonID` int(10) unsigned NOT NULL default '0',
		`GoalMinute` tinyint(3) unsigned NOT NULL default '0',
		`GoalOwn` tinyint(1) unsigned NOT NULL default '0',
		`GoalPenalty` tinyint(1) unsigned NOT NULL default '0',
		`GoalOwnScorer` varchar(64) NOT NULL default '',
		PRIMARY KEY  (`GoalID`),
		KEY `GoalPlayerID` (`GoalPlayerID`),
		KEY `GoalMatchID` (`GoalMatchID`),
		KEY `GoalSeasonID` (`GoalSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));
	
	mysqli_query($db_connect, "CREATE TABLE `team_goal_assists` (
		`GoalAssistID` int(10) unsigned NOT NULL auto_increment,
		`GoalAssistPlayerID` int(10) unsigned NOT NULL default '0',
		`GoalAssistMatchID` int(10) unsigned NOT NULL default '0',
		`GoalAssistSeasonID` int(10) unsigned NOT NULL default '0',
		`GoalAssistMinute` tinyint(3) unsigned NOT NULL default '0',
		PRIMARY KEY  (`GoalAssistID`),
		KEY `GoalAssistPlayerID` (`GoalAssistPlayerID`),
		KEY `GoalAssistMatchID` (`GoalAssistMatchID`),
		KEY `GoalAssistSeasonID` (`GoalAssistSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_injured` (
		`InjuredReason` varchar(128) NOT NULL default '',
		`InjuredPlayerID` int(10) unsigned NOT NULL default '0',
		`InjuredMatchID` int(10) unsigned NOT NULL default '0',
		`InjuredSeasonID` int(10) unsigned NOT NULL default '0',
		KEY `InjuredPlayerID` (`InjuredPlayerID`),
		KEY `InjuredMatchID` (`InjuredMatchID`),
		KEY `InjuredSeasonID` (`InjuredSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));
	
	mysqli_query($db_connect, "CREATE TABLE `team_managers` (
		`ManagerID` int(10) NOT NULL auto_increment,
		`ManagerFirstName` varchar(128) NOT NULL default '',
		`ManagerLastName` varchar(255) NOT NULL default '',
		`ManagerPublish` tinyint(1) NOT NULL default '0',
		`ManagerDOB` date NOT NULL default '2000-01-01',
		`ManagerPOB` varchar(255) NOT NULL default '',
		`ManagerPC` varchar(255) NOT NULL default '',
		`ManagerProfile` text NOT NULL,
		`ManagerPlayerID` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`ManagerID`),
		KEY `ManagerPlayerID` (`ManagerPlayerID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_managers_time` (
		`ID` int(10) NOT NULL auto_increment,
		`ManagerID` int(10) unsigned NOT NULL,
		`StartDate` date NOT NULL,
		`EndDate` date NOT NULL,
		PRIMARY KEY  (`ID`),
		KEY `ManagerID` (`ManagerID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_matches` (
		`MatchID` int(10) unsigned NOT NULL auto_increment,
		`MatchTypeID` int(10) unsigned NOT NULL default '0',
		`MatchAdditionalType` varchar(64) NOT NULL default '',
		`MatchSeasonID` int(10) unsigned NOT NULL default '0',
		`MatchDateTime` datetime NOT NULL default '2000-01-01 00:00:00',
		`MatchOpponent` smallint(4) NOT NULL default '0',
		`MatchPlaceID` tinyint(1) unsigned NOT NULL default '0',
		`MatchNeutral` tinyint(1) unsigned NOT NULL default '0',
		`MatchStadium` varchar(128) NOT NULL default '',
		`MatchAttendance` mediumint(6) unsigned default '0',
		`MatchReferee` varchar(64) NOT NULL default '',
		`MatchReport` text NOT NULL,
		`MatchPublish` tinyint(1) unsigned NOT NULL default '1',
		`MatchPublishOptional` tinyint(1) unsigned NOT NULL default '0',
		`MatchOvertime` tinyint(1) unsigned NOT NULL default '0',
		`MatchPenaltyShootout` tinyint(1) unsigned NOT NULL default '0',
		`MatchGoals` tinyint(2) default NULL,
		`MatchGoalsOpponent` tinyint(2) default NULL,
		`MatchPenaltyGoals` tinyint(2) unsigned default NULL,
		`MatchPenaltyGoalsOpponent` tinyint(2) unsigned default NULL,
		`MatchShots` tinyint(2) unsigned NOT NULL default '0',
		`MatchShotsOpponent` tinyint(2) unsigned NOT NULL default '0',
		`MatchShotsOnGoal` tinyint(2) unsigned NOT NULL default '0',
		`MatchShotsOnGoalOpponent` tinyint(2) unsigned NOT NULL default '0',
		`MatchOffsides` tinyint(2) unsigned NOT NULL default '0',
		`MatchOffsidesOpponent` tinyint(2) unsigned NOT NULL default '0',
		`MatchCorners` tinyint(2) unsigned NOT NULL default '0',
		`MatchCornersOpponent` tinyint(2) unsigned NOT NULL default '0',
		`MatchFreekicks` tinyint(2) unsigned NOT NULL default '0',
		`MatchFreekicksOpponent` tinyint(2) unsigned NOT NULL default '0',
		`MatchPenalties` tinyint(2) unsigned NOT NULL default '0',
		`MatchPenaltiesOpponent` tinyint(2) unsigned NOT NULL default '0',
		`MatchOpeningOpponent` text NULL,
		`MatchSubstitutesOpponent` text NULL,
		`MatchSubstitutionsOpponent` text NULL,
		`MatchGoalscorersOpponent` text NULL,
		`MatchGoalAssistsOpponent` text NULL,
		`MatchYellowCardsOpponent` text NULL,
		`MatchRedCardsOpponent` text NULL,
		PRIMARY KEY  (`MatchID`),
		KEY `MatchTypeID` (`MatchTypeID`),
		KEY `MatchSeasonID` (`MatchSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_match_places` (
		`MatchPlaceID` tinyint(1) unsigned NOT NULL default '0',
		`MatchPlaceName` varchar(4) NOT NULL default '',
		PRIMARY KEY  (`MatchPlaceID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_match_types` (
		`MatchTypeID` int(10) unsigned NOT NULL auto_increment,
		`MatchTypeName` varchar(64) NOT NULL default '',
		PRIMARY KEY  (`MatchTypeID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_news` (
		`news_id` mediumint(6) unsigned NOT NULL auto_increment,
		`news_date` datetime NOT NULL default '2000-01-01 00:00:00',
		`news_subject` varchar(255) NOT NULL default '',
		`news_content` text NOT NULL,
		`news_picture_text` varchar(255) NOT NULL,
		PRIMARY KEY  (`news_id`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_opponents` (
		`OpponentID` smallint(4) unsigned NOT NULL auto_increment,
		`OpponentName` varchar(128) NOT NULL default '',
		`OpponentWWW` varchar(128) NOT NULL default '',
		`OpponentInfo` text NULL,
		`OpponentAllData` tinyint(1) NOT NULL default '0',
		PRIMARY KEY  (`OpponentID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_pages` (
		`page_id` int(10) NOT NULL auto_increment,
		`page_title` varchar(255) NOT NULL,
		`page_content` text NOT NULL,
		`publish` tinyint(1) NOT NULL,
		PRIMARY KEY  (`page_id`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_passwords` (
		`PasswordID` int(10) unsigned NOT NULL default '0',
		`PasswordUser` varchar(16) NOT NULL default '',
		`PasswordPassword` varchar(32) NOT NULL default '',
		PRIMARY KEY  (`PasswordID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_picture_gallery` (
		`PictureID` int(10) unsigned NOT NULL auto_increment,
		`PictureName` varchar(64) NOT NULL default '',
		`PictureMatchID` int(10) unsigned NOT NULL default '0',
		`PictureText` varchar(255) NOT NULL default '',
		`PictureNumber` int(10) unsigned NOT NULL default '1',
		PRIMARY KEY  (`PictureID`),
		KEY `PictureMatchID` (`PictureMatchID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_player_positions` (
		`PlayerPositionID` tinyint(1) unsigned NOT NULL default '0',
		`PlayerPositionName` varchar(64) NOT NULL default '',
		PRIMARY KEY  (`PlayerPositionID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_players` (
		`PlayerID` int(10) unsigned NOT NULL auto_increment,
		`PlayerFirstName` varchar(32) NOT NULL default '',
		`PlayerLastName` varchar(64) NOT NULL default '',
		`PlayerSeasonID` int(11) unsigned NOT NULL default '0',
		`PlayerPositionID` tinyint(1) unsigned NOT NULL default '0',
		`PlayerNumber` tinyint(3) unsigned NOT NULL default '0',
		`PlayerDescription` text NOT NULL,
		`PlayerPublish` tinyint(1) unsigned NOT NULL default '1',
		`PlayerShowStats` tinyint(1) NOT NULL default '1',
		`PlayerAllData` tinyint(1) NOT NULL default '1',
		`PlayerDOB` date NOT NULL default '2000-01-01',
		`PlayerPOB` varchar(64) NOT NULL default '',
		`PlayerHeight` varchar(32) NOT NULL default '',
		`PlayerWeight` varchar(32) NOT NULL default '',
		`PlayerPC` text NOT NULL,
		`PlayerInSquadList` tinyint(1) NOT NULL default '1',
		PRIMARY KEY  (`PlayerID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_players_opponent` (
		`PlayerID` int(10) unsigned NOT NULL default '0',
		`OpponentID` smallint(4) unsigned NOT NULL default '0',
		KEY `PlayerID` (`PlayerID`),
		KEY `OpponentID` (`OpponentID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_preferences` (
		`id` tinyint(1) NOT NULL auto_increment,
		`site_title` varchar(255) NOT NULL,
		`team_name` varchar(255) NOT NULL,
		`bgcolor` varchar(32) NOT NULL,
		`bgcolor1` varchar(32) NOT NULL,
		`bgcolor2` varchar(32) NOT NULL,
		`cellbgcolortop` varchar(32) NOT NULL,
		`cellbgcolorbottom` varchar(32) NOT NULL,
		`bordercolor` varchar(32) NOT NULL,
		`show_staff` tinyint(1) NOT NULL,
		`show_comments` tinyint(1) NOT NULL,
		`default_season` int(10) NOT NULL,
		`default_match_type` int(10) NOT NULL,
		`print_date` tinyint(1) NOT NULL,
		`default_language` tinyint(3) NOT NULL,
		`accept_multi_language` tinyint(1) NOT NULL,
		`contact` text NOT NULL,
		`show_latest_match` tinyint(1) NOT NULL default '1',
		`show_featured_player` tinyint(1) NOT NULL default '1',
		`show_search` tinyint(1) NOT NULL default '1',
		`show_next_match` tinyint(1) NOT NULL default '1',
		`show_top_scorers` tinyint(1) NOT NULL default '1',
		`show_top_apps` tinyint(1) NOT NULL default '1',
		`show_top_assists` tinyint(1) NOT NULL default '1',
		`show_top_bookings` tinyint(1) NOT NULL default '1',
		`show_contact` tinyint(1) NOT NULL default '1',
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_previews` (
		`PreviewID` int(10) unsigned NOT NULL auto_increment,
		`PreviewMatchID` int(11) NOT NULL default '0',
		`PreviewText` text NOT NULL,
		`PreviewTickets` text NOT NULL,
		`PreviewTV` text NOT NULL,
		`PreviewTextUnder` text NOT NULL,
		PRIMARY KEY  (`PreviewID`),
		KEY `PreviewMatchID` (`PreviewMatchID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_red_cards` (
		`RedCardID` int(10) unsigned NOT NULL auto_increment,
		`RedCardPlayerID` int(10) unsigned NOT NULL default '0',
		`RedCardMatchID` int(10) unsigned NOT NULL default '0',
		`RedCardSeasonID` int(10) unsigned NOT NULL default '0',
		`RedCardMinute` tinyint(3) unsigned NOT NULL default '0',
		PRIMARY KEY  (`RedCardID`),
		KEY `RedCardPlayerID` (`RedCardPlayerID`),
		KEY `RedCardMatchID` (`RedCardMatchID`),
		KEY `RedCardSeasonID` (`RedCardSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_season_names` (
		`SeasonID` int(10) unsigned NOT NULL auto_increment,
		`SeasonName` varchar(64) NOT NULL default '',
		`SeasonPublish` tinyint(1) unsigned NOT NULL default '1',
		PRIMARY KEY  (`SeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_seasons` (
		`SeasonID` int(10) unsigned NOT NULL default '0',
		`SeasonPlayerID` int(10) unsigned NOT NULL default '0',
		KEY `SeasonID` (`SeasonID`),
		KEY `SeasonPlayerID` (`SeasonPlayerID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_substitutes` (
		`SubstituteID` int(10) unsigned NOT NULL auto_increment,
		`SubstitutePlayerID` int(10) unsigned NOT NULL default '0',
		`SubstituteMatchID` int(10) unsigned NOT NULL default '0',
		`SubstituteSeasonID` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`SubstituteID`),
		KEY `SubstitutePlayerID` (`SubstitutePlayerID`),
		KEY `SubstituteMatchID` (`SubstituteMatchID`),
		KEY `SubstituteSeasonID` (`SubstituteSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_substitutions` (
		`SubstitutionID` int(10) unsigned NOT NULL auto_increment,
		`SubstitutionPlayerIDIn` int(10) unsigned NOT NULL default '0',
		`SubstitutionPlayerIDOut` int(10) unsigned NOT NULL default '0',
		`SubstitutionMatchID` int(10) unsigned NOT NULL default '0',
		`SubstitutionSeasonID` int(10) unsigned NOT NULL default '0',
		`SubstitutionMinute` tinyint(3) unsigned NOT NULL default '0',
		PRIMARY KEY  (`SubstitutionID`),
		KEY `SubstitutionPlayerIDIn` (`SubstitutionPlayerIDIn`),
		KEY `SubstitutionMatchID` (`SubstitutionMatchID`),
		KEY `SubstitutionSeasonID` (`SubstitutionSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_suspended` (
		`SuspendedReason` varchar(128) NOT NULL default '',
		`SuspendedPlayerID` int(10) unsigned NOT NULL default '0',
		`SuspendedMatchID` int(10) unsigned NOT NULL default '0',
		`SuspendedSeasonID` int(10) unsigned NOT NULL default '0',
		KEY `SuspendedPlayerID` (`SuspendedPlayerID`),
		KEY `SuspendedMatchID` (`SuspendedMatchID`),
		KEY `SuspendedSeasonID` (`SuspendedSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_transferred` (
		`TransferredReason` varchar(255) NOT NULL default '',
		`TransferredPlayerID` int(10) unsigned NOT NULL default '0',
		KEY `TransferredPlayerID` (`TransferredPlayerID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_transfers` (
		`ID` int(10) NOT NULL auto_increment,
		`SeasonID` int(10) NOT NULL,
		`PlayerID` int(10) NOT NULL,
		`ClubID` int(10) NOT NULL,
		`Value` varchar(255) NOT NULL,
		`InOrOut` tinyint(1) NOT NULL,
		PRIMARY KEY  (`ID`),
		KEY `SeasonID` (`SeasonID`,`PlayerID`,`ClubID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "CREATE TABLE `team_yellow_cards` (
		`YellowCardID` int(10) unsigned NOT NULL auto_increment,
		`YellowCardPlayerID` int(10) unsigned NOT NULL default '0',
		`YellowCardMatchID` int(10) unsigned NOT NULL default '0',
		`YellowCardSeasonID` int(10) unsigned NOT NULL default '0',
		`YellowCardMinute` tinyint(3) unsigned NOT NULL default '0',
		PRIMARY KEY  (`YellowCardID`),
		KEY `YellowCardPlayerID` (`YellowCardPlayerID`),
		KEY `YellowCardMatchID` (`YellowCardMatchID`),
		KEY `YellowCardSeasonID` (`YellowCardSeasonID`)
		) ENGINE=MyISAM
	") or die(mysqli_error($db_connect));

	mysqli_query($db_connect, "INSERT INTO team_passwords (PasswordID, PasswordUser, PasswordPassword) VALUES ('1','$db_username',MD5('$db_password'))") or die(mysqli_error($db_connect));
	mysqli_query($db_connect, "INSERT INTO team_match_places (MatchPlaceID, MatchPlaceName) VALUES ('1','Home'),('2','Away')") or die(mysqli_error($db_connect));
	mysqli_query($db_connect, "INSERT INTO team_preferences (id, site_title, team_name, bgcolor, bgcolor1, bgcolor2, cellbgcolortop, cellbgcolorbottom, bordercolor, show_staff, show_comments, default_season, default_match_type, print_date, default_language, accept_multi_language, contact, show_latest_match, show_featured_player, show_search, show_next_match, show_top_scorers, show_top_apps, show_top_assists, show_top_bookings, show_contact)
		VALUES ('1', '$team_name', '$team_name','B9D3EE', 'B9D3EE', 'A0B6CD', 'D2D7DD', 'DEE3E7', '000000', '1', '1', '1', '1', '1', '1', '1', 'contact info', '1', '1', '1', '1', '1', '1', '1', '1', '1')
	") or die(mysqli_error($db_connect));
	mysqli_query($db_connect, "INSERT INTO team_season_names (SeasonID, SeasonName, SeasonPublish) VALUES ('1', '$season_name', '1')") or die(mysqli_error($db_connect));
	mysqli_query($db_connect, "INSERT INTO `team_player_positions` (`PlayerPositionID`, `PlayerPositionName`) VALUES ('1', 'Goalkeeper')") or die(mysqli_error($db_connect));
	mysqli_query($db_connect, "INSERT INTO `team_player_positions` (`PlayerPositionID`, `PlayerPositionName`) VALUES ('2', 'Defender')") or die(mysqli_error($db_connect));
	mysqli_query($db_connect, "INSERT INTO `team_player_positions` (`PlayerPositionID`, `PlayerPositionName`) VALUES ('3', 'Midfielder')") or die(mysqli_error($db_connect));
	mysqli_query($db_connect, "INSERT INTO `team_player_positions` (`PlayerPositionID`, `PlayerPositionName`) VALUES ('4', 'Forward')") or die(mysqli_error($db_connect));
	mysqli_query($db_connect, "INSERT INTO team_opponents SET OpponentName = 'N/A'") or die(mysqli_error($db_connect));
	mysqli_query($db_connect, "INSERT INTO team_match_types SET MatchTypeName = 'N/A'") or die(mysqli_error($db_connect));

	echo "Installation is now Complete.<br><br>Your User Name: ".$db_username."<br>Password: ".$db_password."<br><br>Before heading to Login In Page, Remove installer.php from your Server.<br><br><a href='index.php'>Log In</a>";
	exit();

} else {
	echo "<!DOCTYPE html>\n";
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Install FootballStats</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='../css/admin.css'>\n";
	echo "</head>\n";
	echo "<body>\n";
	echo "<form action='".$PHP_SELF."' method='post'>\n";
	echo "<table align='center' width='600'><tr>\n";
	echo "<td align='left' valign='middle' colspan='2'><h1>Install FootballStats</h1></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='middle' colspan='2'>Before entering data into the form, please check that user.php is correctly modified.</td>\n";
	echo "</tr><tr>\n";
	echo "<td valign='middle' align='left'>User Name to Admin Area:</td>\n";
	echo "<td valign='middle' align='left'><input name='username' type='text' size='20'></td>\n";
	echo "</tr><tr>\n";
	echo "<td valign='middle' align='left'>Desired Password into Admin Area:<br>(at least 6 characters)</td>\n";
	echo "<td valign='middle' align='left'><input name='password' type='password' size='20'></td>\n";
	echo "</tr><tr>\n";
	echo "<td valign='middle' align='left'>Retype Password:</td>\n";
	echo "<td valign='middle' align='left'><input name='password2' type='password' size='20'></td>\n";
	echo "</tr><tr>\n";
	echo "<td valign='middle' align='left'>First Modified Season Name:</td>\n";
	echo "<td valign='middle' align='left'><input name='season_name' type='text' size='20'></td>\n";
	echo "</tr><tr>\n";
	echo "<td valign='middle' align='left'>Team Name:</td>\n";
	echo "<td valign='middle' align='left'><input name='team_name' type='text' size='20'></td>\n";
	echo "</tr><tr>\n";
	echo "<td align='left' valign='middle' colspan='2'><input type='submit' name='submit' value='Install'></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	echo "</body>\n";
	echo "</html>\n";
}
?>