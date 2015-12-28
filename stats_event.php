<?php

  require("config.php");
  require("functions.php");

  if($_GET["eventId"] != "")
    {
	foreach($_GET as $key=>$value)
	    {
		${$key} = $value;
	     }

	$fetch_event = mysql_query("SELECT * FROM `web_events` WHERE `id` = '$eventId' LIMIT 1");

	if(mysql_num_rows($fetch_event) == "0")
	    {
		print_header("Welcome","Events");
		show_error("The event you requested does not exist");
		print_footer();
	     }
	while($eventRow = mysql_fetch_assoc($fetch_event))
	    {
		print_header("$eventRow[title]","Event Stats");

		if($limit != "" && is_numeric($limit))
		    {
			$fetch_limit = $limit;
		     }
		else
		    {
			$fetch_limit = 10;
		     }

		if($sort == "asc")
		    {
			$fetch_sort = "ASC";
			$title_sort = "Bottom";
		     }
		elseif($sort == "desc")
		    {
			$fetch_sort = "DESC";
			$title_sort = "Top";
		     }
		else
		    {
			$fetch_sort = "DESC";
			$title_sort = "Top";
		     }

		print("<table width=\"430\" cellspacing=\"1\" cellpadding=\"2\" class=\"table_bl_top\" align=\"center\">\n");
		print("  <tr>\n");
		print("   <td colspan=\"16\"><b>Overall $title_sort $fetch_limit Scores by Event</b></td>\n");
		print(" </tr>\n");
		print("</table>\n\n");
		print("<table width=\"430\" cellspacing=\"1\" cellpadding=\"2\" bgcolor=\"#aaaaaa\" align=\"center\">\n");
		print("  <tr bgcolor=\"#ffffff\">\n");
		print("   <td colspan=\"16\" height=\"22\" class=\"small\">View Top: <a href=\"./stats_event.php?eventId=$eventId&sort=desc&limit=25\">25</a> &bull; <a href=\"./stats_event.php?eventId=$eventId&sort=desc&limit=50\">50</a> &bull; <a href=\"./stats_event.php?eventId=$eventId&sort=desc&limit=100\">100</a> | View Bottom: <a href=\"./stats_event.php?eventId=$eventId&sort=asc&limit=25\">25</a> &bull; <a href=\"./stats_event.php?eventId=$eventId&sort=asc&limit=50\">50</a> &bull; <a href=\"./stats_event.php?eventId=$eventId&sort=asc&limit=100\">100</a></td>\n");
		print(" </tr>\n");
		print("  <tr bgcolor=\"#ededed\" height=\"22\">\n");
		print("   <td width=\"20\">&nbsp;</td>\n");
		print("   <td width=\"110\">Codename</td>\n");
		print("   <td width=\"60\" align=\"center\">Rank</td>\n");
		print("   <td width=\"60\" align=\"center\">Score</td>\n");
		print("   <td width=\"60\" align=\"center\">Type</td>\n");
		print("   <td width=\"60\" align=\"center\">Image</td>\n");
		print("   <td width=\"60\" align=\"center\">Game</td>\n");
		print(" </tr>\n");

		$high_i = 1;

		$fetch_scores = mysql_query("SELECT * FROM `web_scores` WHERE score_event='$eventId' ORDER BY `score_score` $fetch_sort LIMIT 0, $fetch_limit");

		if(mysql_num_rows($fetch_scores) == "0")
		    {
			print("  <tr bgcolor=\"#ffffff\">\n");
			print("   <td colspan=\"40\" align=\"center\" class=\"small\">No data to display</td>\n");
			print(" </tr>\n");
		     }

		while($fetch_high_row = mysql_fetch_assoc($fetch_scores))
		    {
		  	$player_codename = return_codename($fetch_high_row[score_player]);

			$high_game_type = strtoupper($fetch_high_row[score_game_type]);

			print("  <tr bgcolor=\"#ffffff\">\n");
			print("   <td align=\"center\">$high_i</td>\n");
			print("   <td><a href=\"./stats_player.php?playerId=$fetch_high_row[score_player]\">$player_codename</a></td>\n");
			print("   <td align=\"center\">$fetch_high_row[score_rank]</td>\n");
			print("   <td align=\"center\">$fetch_high_row[score_score]</td>\n");
			print("   <td align=\"center\">$high_game_type</td>\n");

			if($fetch_high_row[score_scanned_image] != "")
			    {
				print("   <td align=\"center\"><a href=\"./scanned/$fetch_high_row[score_scanned_image]\" target=\"_blank\">View</a></td>\n");
			     }
			else
			    {
				print("   <td align=\"center\">-</td>\n");
			     }

			print("   <td align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getGame=$fetch_high_row[score_game]&getType=$fetch_high_row[score_game_type]\">View</a></td>\n");
			print(" </tr>\n");

			$high_i++;
		     }

		print("</table>\n");

		print_footer();
	     }
     }
?>