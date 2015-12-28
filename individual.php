<?
  require("config.php");
  require("functions.php");

  if($_GET["eventId"] != "")
    {
	$game_type = $_GET["game_type"];
	$eventId = $_GET["eventId"];

	if($game_type == "pre")
	    {
		$table_title = "Prelim";
	     }
	elseif($game_type == "pla")
	    {
		$table_title = "Playoff";
	     }
	elseif($game_type == "con")
	    {
		$table_title = "Consoles";
	     }
	elseif($game_type == "fin")
	    {
		$table_title = "Finals";
	     }

	print_header($eventId,"$table_title Individual Ranks");

	$col_games = $game_type . "_games";
	$col_avg = $game_type . "_avg";
	$col_avg_rank = $game_type . "_avg_rank";
	$col_avg_twr_rank = $game_type . "_avg_twr_rank";

	if($_GET["sortBy"] == "score")
	    {
		$fetch_ranks = mysql_query("SELECT * FROM `web_ranks` WHERE event_id='$eventId' ORDER BY $col_avg DESC");
		$sort_score = "s_desc.png";
	     }
	elseif($_GET["sortBy"] == "rank")
	    {
		$fetch_ranks = mysql_query("SELECT * FROM `web_ranks` WHERE event_id='$eventId' ORDER BY $col_avg_rank ASC, $col_avg DESC");
		$sort_rank = "s_asc.png";
	     }
	else
	    {
		$fetch_ranks = mysql_query("SELECT * FROM `web_ranks` WHERE event_id='$eventId' ORDER BY $col_avg_twr_rank ASC, $col_avg_rank ASC, $col_avg DESC");
		$sort_twr_rank = "s_asc.png";
	     }

?>
	<table width="660" cellspacing="1" cellpadding="2" align="center" bgcolor="#dadada">
	  <tr bgcolor="#ffffff">
	   <td width="90" align="center"><b>Rank</b></td>
	   <td width="220" align="center"><b>Player</b></td>
	   <td width="50" align="center"><b>Games</b></td>
	   <td width="100" align="center"><a href="./individual.php?game_type=<?=$game_type?>&eventId=<?=$eventId?>&sortBy=rank"><b>Avg Rank</b></a><? if($sort_rank != "") print("<img src=\"./images/$sort_rank\" width=\"11\" height=\"9\"></td>\n"); ?>
	   <td width="100" align="center"><a href="./individual.php?game_type=<?=$game_type?>&eventId=<?=$eventId?>"><b>Avg Twr Rank</b></a><? if($sort_twr_rank != "") print("<img src=\"./images/$sort_twr_rank\" width=\"11\" height=\"9\"></td>\n"); ?>
	   <td width="100" align="center"><a href="./individual.php?game_type=<?=$game_type?>&eventId=<?=$eventId?>&sortBy=score"><b>Avg Score</b></a><? if($sort_score != "") print("<img src=\"./images/$sort_score\" width=\"11\" height=\"9\"></td>\n"); ?>
	 </tr>
<?

	$i = 1;

	while($rankRow = mysql_fetch_assoc($fetch_ranks))
	    {
		$player_id = $rankRow[player_id];

		if($rankRow[$col_games] != "0")
		    {
			print("	  <tr bgcolor=\"#ffffff\">\n");
			print("	   <td align=\"center\">$i</td>\n");
			print("	   <td align=\"center\"><a href=\"./stats_player.php?playerId=$player_id\">");
			player_name($player_id,0);
			print("</a></td>\n");
			print("	   <td align=\"center\">$rankRow[$col_games]</td>\n");
			print("	   <td align=\"center\">$rankRow[$col_avg_rank]</td>\n");
			print("	   <td align=\"center\">$rankRow[$col_avg_twr_rank]</td>\n");
			print("	   <td align=\"center\">$rankRow[$col_avg]</td>\n");
			print("	 </tr>\n");

			$i++;
		     }
	     }

	print("	</table>\n");

	print_footer();
     }
  else
    {
	print_header("Error",0);
	show_error("405 Method Not Allowed");
	print_footer();
     }
?>