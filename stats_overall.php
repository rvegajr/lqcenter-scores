<?php

  require("config.php");
  require("functions.php");

  print_header("LQCenter","Overall Stats");

  foreach($_GET as $key=>$value)
    {
	${$key} = $value;
     }

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
	$fetch_sort = "asc";
	$title_sort = "Bottom";
     }
  elseif($sort == "desc")
    {
	$fetch_sort = "desc";
	$title_sort = "Top";
     }
  else
    {
	$fetch_sort = "desc";
	$title_sort = "Top";
     }

  print("<table width=\"570\" cellspacing=\"1\" cellpadding=\"2\" class=\"table_bl_top\" align=\"center\">\n");
  print("  <tr>\n");
  print("   <td><b>Overall $title_sort $fetch_limit Scores of All-time</b></td>\n");
  print(" </tr>\n");
  print("</table>\n\n");
  print("<table width=\"570\" cellspacing=\"1\" cellpadding=\"2\" bgcolor=\"#aaaaaa\" align=\"center\">\n");
  print("  <tr bgcolor=\"#ffffff\">\n");
  print("   <td height=\"22\" colspan=\"8\" class=\"small\">View Top: <a href=\"./stats_overall.php?sort=desc&limit=25\">25</a> &bull; <a href=\"./stats_overall.php?sort=desc&limit=50\">50</a> &bull; <a href=\"./stats_overall.php?sort=desc&limit=100\">100</a> | View Bottom: <a href=\"./stats_overall.php?sort=asc&limit=25\">25</a> &bull; <a href=\"./stats_overall.php?sort=asc&limit=50\">50</a> &bull; <a href=\"./stats_overall.php?sort=asc&limit=100\">100</a></td>\n");
  print(" </tr>\n");
  print("  <tr height=\"22\" bgcolor=\"#ededed\">\n");
  print("   <td width=\"20\">&nbsp;</td>\n");
  print("   <td width=\"110\">Codename</td>\n");
  print("   <td width=\"60\" align=\"center\">Rank</td>\n");
  print("   <td width=\"60\" align=\"center\">Score</td>\n");
  print("   <td width=\"140\" align=\"center\">Tournament</td>\n");
  print("   <td width=\"60\" align=\"center\">Type</td>\n");
  print("   <td width=\"60\" align=\"center\">Image</td>\n");
  print("   <td width=\"60\" align=\"center\">Game</td>\n");
  print(" </tr>\n");

  $high_i = 1;

  $fetch_highest_scores = mysql_query("SELECT * FROM `web_scores` ORDER BY `score_score` $fetch_sort LIMIT 0, $fetch_limit") or die(mysql_error());

  if(mysql_num_rows($fetch_highest_scores) == "0")
    {
	print("  <tr bgcolor=\"#ffffff\">\n");
	print("   <td colspan=\"40\" align=\"center\" class=\"small\">No data to display</td>\n");
	print(" </tr>\n");
     }

  while($fetch_high_row = mysql_fetch_assoc($fetch_highest_scores))
    {
	foreach($fetch_high_row as $key=>$value)
	    {
		${$key} = $value;
	     }

  	$player_codename = return_codename($score_player);

	$high_game_type = strtoupper($score_game_type);

	$score_event_name = return_event_title($score_event);

	print("  <tr bgcolor=\"#ffffff\">\n");
	print("   <td align=\"center\">$high_i</td>\n");
	print("   <td><a href=\"./stats_player.php?playerId=$score_player\">$player_codename</a></td>\n");
	print("   <td align=\"center\">$score_rank</td>\n");
	print("   <td align=\"center\">$score_score</td>\n");
	print("   <td align=\"center\">$score_event_name</td>\n");
	print("   <td align=\"center\">$high_game_type</td>\n");

	if($fetch_high_row[score_scanned_image] != "")
	    {
		print("   <td align=\"center\"><a href=\"./scanned/$score_scanned_image\" target=\"_blank\">View</a></td>\n");
	     }
	else
	    {
		print("   <td align=\"center\">-</td>\n");
	     }

	print("   <td align=\"center\"><a href=\"./schedule.php?getEvent=$score_event&getGame=$score_game&getType=$score_game_type\">View</a></td>\n");
	print(" </tr>\n");

	$high_i++;
     }

  print("</table>\n");

  print_footer();

?>