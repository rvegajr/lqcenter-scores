<?
  require("config.php");
  require("functions.php");

  if($_POST['submit'] && isset($_COOKIE[lqc_scores_session]))
    {
	if($_POST['do'] == "team_pts")
	    {
		$eventId = $_POST['eventId'];
		$gameId = $_POST['gameId'];
		$game_type = $_POST['game_type'];

		print_header("Admin","Schedule");
		verify_session();

		$team_pts = $_POST['team_pts'];

		foreach($team_pts as $column=>$column_data )
		    {
			if($column_data != "")
			    {
				mysql_query("UPDATE `web_games` SET $column = '$column_data' WHERE id='$gameId' AND event='$eventId'") or die(mysql_error());
			     }
		     }

		echo "<p align=\"center\"><b>Team Points Updated</b></p>\n\n";
		echo "<p align=\"center\">Redirecting...</p>\n\n";
		echo "<p align=\"center\"><a href=\"./schedule.php?eventId=$eventId&game_type=$game_type\">Click here to continue</a></p>\n\n";
		echo "<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./rank_team_scores.php?eventId=$eventId&game_type=$game_type&onlyPts=true\">\n\n";

		print_footer();
	     }
	elseif($_POST['do'] == "game_time")
	    {
		$eventId = $_POST['eventId'];
		$game_type = $_POST['game_type'];
		$gameId = $_POST['gameId'];

		print_header("Admin","Schedule");
		verify_session();

		$game_time_month = $_POST['game_time_month'];
		$game_time_day = $_POST['game_time_day'];
		$game_time_year = $_POST['game_time_year'];
		$game_time_hh = $_POST['game_time_hh'];
		$game_time_mm = $_POST['game_time_mm'];

		$timestamp = mktime($game_time_hh,$game_time_mm,0,$game_time_month,$game_time_day,$game_time_year);

		mysql_query("UPDATE `web_games` SET time='$timestamp' WHERE id='$gameId' LIMIT 1");

		echo "<p align=\"center\"><b>Game Time Updated</b></p>\n\n";
		echo "<p align=\"center\">Redirecting...</p>\n\n";
		echo "<p align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getType=$game_type\">Click here to continue</a></p>\n\n";
		echo "<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./schedule.php?getEvent=$eventId&getType=$game_type\">\n\n";

		print_footer();
	     }
	elseif($_POST['do'] == "add_game")
	    {
		$eventId = $_POST['eventId'];
		$game_type = $_POST['game_type'];

		$event_pack_sets = fetch_pack_sets($eventId,$game_type);

		print_header("Admin","Schedule");
		verify_session();

		$team = $_POST['team'];

		$game_time_month = $_POST['game_time_month'];
		$game_time_day = $_POST['game_time_day'];
		$game_time_year = $_POST['game_time_year'];
		$game_time_hh = $_POST['game_time_hh'];
		$game_time_mm = $_POST['game_time_mm'];

		$game_time = mktime($game_time_hh,$game_time_mm,0,$game_time_month,$game_time_day,$game_time_year);

		$add_game_query = "INSERT INTO `web_games` (`event`, `time`, `type`";

		for($colsA = 1; $colsA < $event_pack_sets+1; $colsA++)
		   {
			$col_name = "team" . $colsA;

			$add_game_query .= ", `$col_name`";
		    }

		$add_game_query .= ") VALUES ('$eventId', '$game_time', '$game_type'";

		for($colsB = 1; $colsB < $event_pack_sets+1; $colsB++)
		   {
			$col_name = "team" . $colsB;

			$add_game_query .= ", '$_POST[$col_name]'";
		    }

		$add_game_query .= ")";

		//print($add_game_query);
		$insert_game = mysql_query($add_game_query) or die(mysql_error());

		if(!$insert_game)
		    {
			echo "<p align=\"center\"><b>ERROR: Failed to add game</b></p>\n\n";
			echo "<p align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getType=$game_type\">Click here to continue</a></p>\n\n";
		     }
		else
		    {
			echo "<p align=\"center\"><b>Game Added</b></p>\n\n";
			echo "<p align=\"center\">Redirecting...</p>\n\n";
			echo "<p align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getType=$game_type\">Click here to continue</a></p>\n\n";
			echo "<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./schedule.php?getEvent=$eventId&getType=$game_type\">\n\n";
		     }

		print_footer();
	     }
     }

  elseif($_GET["eventId"] != "" && $_GET["generate"] == "playoff_schedule")
    {
	print("Function not available");
     }

  //===========================================================================
  //Generate Schedule
  //===========================================================================

  elseif($_GET["eventId"] != "" && $_GET["game_type"] != "" && $_GET["generate"] == "schedule")
      {
	if(isset($_COOKIE[lqc_scores_session]))
	    {
		verify_session();
	     }

	$eventId = $_GET["eventId"];
	$game_type = $_GET["game_type"];

	$fetch_event = mysql_query("SELECT * FROM `web_events` WHERE id='$eventId' LIMIT 1");
	$eventRow = mysql_fetch_array($fetch_event);

	if($game_type == "pre")
	    {
		$event_pack_sets = $eventRow[prelim_sets];
		$table_title = "Prelim";
	     }
	elseif($game_type == "pla")
	    {
		$event_pack_sets = $eventRow[playoff_sets];
		$table_title = "Playoff";
	     }
	elseif($game_type == "con")
	    {
		$event_pack_sets = $eventRow[console_sets];
		$table_title = "Consoles";
	     }
	elseif($game_type == "fin")
	    {
		$event_pack_sets = $eventRow[final_sets];
		$table_title = "Finals";
	     }

	print_header($eventId,"Generate $table_title Schedule");

	if($event_pack_sets > 3) $set_multiple = 1;
	else $set_multiple = 2;

	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY RAND()");

	while($row = mysql_fetch_assoc($fetch_teams))
	    {
		$team_id = $row["team_id"];
		$team_name = $row["team_name"];

		$teams[] = $team_id;
		$team_names[$team_id] = $team_name;
	     }

	$team_count = count($teams);

	$game_count = $team_count * $set_multiple;

	$games_per_team = $event_pack_sets * $set_multiple;

	print("<p align=\"center\">[ Packsets: <b>$event_pack_sets</b> - Teams: <b>$team_count</b> - Games Per Team: <b>$games_per_team</b> - Total Games: <b>$game_count</b> ]</p>\n\n");

	//========================================================
	//Delete any previous entries, build database rows

	$wipe_entries = mysql_query("DELETE FROM `web_games` WHERE event='$eventId' AND type='$game_type'");

	$last_id = mysql_result(mysql_query("SELECT MAX(id) AS value FROM `web_games`"),0);
	$last_row = $last_id+1;

	$time_count = 0;

	for($en = $last_row; $en < $game_count+$last_row; $en++)
	   {
		if($time_count == "0")
		    {
			$game_time = $eventRow[stamp];
		     }
		else
		    {
			$game_time = $eventRow[stamp] + ($time_count*(60*12));
		     }

		mysql_query("INSERT INTO `web_games` (`id`, `event`, `time`) VALUES ('$en', '$eventId', '$game_time')");

		$time_count++;
	    }

	//========================================================
	//Offset calculations

$offset_array = array(2, 5, 9, 7, 12, 13);
	$col = 1;

	foreach($offset_array as $offset_value)
	   {
		$row = $last_row;

		$col_name = "team" . $col;

		$slice = array_slice($teams, $offset_value);
		$remain = array_slice($teams, 0, $offset_value);
		$merged = array_merge($slice, $remain);

		foreach ($merged as $value)
		   {
			mysql_query("UPDATE `web_games` SET $col_name='$value' WHERE id='$row'");
			$row++;
		    }

		$col++;
	    }

	print("<p align=\"center\"><b>Schedule Built</b></p>\n\n");
	print("<p align=\"center\"><a href=\"./schedule.php?game_type=$game_type&eventId=$eventId\">Click here to continue</a></p>\n\n");

	print_footer();
       }

  //===========================================================================
  //Print Page
  //===========================================================================

  elseif($_GET["getEvent"] != "" && $_GET["getType"] != "" && $_GET["printPage"] != "")
      {
	//Updated June 22, 2009
	$getEvent = $_GET["getEvent"];
	$getType = $_GET["getType"];

	$gameInfo = fetch_game_info($getEvent,$getType);

	$hex_array = fetch_hex_array($gameInfo[game_pack_sets],$gameInfo[game_format]);

	$soft_hex_array = fetch_soft_hex_array($gameInfo[game_pack_sets],$gameInfo[game_format]);

	if($_GET["printPage"] == "cycle")
	    {
		printSimpleHeader($getEvent,"$gameInfo[game_title] Schedule");
		print("\n<meta http-equiv=\"refresh\" content=\"6; URL=./teams.php?eventId=$getEvent&do=print&cycle=true&game_type=$getType\">\n\n");
	     }
	else
	    {
		print_header($getEvent,"$gameInfo[game_title] Schedule");
	     }

	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$getEvent' ORDER BY `team_id` ASC");

	if(mysql_num_rows($fetch_teams) == "0")
	    {
		fatalError("Unable to load teams");
	     }
	while($teamRow = mysql_fetch_assoc($fetch_teams))
	    {
		$team_id = $teamRow["team_id"];
		$team_name = $teamRow["team_name"];
		$team_names[$team_id] = $team_name;
	     }

	if($gameInfo[game_pack_sets] > 4)
	    {
		$table_width = 920;
		$avail_width = 820;
	     }
	else
	    {
		$table_width = 650;
		$avail_width = 580;
	     }

	$cell_width = round($avail_width/$gameInfo[game_pack_sets]);

	$twrLabel_row = 0;
	$twrLabel_int = 1;

	print("<table width=\"$table_width\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\" bgcolor=\"#aaaaaa\">\n");
	print("  <tr bgcolor=\"#ffffff\">\n");

	if($gameInfo[format_num] != "1" && $gameInfo[format_num] != $gameInfo[game_pack_sets])
	    {
		print("   <td align=\"center\" rowspan=\"2\"><b>Game</b></td>\n");

		$twrLabels = round($gameInfo[game_pack_sets]/$gameInfo[format_num])+1;

		for($set_column = 1; $set_column < $gameInfo[game_pack_sets]+1; $set_column++)
		    {
			if($twrLabel_row == "0" && $twrLabel_int == "1" || $twrLabel_row == $gameInfo[format_num] && $twrLabel_int < $twrLabels)
			    {
				print("   <td colspan=\"$gameInfo[format_num]\" height=\"20\" align=\"center\"><b>Tower $twrLabel_int</b></td>\n");

				$twrLabel_row = 1;
				$twrLabel_int++;
			     }
			else
			    {
				$twrLabel_row++;
			     }
		     }

		print(" </tr>\n");
		print("  <tr>\n");
	     }
	else
	    {
		print("   <td align=\"center\"><b>Game</b></td>\n");
	     }

	for($set_column = 1; $set_column < $gameInfo[game_pack_sets]+1; $set_column++)
	    {
		print("   <td align=\"center\" width=\"$cell_width\" bgcolor=\"$hex_array[$set_column]\"><b>Set $set_column</b></td>\n");
	     }

	print(" </tr>\n");

	$color1 = "#ffffff";
	$color2 = "#f0f0f0";

	$row_count = 1;

	$fetch_games = mysql_query("SELECT * FROM `web_games` WHERE event='$getEvent' AND type='$getType' ORDER BY `time`, `id` ASC");

	if(mysql_num_rows($fetch_games) == "0")
	    {
		print("  <tr>\n");
		print("   <td bgcolor=\"#ffffff\" align=\"center\" height=\"50\" colspan=\"14\" class=\"large\"><b>Schedule Not Available</td>\n");
		print(" </tr>\n");
	     }
	while($gameRow = mysql_fetch_assoc($fetch_games))
	    {
		$row_color = ($row_count % 2) ? $color1 : $color2;

		print("  <tr bgcolor=\"$row_color\">\n");
		print("   <td align=\"center\">$row_count</td>\n");

		for($set_column = 1; $set_column < $gameInfo[game_pack_sets]+1; $set_column++)
		    {
			$team_col = "team" . $set_column;
			$field_team = $gameRow[$team_col];

			if($field_team == "0")
			    {
				print("   <td align=\"center\" class=\"bg_grey_diag\">&nbsp;</td>\n");
			     }
			else
			    {
				$team_name_len = strlen($team_names[$field_team]);

				if($team_name_len > 17)
				    {
					$word_array = explode(' ', $team_names[$field_team]);

					if(count($word_array) > 2 && 2 > 0)
					    {
						$team_name = implode(' ', array_slice($word_array, 0, 2));
						$team_name .= "...";
					     }
					else
					    {
						$team_name = $team_names[$field_team];
					     }
				     }
				else
				    {
					$team_name = $team_names[$field_team];
				     }

				print("   <td align=\"center\" class=\"print\">$team_name</td>\n");
			     }
		     }

		print(" </tr>\n");

		$row_count++;
	     }

	print("</table>\n\n");

	print_footer();
     }

  //===========================================================================
  //Fetch game page
  //===========================================================================

  elseif($_GET["getEvent"] != "" && $_GET["getType"] != "" && $_GET["getGame"] != "")
    {
	//Updated June 23, 2009
	$getEvent = $_GET["getEvent"];
	$getType = $_GET["getType"];
	$getGame = $_GET["getGame"];

	$gameInfo = fetch_game_info($getEvent,$getType);

	$hex_array = fetch_hex_array($gameInfo[game_pack_sets],$gameInfo[game_format]);
	$soft_hex_array = fetch_soft_hex_array($gameInfo[game_pack_sets],$gameInfo[game_format]);

	print_header($getEvent,"Game Scores");

	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$getEvent' ORDER BY `team_id` ASC");

	if(mysql_num_rows($fetch_teams) == "0")
	    {
		fatalError("Unable to load teams");
	     }
	while($teamRow = mysql_fetch_assoc($fetch_teams))
	    {
		$team_id = $teamRow["team_id"];
		$team_name = $teamRow["team_name"];
		$team_names[$team_id] = $team_name;
	     }

	$fetch_game = mysql_query("SELECT * FROM `web_games` WHERE id='$getGame' AND event='$getEvent' LIMIT 1");

	if(mysql_num_rows($fetch_game) == "0")
	    {
		fatalError("Unable to load game");
	     }
	else
	    {
		$gameRow = mysql_fetch_assoc($fetch_game);
	     }

	if($gameInfo[format_num] == "1")
	    {
		$rowInc = 0;
	     }
	else
	    {
		$rowInc = 1;
	     }

	$true_pack_sets = 1;

	for($setRows = 1; $setRows < $gameInfo[game_pack_sets]+1; $setRows++)
	    {
		$set_col = "team" . $setRows;
		$set_team = $gameRow[$set_col];

		//print("$set_team - ");
		if($set_team != "0")
		    {
			$true_pack_sets++;
		     }
	     }
	print("<br>");
	
	$twrInc = 1;
	$allInc = 1;
	$noneInc = 1;

	$twrLabel_row = 0;
	$twrLabel_int = 1;
	$twrLabels = round($true_pack_sets/$gameInfo[format_num])+1;
	//$twrLabels = round($gameInfo[game_pack_sets]/$gameInfo[format_num])+1;

	$tableWidth = 285*$gameInfo[format_num];

	print("<table width=\"$tableWidth\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\">\n");
	print("  <tr>\n");

	for($teamRows = 1; $teamRows < $true_pack_sets; $teamRows++)
	   {
		$team_col = "team" . $teamRows;
		$set_team = $gameRow[$team_col];

		$rank_col = $team_col . "_rank";
		$team_rank = $gameRow[$rank_col];

		$score_col = $team_col . "_score";
		$team_score = $gameRow[$score_col];

		$pts_col = $team_col . "_pts";
		$team_pts = $gameRow[$pts_col];

		$team_name = team_name($set_team);

		if($gameInfo[format_num] != "1" && $twrLabel_row == "0" && $twrLabel_int == "1" || $gameInfo[format_num] != "1" && $twrLabel_row == $gameInfo[format_num] && $twrLabel_int < $twrLabels)
		    {
			print("   <td colspan=\"$gameInfo[format_num]\" valign=\"top\">\n");
			print("	<table width=\"$tableWidth\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\" bgcolor=\"#aaaaaa\">\n");
			print("	  <tr>\n");
			print("	   <td bgcolor=\"#ffffff\" height=\"20\" colspan=\"4\"><b>Tower $twrLabel_int</b></td>\n");
			print("	 </tr>\n");
			print("	</table></td>\n");
			print(" </tr>\n");
			print("  <tr>\n");

			$twrLabel_row = 1;
			$twrLabel_int++;
		     }
		else
		    {
			$twrLabel_row++;
		     }

		print("   <td valign=\"top\">\n");
		print("	<table width=\"275\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\" bgcolor=\"#aaaaaa\">\n");
		print("	  <tr>\n");
		print("	   <td bgcolor=\"$hex_array[$teamRows]\" colspan=\"5\" align=\"center\"><b>$team_name</b></td>\n");
		print("	 </tr>\n");
		print("	  <tr>\n");
		print("	   <td bgcolor=\"$soft_hex_array[$teamRows]\" colspan=\"5\" align=\"center\" class=\"small\">Rank: $team_rank &nbsp; Score: $team_score &nbsp; Points: $team_pts</td>\n");
		print("	 </tr>\n");
		print("	  <tr bgcolor=\"#f6f6f6\">\n");
		print("	   <td width=\"155\" class=\"small\">Alias / Player</td>\n");
		print("	   <td width=\"30\" align=\"center\" class=\"small\">Rk</td>\n");
		print("	   <td width=\"30\" align=\"center\" class=\"small\">Twr</td>\n");
		print("	   <td width=\"45\" align=\"center\" class=\"small\">Score</td>\n");
		print("	   <td width=\"35\" align=\"center\" class=\"small\">Img</td>\n");
		print("	 </tr>\n");

		$fetch_scores = mysql_query("SELECT * FROM `web_scores` WHERE score_event='$getEvent' AND score_team='$set_team' AND score_game='$getGame' ORDER BY `score_rank` ASC");

		if(mysql_num_rows($fetch_scores) == "0")
		    {
			print("	  <tr bgcolor=\"#ffffff\">\n");
			print("	   <td colspan=\"5\" align=\"center\">Scores not available</td>\n");
			print("	 </tr>\n");
		     }
		while($scoreRow = mysql_fetch_assoc($fetch_scores))
		    {
			$score_score = $scoreRow[score_score];
			$score_deduc = $scoreRow[score_deduc];
			$score_scanned_image = $scoreRow[score_scanned_image];

			$field_player = return_codename($scoreRow[score_player]);

			if($_GET["highlightPlayer"] != "" && is_numeric($_GET["highlightPlayer"]))
			    {
				if($_GET["highlightPlayer"] == $scoreRow[score_player])
				    {
					$playerRowBg = "#ffc4fd";
				     }
				else
				    {
					$playerRowBg = "#ffffff";
				     }
			     }
			else
			    {
				$playerRowBg = "#ffffff";
			     }

			print("	  <tr bgcolor=\"$playerRowBg\">\n");

			$fetch_alias = mysql_query("SELECT * FROM `web_alias` WHERE player_id='$scoreRow[score_player]' AND alias_team='$set_team'");

			if(mysql_num_rows($fetch_alias) == "0")
			    {
				print("	   <td><a href=\"./stats_player.php?playerId=$scoreRow[score_player]\">$field_player</a></td>\n");				
			     }
			else
			    {
				$aliasRow = mysql_fetch_assoc($fetch_alias);
				$alias_name = $aliasRow[alias_name];

				print("	   <td><a href=\"./stats_player.php?playerId=$scoreRow[score_player]\">$alias_name / $field_player</a></td>\n");
			     }

			print("	   <td align=\"center\">$scoreRow[score_rank]</td>\n");
			print("	   <td align=\"center\">$scoreRow[score_twr_rank]</td>\n");

			if($score_deduc != "0")
			    {
				$field_score = $score_score-$score_deduc;
				print("	   <td align=\"center\" class=\"bold_red\"><a href=\"#\" class=\"tooltip\" title=\"$field_player took a deduction of $score_deduc points this game\">$field_score</a></td>\n");
			     }
			else
			    {
				print("	   <td align=\"center\">$score_score</td>\n");
			     }

			if($score_scanned_image != "")
			    {
				print("	   <td align=\"center\"><a href=\"./scanned/$score_scanned_image\" target=\"_blank\">View</a></td>\n");
			     }
			else
			    {
				print("	   <td align=\"center\">-</td>\n");
			     }

			print("	 </tr>\n");
		     }

		print("	</table></td>\n");

		if($rowInc == 1 && $twrInc == $gameInfo[format_num] && $allInc == $gameInfo[game_pack_sets])
		    {
			print(" </tr>\n");
		     }
		elseif($rowInc == 1 && $twrInc == $gameInfo[format_num])
		    {
			print(" </tr>\n");
			print("  <tr bgcolor=\"#ffffff\">\n");

			$twrInc = 1;
			$allInc++;
		     }
		elseif($allInc == $gameInfo[game_pack_sets])
		    {
			print(" </tr>\n");
		     }
		elseif($rowInc == 1)
		    {
			$twrInc++;
			$allInc++;
		     }

		if($gameInfo[format_num] == "1" && $noneInc < $gameInfo[game_pack_sets])
		    {
			print(" </tr>\n");
			print("  <tr>\n");
		     }
		elseif($gameInfo[format_num] == "1" && $noneInc == $gameInfo[game_pack_sets])
		    {
			print(" </tr>\n");
		     }

		$noneInc++;
	     }

	$game_time = date("F j, Y \a\\t g:i A", $gameRow[time]);

	print("  <tr>\n");
	print("   <td colspan=\"$gameInfo[format_num]\" valign=\"top\">\n");
	print("	<table width=\"$tableWidth\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\" bgcolor=\"#aaaaaa\">\n");
	print("	  <tr>\n");
	print("	   <td bgcolor=\"#f6f6f6\" height=\"20\" align=\"center\" class=\"small\" colspan=\"4\">Game Played on $game_time at LQ $gameInfo[center]</td>\n");
	print("	 </tr>\n");
	print("	</table></td>\n");
	print(" </tr>\n");

	print("</table>\n\n");

	print_footer();
     }

  //===========================================================================
  //Admin page
  //===========================================================================


  elseif($_GET["getEvent"] != "" && $_GET["getType"] != "" && isset($_COOKIE[lqc_scores_session]))
    {
	$eventId = $_GET["getEvent"];
	$game_type = $_GET["getType"];

	$event_pack_sets = fetch_pack_sets($eventId,$game_type);
	$game_title = fetch_game_title($eventId,$game_type);
	$game_format = fetch_game_format($eventId,$game_type);
	$format_num = fetch_format_num($game_format,$event_pack_sets);

	$num_f = round($event_pack_sets/$format_num);

	$hex_array = fetch_hex_array($event_pack_sets,$game_format);

	$rgm_array = fetch_rgm_array($event_pack_sets,$game_format);

	print_header($eventId,"$game_title Schedule");

	verify_session();

	verify_event_pm($eventId);

	//$fetch_games = mysql_query("SELECT * FROM `web_games` WHERE event='$eventId' AND type='$game_type' ORDER BY `time`, `id` ASC");
	$fetch_games = mysql_query("SELECT * FROM `web_games` WHERE event='$eventId' AND type='$game_type' ORDER BY `id` ASC");

	print("<table width=\"480\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\" bgcolor=\"#dadada\">\n");
	print("  <tr bgcolor=\"#f0f0f0\">\n");
	print("	   <td width=\"240\" align=\"center\"><b>Script Actions</b></td>\n");
	print("	   <td width=\"240\" align=\"center\"><b>Printing Options</b></td>\n");
	print("  </tr>\n");
	print("  <tr bgcolor=\"#ffffff\">\n");
	print("	   <td width=\"240\" align=\"center\">");

	if(mysql_num_rows($fetch_games) == "0")
	    {
		print("<a href=\"./schedule.php?game_type=$game_type&eventId=$eventId&generate=schedule\">Generate Schedule</a><br>");
	     }

	print("<a href=\"./rank_individuals.php?getEvent=$eventId&getType=$game_type\">Run Individual Scores</a><br>");

	if($game_type == "pre")
	    {
		print("<a href=\"./schedule.php?eventId=$eventId&generate=playoff_schedule\">Generate Playoff Schedule</a>");
	     }

	print("</td>\n	   <td width=\"240\" align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getType=$game_type&printPage=default\">Print Schedule</a><br><a href=\"./team_scores.php?game_type=$game_type&eventId=$eventId&print=standings\">Print Standings</a><br><a href=\"./schedule.php?getEvent=$eventId&getType=$game_type&printPage=cycle\">Tournament Mode</a></td>\n");
	print("  </tr>\n");
	print("</table>\n");

	print("<br><br>\n");

	if(mysql_num_rows($fetch_games) == "0")
	    {
		
	     }
	while($gameRow = mysql_fetch_assoc($fetch_games))
	    {
		$gameId = $gameRow[id];
		$game_time = $gameRow[time];

		$num_i = 1;
		$num_c = 1;

		print("<table width=\"480\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\" bgcolor=\"#dadada\">\n");

		$time_n = date("n", $game_time);
		$time_j = date("j", $game_time);
		$time_y = date("y", $game_time);

		$time_h = date("H", $game_time);
		$time_i = date("i", $game_time);

		print("  <tr bgcolor=\"#f5f5f5\">\n");
		print("   <td colspan=\"6\"><table border=\"0\" width=\"470\" cellspacing=\"1\" cellpadding=\"0\" align=\"center\">\n");
		print("  <tr>\n");
		print("   <td width=\"90\"><a href=\"./score_entry.php?getEvent=$eventId&getGame=$gameRow[id]&getType=$game_type\">Score Entry &raquo;</a></td>");
		print("   <td class=\"small\">GAME DATE & TIME:</td>");
		print("   <td><form method=\"POST\" action=\"schedule.php\"><input type=\"hidden\" name=\"eventId\" value=\"$eventId\"><input type=\"hidden\" name=\"game_type\" value=\"$game_type\"><input type=\"hidden\" name=\"gameId\" value=\"$gameId\"><input type=\"hidden\" name=\"do\" value=\"game_time\"><input class=\"clear\" type=\"text\" name=\"game_time_month\" size=\"1\" value=\"$time_n\"></td>");
		print("   <td align=\"center\">/</td>");
		print("   <td><input class=\"clear\" type=\"text\" name=\"game_time_day\" size=\"1\" value=\"$time_j\"></td>");
		print("   <td align=\"center\">/</td>");
		print("   <td><input class=\"clear\" type=\"text\" name=\"game_time_year\" size=\"1\" value=\"$time_y\"></td>");
		print("   <td align=\"center\"> &nbsp; </td>");
		print("   <td><input class=\"clear\" type=\"text\" name=\"game_time_hh\" size=\"1\" value=\"$time_h\"></td>");
		print("   <td align=\"center\">:</td>");
		print("   <td><input class=\"clear\" type=\"text\" name=\"game_time_mm\" size=\"1\" value=\"$time_i\"></td>");
		print("   <td align=\"center\"> &nbsp; </td>");
		print("   <td><input class=\"clear\" type=\"submit\" name=\"submit\" value=\"Update\"></form></td>");
		print(" </tr>\n");
		print("</table></td>\n");
		print(" </tr>\n");

		print("  <tr bgcolor=\"#f0f0f0\" height=\"25\">\n");
		print("   <td width=\"40\" align=\"center\"><b>Set</b></td>\n");
		print("   <td width=\"160\"><b>Team</b></td>\n");
		print("   <td width=\"70\" align=\"center\"><b>Rank</b></td>\n");
		print("   <td width=\"70\" align=\"center\"><b>Score</b></td>\n");
		print("   <td width=\"70\" align=\"center\"><form method=\"POST\" action=\"schedule.php\"><input type=\"hidden\" name=\"eventId\" value=\"$eventId\"><input type=\"hidden\" name=\"game_type\" value=\"$game_type\"><input type=\"hidden\" name=\"gameId\" value=\"$gameId\"><input type=\"hidden\" name=\"do\" value=\"team_pts\"><b>Pts</b></td>\n");
		print("   <td width=\"70\" align=\"center\"><b>Deduc</b></td>\n");
		print(" </tr>\n");

		for($cols = 1; $cols < $event_pack_sets+1; $cols++)
		   {
			$team_col = "team" . $cols;
			$field_team = $gameRow[$team_col];

			$team_rank = "team" . $cols . "_rank";
			$field_rank = $gameRow[$team_rank];

			$team_score = "team" . $cols . "_score";
			$field_score = $gameRow[$team_score];

			$team_deduc = "team" . $cols . "_deduc";
			$field_deduc = $gameRow[$team_deduc];

			$team_pts = "team" . $cols . "_pts";
			$field_pts = $gameRow[$team_pts];

			$setColor = $rgm_array[$cols];
			$colorScore[$setColor][] = $field_score;

			if($field_team != "0")
			    {
				$team_name = team_name($field_team);

				print("  <tr bgcolor=\"#ffffff\">\n");
				print("   <td bgcolor=\"$hex_array[$cols]\" align=\"center\">$cols</td>\n");
				print("   <td>$team_name</td>\n");
				print("   <td align=\"center\">$field_rank</td>\n");
				print("   <td align=\"center\">$field_score</td>\n");
				print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"team_pts[$team_pts]\" size=\"1\" value=\"$field_pts\"></td>\n");
				print("   <td align=\"center\">$field_deduc</td>\n");
				print(" </tr>\n");
			     }

			if($num_i == $format_num && $num_c < $num_f && $format_num != "1")
			    {
				print("  <tr bgcolor=\"#ffffff\">\n");
				print("   <td  colspan=\"6\"><img src=\"./images/clear.gif\" width=\"5\" height=\"2\"></td>\n");
				print(" </tr>\n");

				$num_i = 1;
				$num_c++;
			     }
			else
			    {
				$num_i++;
			     }
		    }

?>
  <tr bgcolor="#ffffff">
   <td colspan="4"><b>Score Check</b> 
<?

	if(isset($colorScore[red]) && !empty($colorScore[red]))
	    {
		$redSum = array_sum($colorScore[red]);
		print("&middot; Red: $redSum ");
	     }

	if(isset($colorScore[green]) && !empty($colorScore[green]))
	    {
		$greenSum = array_sum($colorScore[green]);
		print("&middot; Green: $greenSum ");
	     }

	if(isset($colorScore[mixed]) && !empty($colorScore[mixed]))
	    {
		$mixedSum = array_sum($colorScore[mixed]);
		print("&middot; Mixed: $mixedSum ");
	     }

	unset($colorScore);

?></td>
   <td align="center"><input class="clear" type="submit" name="submit" value="Go"></form></td>
   <td>&nbsp;</td>
 </tr>
</table>

<br><br>

<?
	     }

	$fetch_max_time = mysql_query("SELECT MAX(time) AS time FROM `web_games` WHERE event='$eventId' AND type='$game_type'");

	$fmt_row = mysql_fetch_assoc($fetch_max_time);
	$fmt_time = $fmt_row[time];

	if($fmt_time == "0")
	    {
		$last_time = mktime();
	     }
	else
	    {
		$last_time = $fmt_time+900;
	     }

	$ntime_n = date("n", $last_time);
	$ntime_j = date("j", $last_time);
	$ntime_y = date("y", $last_time);

	$ntime_h = date("H", $last_time);
	$ntime_i = date("i", $last_time);

	print("<form method=\"POST\" action=\"schedule.php\"><input type=\"hidden\" name=\"eventId\" value=\"$eventId\"><input type=\"hidden\" name=\"game_type\" value=\"$game_type\"><input type=\"hidden\" name=\"do\" value=\"add_game\">\n");
	print("<table width=\"480\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\" bgcolor=\"#dadada\">\n");
	print("  <tr bgcolor=\"#f0f0f0\">\n");
	print("	   <td align=\"center\" colspan=\"2\"><b>Add Game</b></td>\n");
	print("  </tr>\n");
	print("  <tr bgcolor=\"#f5f5f5\">\n");
	print("   <td colspan=\"6\"><table border=\"0\" width=\"370\" cellspacing=\"1\" cellpadding=\"0\" align=\"center\">\n");
	print("  <tr>\n");
	print("   <td class=\"small\">GAME DATE & TIME:</td>");
	print("   <td><input type=\"text\" name=\"game_time_month\" size=\"1\" value=\"$ntime_n\"></td>");
	print("   <td align=\"center\" width=\"10\">/</td>");
	print("   <td><input type=\"text\" name=\"game_time_day\" size=\"1\" value=\"$ntime_j\"></td>");
	print("   <td align=\"center\" width=\"10\">/</td>");
	print("   <td><input type=\"text\" name=\"game_time_year\" size=\"1\" value=\"$ntime_y\"></td>");
	print("   <td align=\"center\"> &nbsp; </td>");
	print("   <td><input type=\"text\" name=\"game_time_hh\" size=\"1\" value=\"$ntime_h\"></td>");
	print("   <td align=\"center\">:</td>");
	print("   <td><input type=\"text\" name=\"game_time_mm\" size=\"1\" value=\"$ntime_i\"></td>");
	print(" </tr>\n");
	print("</table></td>\n");
	print(" </tr>\n");

	for($cols = 1; $cols < $event_pack_sets+1; $cols++)
	   {
		$col_name = "team" . $cols;

		print("  <tr bgcolor=\"#ffffff\">\n");
		print("   <td align=\"center\">$cols</td>\n");
		print("   <td><select name=\"$col_name\" size=\"1\">");
		team_list($eventId);
		print("</select></td>\n");
		print(" </tr>\n");
	    }

	print("  <tr bgcolor=\"#ffffff\">\n");
	print("	   <td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Add Game\"></td>\n");
	print("  </tr>\n");
	print("</table>\n");
	print("</form>\n");

	print_footer();
     }

  //===========================================================================
  //Fetch main page
  //===========================================================================

  elseif($_GET["getEvent"] != "" && $_GET["getType"] != "")
    {
	//Updated June 22, 2009
	$getEvent = $_GET["getEvent"];
	$getType = $_GET["getType"];

	$gameInfo = fetch_game_info($getEvent,$getType);

	$hex_array = fetch_hex_array($gameInfo[game_pack_sets],$gameInfo[game_format]);

	print_header($getEvent,"$gameInfo[game_title] Schedule");

	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$getEvent' ORDER BY `team_id` ASC");

	if(mysql_num_rows($fetch_teams) == "0")
	    {
		fatalError("Unable to load teams");
	     }
	while($teamRow = mysql_fetch_assoc($fetch_teams))
	    {
		$team_id = $teamRow["team_id"];
		$team_name = $teamRow["team_name"];
		$team_names[$team_id] = $team_name;
	     }

	$table_width = 50+(165*$gameInfo[game_pack_sets]);

	$twrLabel_row = 0;
	$twrLabel_int = 1;

	print("<table width=\"$table_width\" cellspacing=\"1\" cellpadding=\"2\" align=\"center\" bgcolor=\"#aaaaaa\">\n");
	print("  <tr bgcolor=\"#ffffff\">\n");

	if($gameInfo[format_num] != "1" && $gameInfo[format_num] != $gameInfo[game_pack_sets])
	    {
		print("   <td align=\"center\" rowspan=\"3\" width=\"50\"><b>Time</b></td>\n");

		$twrLabels = round($gameInfo[game_pack_sets]/$gameInfo[format_num])+1;

		for($set_column = 1; $set_column < $gameInfo[game_pack_sets]+1; $set_column++)
		    {
			$twrColspan = $gameInfo[format_num]*4;

			if($twrLabel_row == "0" && $twrLabel_int == "1" || $twrLabel_row == $gameInfo[format_num] && $twrLabel_int < $twrLabels)
			    {
				print("   <td colspan=\"$twrColspan\" height=\"20\" align=\"center\"><b>Tower $twrLabel_int</b></td>\n");

				$twrLabel_row = 1;
				$twrLabel_int++;
			     }
			else
			    {
				$twrLabel_row++;
			     }
		     }

		print(" </tr>\n");
		print("  <tr>\n");
	     }
	else
	    {
		print("   <td align=\"center\" rowspan=\"2\" width=\"50\"><b>Time</b></td>\n");
	     }

	for($set_column = 1; $set_column < $gameInfo[game_pack_sets]+1; $set_column++)
	    {
		print("   <td bgcolor=\"$hex_array[$set_column]\" align=\"center\" width=\"165\" colspan=\"4\"><b>Set $set_column</b></td>\n");
	     }

	print(" </tr>\n");
	print("  <tr bgcolor=\"#f6f6f6\">\n");

	for($set_column = 1; $set_column < $gameInfo[game_pack_sets]+1; $set_column++)
	    {
		print("   <td align=\"center\" class=\"small\">Team</td>\n");
		print("   <td align=\"center\" class=\"small\">Rk</td>\n");
		print("   <td align=\"center\" class=\"small\">Pts</td>\n");
		print("   <td align=\"center\" class=\"small\">Score</td>\n");
	     }

	print(" </tr>\n");

	$color1 = "#ffffff";
	$color2 = "#f0f0f0";

	$row_count = 1;

	$fetch_games = mysql_query("SELECT * FROM `web_games` WHERE event='$getEvent' AND type='$getType' ORDER BY `time`, `id` ASC");

	if(mysql_num_rows($fetch_games) == "0")
	    {
		print("  <tr>\n");
		print("   <td bgcolor=\"#ffffff\" align=\"center\" height=\"50\" colspan=\"14\" class=\"large\"><b>Schedule Not Available</td>\n");
		print(" </tr>\n");
	     }
	while($gameRow = mysql_fetch_assoc($fetch_games))
	    {
		$row_color = ($row_count % 2) ? $color1 : $color2;

		$game_time = date("g:i A", $gameRow[time]);

		print("  <tr bgcolor=\"$row_color\">\n");
		print("   <td align=\"center\"><a href=\"./schedule.php?getEvent=$getEvent&getType=$getType&getGame=$gameRow[id]\" class=\"tooltip\" title=\"Click here to view game\">$game_time</a></td>\n");

		for($set_column = 1; $set_column < $gameInfo[game_pack_sets]+1; $set_column++)
		    {
			$team_col = "team" . $set_column;
			$team_rank = $team_col . "_rank";
			$team_score = $team_col . "_score";
			$team_deduc = $team_col . "_deduc";
			$team_pts = $team_col . "_pts";

			$field_team = $gameRow[$team_col];
			$field_rank = $gameRow[$team_rank];
			$field_score = $gameRow[$team_score];
			$field_deduc = $gameRow[$team_deduc];
			$field_pts = $gameRow[$team_pts];

			if($field_team == "0")
			    {
				print("   <td colspan=\"4\" class=\"bg_grey_diag\">&nbsp;</td>\n");
			     }
			else
			    {
				$team_name_len = strlen($team_names[$field_team]);

				if($team_name_len > 14)
				    {
					$team_name = substr($team_names[$field_team], 0, 12);
					$team_name .= "...";

					print("   <td align=\"center\" class=\"default\"><a href=\"#\" class=\"tooltip\" title=\"$team_names[$field_team]\">$team_name</a></td>\n");
				     }
				else
				    {
					$team_name = $team_names[$field_team];
					print("   <td align=\"center\">$team_name</td>\n");
				     }

				print("   <td align=\"center\">$field_rank</td>\n");
				print("   <td align=\"center\">$field_pts</td>\n");

				if($field_deduc != "0")
				    {
					print("   <td align=\"center\" class=\"bold_red\"><a href=\"#\" class=\"tooltip\" title=\"$team_name took a deduction of<br>$field_deduc points this game\">$field_score</a></td>\n");
				     }
				else
				    {
					print("   <td align=\"center\">$field_score</td>\n");
				     }
			     }

			if($field_score != "0")
			    {
				$average_rank[$set_column][] = $field_rank;
				$average_pts[$set_column][] = $field_pts;
				$average_score[$set_column][] = $field_score;
			     }
		     }

		print(" </tr>\n");

		$row_count++;
	     }

	print(" <tr bgcolor=\"#f6f6f6\">\n");
	print("   <td align=\"center\" class=\"small\">Averages</td>\n");

	for($set_column = 1; $set_column < $gameInfo[game_pack_sets]+1; $set_column++)
	    {
		if(isset($average_rank[$set_column]))
		    {
			$avg_rank = round(array_sum($average_rank[$set_column])/count($average_rank[$set_column]),1);
		     }
		else
		    {
			$avg_rank = "-";
		     }

		if(isset($average_pts[$set_column]))
		    {
			$avg_pts = round(array_sum($average_pts[$set_column])/count($average_pts[$set_column]),1);
		     }
		else
		    {
			$avg_pts = "-";
		     }

		if(isset($average_score[$set_column]))
		    {
			$avg_score = round(array_sum($average_score[$set_column])/count($average_score[$set_column]),0);
		     }
		else
		    {
			$avg_score = "-";
		     }


		print("   <td class=\"small\">&nbsp;</td>\n");
		print("   <td align=\"center\" class=\"small\">$avg_rank</td>\n");
		print("   <td align=\"center\" class=\"small\">$avg_pts</td>\n");
		print("   <td align=\"center\" class=\"small\">$avg_score</td>\n");
	     }

	print(" </tr>\n");
	print("</table>\n\n");

	print_footer();
     }
  else
    {
	print_header("Error",0);
	show_error("405 Method Not Allowed");
	print_footer();
     }
?>