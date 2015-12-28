<?
  require("config.php");
  require("functions.php");

  $col_array = array("score_beams" => "Beams","score_ratio" => "Ratio","score_rank" => "Rank","score_twr_rank" => "Twr Rank","score_score" => "Score","score_gft" => "Ft (Gain)","score_gbk" => "Bk (Gain)","score_glr" => "Lr (Gain)","score_gsh" => "Sh (Gain)","score_lft" => "Ft (Loss)","score_lbk" => "Bk (Loss)","score_llr" => "Lr (Loss)","score_lsh" => "Sh (Loss)");

  $tag_gain_array = array("score_gft" => "Ft (Gain)","score_gbk" => "Bk (Gain)","score_glr" => "Lr (Gain)","score_gsh" => "Sh (Gain)");
  $tag_loss_array = array("score_lft" => "Ft (Loss)","score_lbk" => "Bk (Loss)","score_llr" => "Lr (Loss)","score_lsh" => "Sh (Loss)");

  $type_array = array("pre" => "Prelim","pla" => "Playoff","con" => "Consoles","fin" => "Finals");

  if($_POST["sidebyside"])
    {
	foreach($_POST as $key=>$value )
	    {
		${$key} = $value;
	     }

	print_header("Player Stats","Side-by-Side");

	if($compare_player1 == "0" || $compare_player2 == "0")
	    {
		fatalError("You must select at least two players");
	     }
	elseif($compare_type == "event" || $compare_type == "eventtype" && $eventId == "0")
	    {
		fatalError("You must select an event for this comparison type");
	     }
	else
	    {
		$compare_array = array($compare_player1, $compare_player2);

		//$compare_player1 / $compare_player2 / $compare_type / $compare_event
		//if($compare_type == "event" || $compare_type = "eventtype")

		$whereQuery = "WHERE `score_player` = '$compare_player1' OR `score_player` = '$compare_player2' ORDER BY `score_player` ASC";

		$fetch_all_scores = mysql_query("SELECT * FROM `web_scores` $whereQuery");

		if(mysql_num_rows($fetch_all_scores) == "0")
		    {
			fatalError("We were unable to process your request, please try different parameters");
		     }
		while($scoreRow = mysql_fetch_assoc($fetch_all_scores))
		    {
			$score_player = $scoreRow[score_player];
			$score_event = $scoreRow[score_event];

			foreach($col_array as $score_key=>$score_title)
			    {
				$column_value = $scoreRow[$score_key];

				if($column_value != "0")
				    {
					$array_overall[$score_player][$score_key][] = $column_value;
				     }
			     }
		     }

		print("<table width=\"300\" cellspacing=\"1\" cellpadding=\"2\" class=\"table_bl_top\" align=\"center\">\n");
		print("  <tr>\n");
		print("   <td><b>Results</b></td>\n");
		print(" </tr>\n");
		print("</table>\n\n");

		$color1 = "#ffffff";
		$color2 = "#f0f0f0";

		$row_count = 0;

		print("<table width=\"300\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"#aaaaaa\" align=\"center\">\n");

		//if($compare_type == "overall")
		//    {
			print("  <tr>\n");
			print("   <td bgcolor=\"#ffffff\">&nbsp;</td>\n");

			foreach($compare_array as $compare_key=>$compare_player)
			    {
				$player_codename = return_codename($compare_player);
				print("   <td bgcolor=\"#ffffff\" align=\"center\">&nbsp; $player_codename</td>\n");
			     }

			print(" </tr>\n");

			foreach($col_array as $col_key=>$col_title)
			    {
				$row_color = ($row_count % 2) ? $color1 : $color2;

				print("  <tr>\n");
				print("   <td bgcolor=\"$row_color\">&nbsp; $col_title</td>\n");

				foreach($compare_array as $compare_key=>$compare_player)
				    {
					$array_count = count($array_overall[$compare_player][$col_key]);

					if($array_count != "0")
					    {
						$array_sum = array_sum($array_overall[$compare_player][$col_key]);

						$col_avg = round($array_sum/$array_count,1);

						print("   <td bgcolor=\"$row_color\" align=\"center\">$col_avg</td>\n");
					     }
					else
					    {
						print("   <td bgcolor=\"$row_color\" align=\"center\">-</td>\n");
					     }
				     }

				print(" </tr>\n");

				$row_count++;
			     }
		//     }

		print("</table>\n\n");
	     }

	print_footer();
     }
  elseif($_GET["playerId"] != "" && is_numeric($_GET["playerId"]) && $_GET["eventId"] != "" && is_numeric($_GET["eventId"]))
    {
	$playerId = $_GET["playerId"];
	$eventId = $_GET["eventId"];
	$graphLabels = $_GET["graphLabels"];

	print_header("Player Stats","Scores by Event");

	$fetch_player_info = mysql_query("SELECT * FROM `web_players` WHERE `player_id` = '$playerId'");

	if(mysql_num_rows($fetch_player_info) == "0")
	    {
		$player_codename = "-";
		$player_center = "-";
		$player_name = "-";
	     }
	else
	    {
		$player_data = mysql_fetch_assoc($fetch_player_info);

		$player_codename = $player_data[player_codename];

		$player_center = return_center($player_data[player_center]);

		if($player_data[player_fname] == "")
		    {
			$player_name = "-";
		     }
		else
		    {
			$player_name = $player_data[player_fname] . " " . $player_data[player_lname];
		     }
	     }

	$event_name = return_event_title($eventId);

	if($graphLabels == "off")
	    {
		$graph_link = "./stats_playerGraph.php?getPlayer=$playerId&eventId=$eventId&graphLabels=off";
		$graph_label_option_link = "./stats_player.php?playerId=$playerId&eventId=$eventId";
		$graph_label_option_title = "Turn Labels On";
	     }
	else
	    {
		$graph_link = "./stats_playerGraph.php?getPlayer=$playerId&eventId=$eventId";
		$graph_label_option_link = "./stats_player.php?playerId=$playerId&eventId=$eventId&graphLabels=off";
		$graph_label_option_title = "Turn Labels Off";
	     }
?>
<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Player Information</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed" height="22">
   <td align="center" width="263">Codename</td>
   <td align="center" width="263">Home Center</td>
   <td align="center" width="263">Full Name</td>
 </tr>
  <tr bgcolor="#ffffff" height="22">
   <td align="center"><?=$player_codename?></td>
   <td align="center"><?=$player_center?></td>
   <td align="center"><?=$player_name?></td>
 </tr>
</table>

<br>

<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b><?=$event_name?> Score Graph</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed">
   <td height="35" align="center">Graph Options: <a href="<?=$graph_label_option_link?>"><?=$graph_label_option_title?></a></td>
 </tr>
  <tr bgcolor="#ffffff">
   <td height="430" align="center"><img src="<?=$graph_link?>" width="700" height="400"></td>
 </tr>
</table>

<br>

<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b><?=$event_name?> Score Details</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed">
   <td align="center" rowspan="2" height="35" width="67"><b>Game</b></td>
   <td align="center" rowspan="2" height="35" width="67"><b>Beams</b></td>
   <td align="center" rowspan="2" height="35" width="67"><b>Ratio</b></td>
   <td align="center" rowspan="2" height="35" width="67"><b>Rank</b></td>
   <td align="center" rowspan="2" height="35" width="67"><b>Twr Rk</b></td>
   <td align="center" rowspan="2" height="35" width="67"><b>Score</b></td>
   <td align="center" colspan="4" class="small">Tags Gained</td>
   <td align="center" colspan="4" class="small">Tags Lost</td>
   <td align="center" rowspan="2" height="35" width="67"><b>Image</b></td>
 </tr>
  <tr bgcolor="#ededed">
   <td align="center" width="40" class="small">Ft</td>
   <td align="center" width="40" class="small">Bk</td>
   <td align="center" width="40" class="small">Lr</td>
   <td align="center" width="40" class="small">Sh</td>
   <td align="center" width="40" class="small">Ft</td>
   <td align="center" width="40" class="small">Bk</td>
   <td align="center" width="40" class="small">Lr</td>
   <td align="center" width="40" class="small">Sh</td>
 </tr>
<?
	foreach($type_array as $type_key=>$type_value)
	    {
		print("  <tr bgcolor=\"#f5f5f5\">\n");
		print("   <td height=\"19\" colspan=\"17\">&nbsp; <b>$type_value</b></td>\n");
		print(" </tr>\n");

		$fetch_scores = mysql_query("SELECT * FROM `web_scores` WHERE `score_player` = '$playerId' AND `score_event` = '$eventId' AND `score_game_type` = '$type_key'");

		if(mysql_num_rows($fetch_scores) == "0")
		    {
			print("  <tr height=\"22\" bgcolor=\"#ffffff\">\n");
			print("   <td height=\"19\" colspan=\"17\" align=\"center\">No scores found</td>\n");
			print(" </tr>\n");
		     }
		while($scoreRow = mysql_fetch_assoc($fetch_scores))
		    {
			print("  <tr height=\"22\" bgcolor=\"#ffffff\">\n");
			print("   <td align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getType=$type_key&getGame=$scoreRow[score_game]&highlightPlayer=$playerId\" class=\"tooltip\" title=\"Click here to view game\">View</a></td>\n");

			$score_game_type = $scoreRow[score_game_type];
			$score_event = $scoreRow[score_event];

			foreach($col_array as $key=>$value)
			    {
				$column_value = $scoreRow[$key];

				if($column_value != "0")
				    {
					print("   <td align=\"center\">$column_value</td>\n");
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }
			     }

			if($scoreRow[score_scanned_image] != "")
			    {
				print("   <td align=\"center\"><a href=\"./scanned/$scoreRow[score_scanned_image]\" target=\"_blank\">View</a></td>\n");
			     }
			else
			    {
				print("   <td align=\"center\">-</td>\n");
			     }

			print(" </tr>\n");
		     }
	     }
?>
</table>
<?
     }
  elseif($_GET["playerId"] != "" && is_numeric($_GET["playerId"]))
    {
	$playerId = $_GET["playerId"];
	$graphLabels = $_GET["graphLabels"];

	print_header("Player Stats","Overall Averages");

	$fetch_player_info = mysql_query("SELECT * FROM `web_players` WHERE `player_id` = '$playerId'");

	if(mysql_num_rows($fetch_player_info) == "0")
	    {
		$player_codename = "-";
		$player_center = "-";
		$player_name = "-";
	     }
	else
	    {
		$player_data = mysql_fetch_assoc($fetch_player_info);

		$player_codename = $player_data[player_codename];

		$player_center = return_center($player_data[player_center]);

		if($player_data[player_fname] == "")
		    {
			$player_name = "-";
		     }
		else
		    {
			$player_name = $player_data[player_fname] . " " . $player_data[player_lname];
		     }
	     }


	if($graphLabels == "off")
	    {
		$graph_link = "./stats_playerGraph.php?getPlayer=$playerId&graphLabels=off";
		$graph_label_option_link = "./stats_player.php?playerId=$playerId";
		$graph_label_option_title = "Turn Labels On";
	     }
	else
	    {
		$graph_link = "./stats_playerGraph.php?getPlayer=$playerId";
		$graph_label_option_link = "./stats_player.php?playerId=$playerId&graphLabels=off";
		$graph_label_option_title = "Turn Labels Off";
	     }

?>
<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Player Information</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed" height="22">
   <td align="center" width="263">Codename</td>
   <td align="center" width="263">Home Center</td>
   <td align="center" width="263">Full Name</td>
 </tr>
  <tr bgcolor="#ffffff" height="22">
   <td align="center"><?=$player_codename?></td>
   <td align="center"><?=$player_center?></td>
   <td align="center"><?=$player_name?></td>
 </tr>
</table>

<br>
<?

	$fetch_all_scores = mysql_query("SELECT * FROM `web_scores` WHERE `score_player` = '$playerId'");

	if(mysql_num_rows($fetch_all_scores) == "0")
	    {
		fatalError("No scores found for this player");
	     }
	while($scoreRow = mysql_fetch_assoc($fetch_all_scores))
	    {
		$score_game_type = $scoreRow[score_game_type];
		$score_event = $scoreRow[score_event];

		foreach($col_array as $key=>$value)
		    {
			$column_value = $scoreRow[$key];

			if($column_value != "0")
			    {
				$array_overall[$value][] = $column_value;

				$array_byType[$score_game_type][$value][] = $column_value;

				$array_inEvent[$score_event] = 1;
				$array_byEvent[$score_event][$value][] = $column_value;

				$array_byTypeEvent[$score_event][$score_game_type][$value][] = $column_value;
			     }
		     }
	     }
?>
<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Overall Averages</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed">
   <td align="center" rowspan="2" height="35" width="94"><b>Beams</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Ratio</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Rank</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Twr Rk</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Score</b></td>
   <td align="center" colspan="4" class="small">Tags Gained</td>
   <td align="center" colspan="4" class="small">Tags Lost</td>
 </tr>
  <tr bgcolor="#ededed">
   <td align="center" width="40" class="small">Ft</td>
   <td align="center" width="40" class="small">Bk</td>
   <td align="center" width="40" class="small">Lr</td>
   <td align="center" width="40" class="small">Sh</td>
   <td align="center" width="40" class="small">Ft</td>
   <td align="center" width="40" class="small">Bk</td>
   <td align="center" width="40" class="small">Lr</td>
   <td align="center" width="40" class="small">Sh</td>
 </tr>
<?
	print("  <tr height=\"22\" bgcolor=\"#ffffff\">\n");

	foreach($col_array as $col_key=>$col_value)
	    {
		$array_count = count($array_overall[$col_value]);

		if($array_count != "0")
		    {
			$array_sum = array_sum($array_overall[$col_value]);

			$col_avg = round($array_sum/$array_count,1);

			print("   <td align=\"center\">$col_avg</td>\n");
		     }
		else
		    {
			print("   <td align=\"center\">-</td>\n");
		     }
	     }

	print(" </tr>\n");
?>
</table>

<br>

<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Overall Score Graph</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed">
   <td height="35" align="center">Graph Options: <a href="<?=$graph_label_option_link?>"><?=$graph_label_option_title?></a></td>
 </tr>
  <tr bgcolor="#ffffff">
   <td height="430" align="center"><img src="<?=$graph_link?>" width="700" height="400"></td>
 </tr>
</table>

<br>

<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Averages by Game Type</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed">
   <td align="center" rowspan="2" height="35" width="94"><b>Beams</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Ratio</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Rank</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Twr Rk</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Score</b></td>
   <td align="center" colspan="4" class="small">Tags Gained</td>
   <td align="center" colspan="4" class="small">Tags Lost</td>
 </tr>
  <tr bgcolor="#ededed">
   <td align="center" width="40" class="small">Ft</td>
   <td align="center" width="40" class="small">Bk</td>
   <td align="center" width="40" class="small">Lr</td>
   <td align="center" width="40" class="small">Sh</td>
   <td align="center" width="40" class="small">Ft</td>
   <td align="center" width="40" class="small">Bk</td>
   <td align="center" width="40" class="small">Lr</td>
   <td align="center" width="40" class="small">Sh</td>
 </tr>
<?
	foreach($type_array as $type_key=>$type_value)
	    {
		print("  <tr bgcolor=\"#f5f5f5\">\n");
		print("   <td height=\"19\" colspan=\"17\">&nbsp; <b>$type_value</b></td>\n");
		print(" </tr>\n");
		print("  <tr height=\"22\" bgcolor=\"#ffffff\">\n");

		foreach($col_array as $col_key=>$col_value)
		    {
			$array_count = count($array_byType[$type_key][$col_value]);

			if($array_count != "0")
			    {
				$array_sum = array_sum($array_byType[$type_key][$col_value]);

				$col_avg = round($array_sum/$array_count,1);

				print("   <td align=\"center\">$col_avg</td>\n");
			     }
			else
			    {
				print("   <td align=\"center\">-</td>\n");
			     }
		     }

		print(" </tr>\n");
	     }
?>
</table>

<br>

<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Averages by Event</b> &nbsp; <i>Click on event name for player's game scores</i></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed">
   <td align="center" rowspan="2" height="35" width="94"><b>Beams</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Ratio</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Rank</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Twr Rk</b></td>
   <td align="center" rowspan="2" height="35" width="94"><b>Score</b></td>
   <td align="center" colspan="4" class="small">Tags Gained</td>
   <td align="center" colspan="4" class="small">Tags Lost</td>
 </tr>
  <tr bgcolor="#ededed">
   <td align="center" width="40" class="small">Ft</td>
   <td align="center" width="40" class="small">Bk</td>
   <td align="center" width="40" class="small">Lr</td>
   <td align="center" width="40" class="small">Sh</td>
   <td align="center" width="40" class="small">Ft</td>
   <td align="center" width="40" class="small">Bk</td>
   <td align="center" width="40" class="small">Lr</td>
   <td align="center" width="40" class="small">Sh</td>
 </tr>
<?
	foreach($array_inEvent as $eventId=>$event_value)
	    {
		$event_name = return_event_title($eventId);

		print("  <tr bgcolor=\"#f5f5f5\">\n");
		print("   <td height=\"19\" colspan=\"17\">&nbsp; <a href=\"./stats_player.php?playerId=$playerId&eventId=$eventId\"><b>$event_name</b></a></td>\n");
		print(" </tr>\n");
		print("  <tr height=\"22\" bgcolor=\"#ffffff\">\n");

		foreach($col_array as $col_key=>$col_value)
		    {
			$array_count = count($array_byEvent[$eventId][$col_value]);

			if($array_count != "0")
			    {
				$array_sum = array_sum($array_byEvent[$eventId][$col_value]);

				$col_avg = round($array_sum/$array_count,1);

				print("   <td align=\"center\">$col_avg</td>\n");
			     }
			else
			    {
				print("   <td align=\"center\">-</td>\n");
			     }
		     }

		print(" </tr>\n");
	     }
?>
</table>

<br>

<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Averages by Event, Game Type</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed">
   <td align="center" rowspan="2" height="35" width="85"><b>Event</b></td>
   <td align="center" rowspan="2" height="35" width="85"><b>Beams</b></td>
   <td align="center" rowspan="2" height="35" width="85"><b>Ratio</b></td>
   <td align="center" rowspan="2" height="35" width="85"><b>Rank</b></td>
   <td align="center" rowspan="2" height="35" width="85"><b>Twr Rk</b></td>
   <td align="center" rowspan="2" height="35" width="85"><b>Score</b></td>
   <td align="center" colspan="4" class="small">Tags Gained</td>
   <td align="center" colspan="4" class="small">Tags Lost</td>
 </tr>
  <tr bgcolor="#ededed">
   <td align="center" width="35" class="small">Ft</td>
   <td align="center" width="35" class="small">Bk</td>
   <td align="center" width="35" class="small">Lr</td>
   <td align="center" width="35" class="small">Sh</td>
   <td align="center" width="35" class="small">Ft</td>
   <td align="center" width="35" class="small">Bk</td>
   <td align="center" width="35" class="small">Lr</td>
   <td align="center" width="35" class="small">Sh</td>
 </tr>
<?
	foreach($array_inEvent as $eventId=>$event_value)
	    {
		$event_name = return_event_title($eventId);

		print("  <tr bgcolor=\"#f5f5f5\">\n");
		print("   <td height=\"19\" colspan=\"17\">&nbsp; <b>$event_name</b></td>\n");
		print(" </tr>\n");

		foreach($type_array as $type_key=>$type_value)
		    {
			print("  <tr height=\"22\" bgcolor=\"#ffffff\">\n");
			print("   <td>&nbsp; $type_value</td>\n");

			foreach($col_array as $col_key=>$col_value)
			    {
				$array_count = count($array_byTypeEvent[$eventId][$type_key][$col_value]);

				if($array_count != "0")
				    {
					$array_sum = array_sum($array_byTypeEvent[$eventId][$type_key][$col_value]);

					$col_avg = round($array_sum/$array_count,1);

					print("   <td align=\"center\">$col_avg</td>\n");
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }
			     }

			print(" </tr>\n");
		     }
	     }
?>
</table>
<?
	print_footer();
     }
  else
    {
	print_header("Player Stats",0);
?>
<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Side-by-side Player Comparison</b></td>
 </tr>
</table>

<form method="POST" action="./stats_player.php">
<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr height="22" bgcolor="#ededed">
   <td colspan="2">&nbsp; Select Players</td>
   <td>&nbsp; Comparison Type, Overall by:</td>
   <td colspan="2">&nbsp; Event <i>(if by event)</i></td>
 </tr>
  <tr height="35" bgcolor="#ffffff">
   <td align="center"><select name="compare_player1"><?=player_list();?></select></td>
   <td align="center"><select name="compare_player2"><?=player_list();?></select></td>
   <td align="center"><select name="compare_type" disabled><option value="overall" selected>Averages</option><option value="gametype">Game Type</option><option value="event">Event</option><option value="eventtype">Event, Game Type</option></select></td>
   <td align="center"><select name="compare_event" disabled><?=return_event_list();?></select></td>
   <td align="center"><input type="submit" name="sidebyside" value="Compare"></td>
 </tr>
</table>
</form>
<?
	print_footer();
     }
?>