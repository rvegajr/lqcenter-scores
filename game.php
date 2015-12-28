<?
  require("config.php");
  require("functions.php");

  if($_GET["eventId"] != "" && $_GET["gameId"] != "" && $_GET["game_type"] != "")
    {
	$eventId = $_GET["eventId"];
	$gameId = $_GET["gameId"];
	$game_type = $_GET["game_type"];

	print_header($eventId,"Game Scores");

	$event_pack_sets = fetch_pack_sets($eventId, $game_type);

	$adj_pack_sets = $event_pack_sets+1;

	$table_colspan = (($event_pack_sets*3));

	if($event_pack_sets < 4)
	    {
		$table_width = (($event_pack_sets*225));
	     }
	else
	    {
		$table_width = (($event_pack_sets*160));
	     }

	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY `team_id` ASC");

	while($row = mysql_fetch_assoc($fetch_teams))
	    {
		$team_id = $row["team_id"];
		$team_name = $row["team_name"];

		$teams[] = $team_id;
		$team_names[$team_id] = $team_name;
	     }

	$rgy_color = array(1 => '#ff5e5e', 2 => '#39ff39', 3 => '#ffef39', 4 => '#ff5e5e', 5 => '#39ff39', 6 => '#ffef39', 7 => '#ff5e5e', 8 => '#39ff39', 9 => '#ffef39');

?>
	<table width="<?=$table_width?>" cellspacing="1" cellpadding="2" align="center" bgcolor="#dadada">
<?
	$fetch_game = mysql_query("SELECT * FROM `web_games` WHERE event='$eventId' AND id='$gameId' LIMIT 1");

	if(mysql_num_rows($fetch_game) == "0")
	    {
		show_error("Failed to load game");
		print_footer();
		exit;
	     }
	else
	     {
		$gameRow = mysql_fetch_assoc($fetch_game);

		print("	  <tr>\n");
		print("	   <td bgcolor=\"#ebebeb\" colspan=\"$table_colspan\"> &nbsp;Game Played on ");
		print(date("F j, Y \a\\t g:i A", $gameRow[time]));
		print("</td>\n");
		print("	 </tr>\n");
		print("	  <tr>\n");

		for($columns = 1; $columns < $event_pack_sets+1; $columns++)
		   {
			$team_col = "team" . $columns;
			$field_team = $gameRow[$team_col];

			print("	   <td width=\"190\" bgcolor=\"$rgy_color[$columns]\" align=\"center\"><b>$team_names[$field_team]</b></td>\n");
		    }

		print("	 </tr>\n	  <tr>\n");

		for($cols = 1; $cols < $adj_pack_sets; $cols++)
		   {
			$team_col = "team" . $cols;
			$field_team = $gameRow[$team_col];

			$fetch_scores = mysql_query("SELECT * FROM `web_scores` WHERE score_event='$eventId' AND score_team='$field_team' AND score_game='$gameId' ORDER BY `score_rank` ASC");

			if(mysql_num_rows($fetch_scores) != "0")
			    {
				print("	   <td bgcolor=\"#ffffff\" valign=\"top\">");
				print("<table border=\"0\" width=\"180\"><tr>");
			     }

			while($scoreRow = mysql_fetch_assoc($fetch_scores))
			    {
				$image = $scoreRow[score_scanned_image];

				print("<td width=\"70\"><a href=\"./stats_player.php?playerId=$scoreRow[score_player]\">");
				player_name($scoreRow[score_player],$field_team);
				print("</a></td>");
				print("<td width=\"25\" align=\"center\">$scoreRow[score_twr_rank]</td>");
				print("<td width=\"25\" align=\"center\">$scoreRow[score_rank]</td>");

				if($image != "")
				    {
					print("<td width=\"40\" align=\"center\">$scoreRow[score_score]</td>");
					print("<td width=\"20\" align=\"center\"><a href=\"./scanned/$image\" target=\"_blank\"><img border=\"0\" src=\"./images/magnify.gif\" width=\"12\" height=\"12\" alt=\"View Score Card\"></a></td>");
				     }
				else
				    {
					print("<td width=\"60\" align=\"center\">$scoreRow[score_score]</td>");
				     }

				if($scoreRow[score_deduc] != "0")
				    {
					print("</tr><tr><td colspan=\"5\" align=\"center\" class=\"small\">Deduction: $scoreRow[score_deduc]</td>");
				     }

				print("</tr><tr>");
			     }

		$fetch_team_total = mysql_query("SELECT SUM(score_score + score_deduc) AS score_score FROM `web_scores` WHERE score_game='$gameId' AND score_team='$field_team'");
		$team_total_row = mysql_fetch_assoc($fetch_team_total);
		$total_score = $team_total_row[score_score];

				print("</tr><tr>");
				print("<td colspan=\"4\" align=\"center\">$total_score</td>");
				print("</tr><tr>");

			if(mysql_num_rows($fetch_scores) != "0")
			    {
				print("</table></td>\n");
			     }
		     }

		print("	 </tr>\n");
	     }

	print("	</table>\n\n");

	print("<p align=\"center\"><b>Key:</b> Codename | Tower Rank | Game Rank | Score</p>\n\n");

	print_footer();
     }
  else
    {
	print_header("Error",0);
	show_error("405 Method Not Allowed");
	print_footer();
     }
?>