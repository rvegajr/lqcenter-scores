<?php

  require("conf.php");
  require("func.php");

  $scriptFilename = $_SERVER["PHP_SELF"];
  $pageAnalytics = 1;
  $pageTitle = "Individual Stats";

  if($_GET["getEvent"] != "" && $_GET["getType"] != "")
    {
	$getType = $_GET["getType"];
	$getEvent = $_GET["getEvent"];

	$gameArray = fetch_game_info($getEvent,$getType);

	$headerTitle = "<span>$gameArray[game_title] Individual Stats</span> . $gameArray[title]";

	include $inc_header;

	//('title' => $event_title, 'game_pack_sets' => $game_pack_sets, 'game_format' => $game_format, 'game_title' => $game_title, 'format_num' => $format_num, 'center' => $center);

	$count = 1;

	if($_GET["sortBy"] == "rank")
	    {
		$sortQuery = "ORDER BY `rank_indiv`.`rank_avg_rank` ASC";
		$colA = "bgcolor=\"#f1f1f1\"";
		$colB = "";
		$colC = "";
	     }
	elseif($_GET["sortBy"] == "score")
	    {
		$sortQuery = "ORDER BY `rank_indiv`.`rank_avg` DESC";
		$colA = "";
		$colB = "";
		$colC = "bgcolor=\"#f1f1f1\"";
	     }
	else
	    {
		$sortQuery = "ORDER BY `rank_indiv`.`rank_avg_twr_rank` ASC";
		$colA = "";
		$colB = "bgcolor=\"#f1f1f1\"";
		$colC = "";
	     }

	print("<br>\n<table border=\"0\" width=\"700\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\">\n");
	print("  <tr class=\"statsIndiv\" height=\"30\">\n");
	print("   <td align=\"center\">Rank</td>\n");
	print("   <td>Player</td>\n");
	print("   <td>Center</td>\n");
	print("   <td align=\"center\" $colA><a href=\"$scriptFilename?getEvent=$getEvent&getType=$getType&sortBy=rank\">Avg Rank</a></td>\n");
	print("   <td align=\"center\" $colB><a href=\"$scriptFilename?getEvent=$getEvent&getType=$getType\">Avg Twr Rank</a></td>\n");
	print("   <td align=\"center\" $colC><a href=\"$scriptFilename?getEvent=$getEvent&getType=$getType&sortBy=score\">Avg Score</a></td>\n");
	print(" </tr>\n");

	$fetchData = mysql_query("SELECT `web_players`.`player_codename` , `rank_indiv` . *, `web_centers`.`center_title` FROM `web_players` , `rank_indiv`, `web_centers` WHERE `web_players`.`player_id` = `rank_indiv`.`rank_player` AND `web_players`.`player_center` = `web_centers`.`center_number` AND `rank_indiv`.`rank_event` = '$getEvent' AND `rank_indiv`.`rank_game_type` = '$getType' $sortQuery");

	if(mysql_num_rows($fetchData) == "0")
	    {
		print("  <tr>\n");
		print("   <td align=\"center\" colspan=\"6\">There is no data available</td>\n");
		print(" </tr>\n");
	     }
	while($dataRow = mysql_fetch_assoc($fetchData))
	    {
		print("  <tr>\n");
		print("   <td align=\"center\">$count</td>\n");
		print("   <td><a href=\"./stats_player.php?playerId=$dataRow[rank_player]\">$dataRow[player_codename]</a></td>\n");
		print("   <td>$dataRow[center_title]</td>\n");
		print("   <td align=\"center\" $colA>$dataRow[rank_avg_rank]</td>\n");
		print("   <td align=\"center\" $colB>$dataRow[rank_avg_twr_rank]</td>\n");
		print("   <td align=\"center\" $colC>$dataRow[rank_avg]</td>\n");
		print(" </tr>\n");

		$count++;
	     }

	print("</table>\n");
     }
  else
    {
	$getEvent = $_GET["getEvent"];

	include $inc_header;

	//('title' => $event_title, 'game_pack_sets' => $game_pack_sets, 'game_format' => $game_format, 'game_title' => $game_title, 'format_num' => $format_num, 'center' => $center);

	$count = 1;

	if($_GET["sortBy"] == "rank")
	    {
		$sortQuery = "ORDER BY `rank_indiv`.`rank_avg_rank` ASC";
		$colA = "bgcolor=\"#f1f1f1\"";
		$colB = "";
		$colC = "";
	     }
	elseif($_GET["sortBy"] == "score")
	    {
		$sortQuery = "ORDER BY `rank_indiv`.`rank_avg` DESC";
		$colA = "";
		$colB = "";
		$colC = "bgcolor=\"#f1f1f1\"";
	     }
	else
	    {
		$sortQuery = "ORDER BY `rank_indiv`.`rank_avg_twr_rank` ASC";
		$colA = "";
		$colB = "bgcolor=\"#f1f1f1\"";
		$colC = "";
	     }

	print("<br>\n<table border=\"0\" width=\"700\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\">\n");
	print("  <tr class=\"statsIndiv\" height=\"30\">\n");
	print("   <td align=\"center\">Rank</td>\n");
	print("   <td>Player</td>\n");
	print("   <td>Center</td>\n");
	print("   <td align=\"center\" $colA><a href=\"$scriptFilename?getEvent=$getEvent&getType=$getType&sortBy=rank\">Avg Rank</a></td>\n");
	print("   <td align=\"center\" $colB><a href=\"$scriptFilename?getEvent=$getEvent&getType=$getType\">Avg Twr Rank</a></td>\n");
	print("   <td align=\"center\" $colC><a href=\"$scriptFilename?getEvent=$getEvent&getType=$getType&sortBy=score\">Avg Score</a></td>\n");
	print(" </tr>\n");

	$fetchData = mysql_query("SELECT `web_players`.`player_codename` , `rank_indiv` . *, `web_centers`.`center_title` FROM `web_players` , `rank_indiv`, `web_centers` WHERE `web_players`.`player_id` = `rank_indiv`.`rank_player` AND `web_players`.`player_center` = `web_centers`.`center_number` AND `rank_indiv`.`rank_event` = '$getEvent' AND `rank_indiv`.`rank_game_type` = '$getType' $sortQuery");

	if(mysql_num_rows($fetchData) == "0")
	    {
		print("  <tr>\n");
		print("   <td align=\"center\" colspan=\"6\">There is no data available</td>\n");
		print(" </tr>\n");
	     }
	while($dataRow = mysql_fetch_assoc($fetchData))
	    {
		print("  <tr>\n");
		print("   <td align=\"center\">$count</td>\n");
		print("   <td>$dataRow[player_codename]</td>\n");
		print("   <td>$dataRow[center_title]</td>\n");
		print("   <td align=\"center\" $colA>$dataRow[rank_avg_rank]</td>\n");
		print("   <td align=\"center\" $colB>$dataRow[rank_avg_twr_rank]</td>\n");
		print("   <td align=\"center\" $colC>$dataRow[rank_avg]</td>\n");
		print(" </tr>\n");

		$count++;
	     }

	print("</table>\n");
     }

  include $inc_footer;
?>