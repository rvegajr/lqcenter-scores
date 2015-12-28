<?
  require("config.php");
  require("functions.php");

  if($_GET["eventId"] != "" && $_GET["game_type"] != "" && $_GET["print"] == "standings")
    {
	$eventId = $_GET["eventId"];
	$game_type = $_GET["game_type"];

	$fetch_event = mysql_query("SELECT * FROM `web_events` WHERE id='$eventId' LIMIT 1");

	if(mysql_num_rows($fetch_event) == "0")
	    {
		show_error("Unable to load data");
		exit;
	     }

	$game_title = fetch_game_title($eventId,$game_type);

	$eventRow = mysql_fetch_assoc($fetch_event);

	$team_advance = $eventRow[playoff_advance1];

	if($_GET["cycle"] == "true" )
	    {
		printSimpleHeader($eventId,"$game_title Standings");
		print("\n<meta http-equiv=\"refresh\" content=\"6; URL=./schedule.php?getEvent=$eventId&getType=$game_type&printPage=cycle\">\n\n");
	     }
	else
	    {
		print_header($eventId,"$game_title Standings");
	     }

?>
<table width="550" cellspacing="1" cellpadding="2" align="center" bgcolor="#dadada">
<?
  if($game_type == "pre")
    {
	print("  <tr bgcolor=\"#ffffff\" height=\"25\">\n   <td colspan=\"5\" align=\"center\" class=\"print\">Top <b>$team_advance</b> teams advance to playoffs</td>\n </tr>\n");
     }
?>
  <tr bgcolor="#ffffff">
   <td width="50" align="center" class=\"print\"><b>Rank</b></td>
   <td width="200" align="center" class=\"print\"><b>Team</b></td>
   <td width="75" align="center" class=\"print\"><b>Points</b></td>
   <td width="125" align="center" class=\"print\"><b>Score</b></td>
   <td width="100" align="center" class=\"print\"><b>Games Played</b></td>
 </tr>
<?
	$rank_games_col = $game_type . "_games";
	$rank_score_col = $game_type . "_score";
	$rank_pts_col = $game_type . "_pts";

	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY `$rank_pts_col` DESC, `$rank_score_col` DESC");

	//$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY `web_team_ranks`.`$rank_pts_col` DESC, `web_team_ranks`.`$rank_score_col` DESC");

	if(mysql_num_rows($fetch_teams) == "0")
	    {
		print("	  <tr bgcolor=\"#ffffff\" height=\"100\">\n");
		print("	   <td align=\"center\" colspan=\"6\" class=\"large\"><b>Standings not available</td>\n");
		print("	 </tr>\n");
		print("</table>\n");

		print_footer();
		exit;
	     }

	$row_count = 1;

	while($rank_row = mysql_fetch_assoc($fetch_teams))
	    {
		$team_score = number_format($rank_row[$rank_score_col]);
		$team_games = $rank_row[$rank_games_col];

		if($team_games != "0")
		    {
			if($game_type == "pre" && $row_count < $team_advance+1)
			    {
				print("	  <tr bgcolor=\"#e1e1e1\">\n");
			     }
			else
			    {
				print("	  <tr bgcolor=\"#ffffff\">\n");
			     }

			print("	   <td align=\"center\" class=\"print\">$row_count</td>\n");
			print("	   <td align=\"center\" class=\"print\">");
			print_team_name($rank_row[team_id]);
			print("</td>\n");
			print("	   <td align=\"center\" class=\"print\">$rank_row[$rank_pts_col]</td>\n");
			print("	   <td align=\"center\" class=\"print\">$team_score</td>\n");
			print("	   <td align=\"center\" class=\"print\">$team_games</td>\n");
			print("	 </tr>\n");

			$row_count++;
		     }
	    }

	if($row_count == "1")
	    {
		print("	  <tr bgcolor=\"#ffffff\" height=\"35\">\n");
		print("	   <td align=\"center\" colspan=\"6\" class=\"print\"><b>Standings not available</td>\n");
		print("	 </tr>\n");
	     }

	print("</table>\n");

	print_footer();
     }
  elseif($_GET["game_type"] != "" && $_GET["eventId"] != "")
    {
	$game_type = $_GET["game_type"];
	$eventId = $_GET["eventId"];

	print_header($eventId,"Team Scores");

	$prelim_drops = fetch_prelim_drops($eventId);
	$prelim_rankby = fetch_prelim_rankby($eventId);

?>
<table width="660" cellspacing="1" cellpadding="2" align="center" bgcolor="#dadada">
  <tr bgcolor="#ffffff">
   <td width="60" align="center"><b>Rank</b></td>
   <td width="200" align="center"><b>Team</b></td>
   <td width="100" align="center"><b>Points</b></td>
   <td width="100" align="center"><b>Score</b></td>
   <td width="100" align="center"><b>Drops</b></td>
   <td width="100" align="center"><b>Games Played</b></td>
 </tr>
<?

	$rank_games_col = $game_type . "_games";
	$rank_score_col = $game_type . "_score";
	$rank_pts_col = $game_type . "_pts";

	if($game_type == "pre" && $prelim_drops == "1")
	    {
		$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY `pre_drop_score` DESC");
	     }
	else
	    {
		$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY `$rank_pts_col` DESC, `$rank_score_col` DESC");
	     }

	if(mysql_num_rows($fetch_teams) == "0")
	    {
		print("  <tr bgcolor=\"#ffffff\" height=\"35\">\n");
		print("   <td align=\"center\" colspan=\"6\"><b>Standings not available</td>\n");
		print(" </tr>\n");
		print("</table>\n");

		print_footer();
		exit;
	     }

	$row_count = 1;

	while($rank_row = mysql_fetch_assoc($fetch_teams))
	    {
		//$team_name = team_name($rank_row[team_id]);
		$team_score = number_format($rank_row[$rank_score_col]);

		$pre_drops = number_format($rank_row[pre_drop_score]);
		$team_games = $rank_row[$rank_games_col];

		if($team_games != "0")
		    {
			if($game_type == "pre" && $row_count < $team_advance+1)
			    {
				print("	  <tr bgcolor=\"#e1e1e1\">\n");
			     }
			else
			    {
				print("	  <tr bgcolor=\"#ffffff\">\n");
			     }

			print("	   <td align=\"center\">$row_count</td>\n");
			print("	   <td align=\"center\">");
			print_team_name($rank_row[team_id]);
			print("</td>\n");

			if($prelim_rankby == "points")
			    {
				print("	   <td align=\"center\">$rank_row[$rank_pts_col]</td>\n");
			     }
			else
			    {
				print("	   <td align=\"center\">-</td>\n");
			     }

			print("	   <td align=\"center\">$team_score</td>\n");

			if($game_type == "pre" && $prelim_drops == "1")
			    {
				print("	   <td align=\"center\">$pre_drops</td>\n");
			     }
			else
			    {
				print("	   <td align=\"center\">-</td>\n");
			     }

			print("	   <td align=\"center\">$team_games</td>\n");
			print("	 </tr>\n");

			$row_count++;
		     }
	    }

	if($row_count == "1")
	    {
		print("	  <tr bgcolor=\"#ffffff\" height=\"35\">\n");
		print("	   <td align=\"center\" colspan=\"6\"><b>Standings not available</td>\n");
		print("	 </tr>\n");
	     }

	print("</table>\n");

	print_footer();
     }
  else
    {
	print_header("Error",0);
	show_error("405 Method Not Allowed");
	print_footer();
     }
?>