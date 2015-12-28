<?
  require("config.php");
  require("functions.php");

  if($_GET["playerId"] != "")
    {
	$playerId = $_GET["playerId"];

	$game_type_array = array('pre','pla','con','fin');

	$center_ref = array('C' => 'Central', 'E' => 'East', 'W' => 'West', 'S' => 'South', 'N' => 'North');

	function print_table_header($game_type)
	    {
		$game_type_ref = array('pre' => 'Prelim', 'pla' => 'Playoff', 'con' => 'Console', 'fin' => 'Finals');

?>
<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b><?=$game_type_ref[$game_type]?> Stats</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr bgcolor="#ededed">
   <td align="center" width="150"><b>Event</b></td>
   <td align="center" width="50"><b>Beams</b></td>
   <td align="center" width="50"><b>Ratio</b></td>
   <td align="center" width="50"><b>Rank</b></td>
   <td align="center" width="50"><b>Twr<br>Rank</b></td>
   <td align="center" width="50"><b>Color</b></td>
   <td align="center" width="50"><b>Score</b></td>
   <td align="center" width="50"><b>Deduc</b></td>
   <td align="center" width="30"><b>Ft</b></td>
   <td align="center" width="30"><b>Bk</b></td>
   <td align="center" width="30"><b>Lr</b></td>
   <td align="center" width="30"><b>Sh</b></td>
   <td align="center" width="30"><b>Ft</b></td>
   <td align="center" width="30"><b>Bk</b></td>
   <td align="center" width="30"><b>Lr</b></td>
   <td align="center" width="30"><b>Sh</b></td>
   <td align="center" width="50"><b>Image</b></td>
 </tr>
<?
	     }

	$fetch_player = mysql_query("SELECT * FROM `web_players` WHERE player_id='$playerId'");

	if(mysql_num_rows($fetch_player) == "0")
	    {
		print_header("Error",0);
		show_error("Unable to find this player");
	     }
	else
	    {
		//score_id 	 	score_player 	 	score_game 	score_scanned_image 	

		$player_row = mysql_fetch_assoc($fetch_player);

		$display_center = return_center($player_row[player_center]);

		print_header("Player Profile","$player_row[player_codename] ($display_center)");

		foreach($game_type_array as $key => $game_type)
		    {
			$row_count = 1;

			$color1 = "#ffffff";
			$color2 = "#f3f3f3";

			$fetch_scores = mysql_query("SELECT * FROM `web_scores` WHERE score_player='$playerId' AND score_game_type='$game_type' ORDER BY `score_event` ASC");

			if(mysql_num_rows($fetch_scores) != "0")
			    {
				print_table_header($game_type);
			     }

			while($scoreRow = mysql_fetch_assoc($fetch_scores))
			    {
				$row_color = ($row_count % 2) ? $color1 : $color2;
				
				print("  <tr bgcolor=\"$row_color\">\n");
				print("   <td align=\"center\">");
				event_title($scoreRow[score_event]);
				print("</td>\n");


				if($scoreRow[score_beams] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_beams]</td>\n");
					$stats_beams[] = $scoreRow[score_beams];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_ratio] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_ratio]</td>\n");
					$stats_ratio[] = $scoreRow[score_ratio];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_rank] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_rank]</td>\n");
					$stats_rank[] = $scoreRow[score_rank];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_twr_rank] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_twr_rank]</td>\n");
					$stats_twr_rank[] = $scoreRow[score_twr_rank];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				print("   <td align=\"center\">$scoreRow[score_color]</td>\n");

				print("   <td align=\"center\">$scoreRow[score_score]</td>\n");
				if($scoreRow[score_score] != "0") $stats_score[] = $scoreRow[score_score];

				if($scoreRow[score_deduc] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_deduc]</td>\n");
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_gft] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_gft]</td>\n");
					$stats_gft[] = $scoreRow[score_gft];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_gbk] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_gbk]</td>\n");
					$stats_gbk[] = $scoreRow[score_gbk];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_glr] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_glr]</td>\n");
					$stats_glr[] = $scoreRow[score_glr];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_gsh] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_gsh]</td>\n");
					$stats_gsh[] = $scoreRow[score_gsh];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_lft] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_lft]</td>\n");
					$stats_lft[] = $scoreRow[score_lft];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_lbk] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_lbk]</td>\n");
					$stats_lbk[] = $scoreRow[score_lbk];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_llr] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_llr]</td>\n");
					$stats_llr[] = $scoreRow[score_llr];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_lsh] != "0")
				    {
					print("   <td align=\"center\">$scoreRow[score_lsh]</td>\n");
					$stats_lsh[] = $scoreRow[score_lsh];
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				if($scoreRow[score_scanned_image] != "")
				    {
					print("   <td align=\"center\" class=\"small\"><a href=\"./scanned/$scoreRow[score_scanned_image]\">view</a></td>\n");
				     }
				else
				    {
					print("   <td align=\"center\">-</td>\n");
				     }

				print(" </tr>\n");

				$row_count++;
			     }

			if(mysql_num_rows($fetch_scores) != "0")
			    {
				print("</table>\n");

				if(count($stats_beams) != "0") $avg_beams = round(array_sum($stats_beams)/count($stats_beams),1);
				else $avg_beams = "-";

				if(count($stats_ratio) != "0") $avg_ratio = round(array_sum($stats_ratio)/count($stats_ratio),1);
				else $avg_ratio = "-";

				if(count($stats_rank) != "0") $avg_rank = round(array_sum($stats_rank)/count($stats_rank),1);
				else $avg_rank = "-";

				if(count($stats_twr_rank) != "0") $avg_twr_rank = round(array_sum($stats_twr_rank)/count($stats_twr_rank),1);
				else $avg_twr_rank = "-";

				if(count($stats_score) != "0") $avg_score = round(array_sum($stats_score)/count($stats_score),1);
				else $avg_score = "-";

				if(count($stats_gft) != "0") $avg_gft = round(array_sum($stats_gft)/count($stats_gft),1);
				else $avg_gft = "-";

				if(count($stats_gbk) != "0") $avg_gbk = round(array_sum($stats_gbk)/count($stats_gbk),1);
				else $avg_gbk = "-";

				if(count($stats_glr) != "0") $avg_glr = round(array_sum($stats_glr)/count($stats_glr),1);
				else $avg_glr = "-";

				if(count($stats_gsh) != "0") $avg_gsh = round(array_sum($stats_gsh)/count($stats_gsh),1);
				else $avg_gsh = "-";

				if(count($stats_lft) != "0") $avg_lft = round(array_sum($stats_lft)/count($stats_lft),1);
				else $avg_lft = "-";

				if(count($stats_lbk) != "0") $avg_lbk = round(array_sum($stats_lbk)/count($stats_lbk),1);
				else $avg_lbk = "-";

				if(count($stats_llr) != "0") $avg_llr = round(array_sum($stats_llr)/count($stats_llr),1);
				else $avg_llr = "-";

				if(count($stats_lsh) != "0") $avg_lsh = round(array_sum($stats_lsh)/count($stats_lsh),1);
				else $avg_lsh = "-";


?>

<table width="790" cellspacing="1" cellpadding="0" class="table_yl_bot" align="center">
  <tr>
   <td align="right" width="150" class="small">overall average</td>
   <td align="center" width="50" class="small"><?=$avg_beams?></td>
   <td align="center" width="50" class="small"><?=$avg_ratio?></td>
   <td align="center" width="50" class="small"><?=$avg_rank?></td>
   <td align="center" width="50" class="small"><?=$avg_twr_rank?></td>
   <td align="center" width="50">&nbsp;</td>
   <td align="center" width="50" class="small"><?=$avg_score?></td>
   <td align="center" width="50">&nbsp;</td>
   <td align="center" width="30" class="small"><?=$avg_gft?></td>
   <td align="center" width="30" class="small"><?=$avg_gbk?></td>
   <td align="center" width="30" class="small"><?=$avg_glr?></td>
   <td align="center" width="30" class="small"><?=$avg_gsh?></td>
   <td align="center" width="30" class="small"><?=$avg_lft?></td>
   <td align="center" width="30" class="small"><?=$avg_lbk?></td>
   <td align="center" width="30" class="small"><?=$avg_llr?></td>
   <td align="center" width="30" class="small"><?=$avg_lsh?></td>
   <td align="center" width="50">&nbsp;</td>
</table>

<br>

<?
				unset($stats_beams);
				unset($stats_ratio);
				unset($stats_rank);
				unset($stats_twr_rank);
				unset($stats_score);
				unset($stats_gft);
				unset($stats_gbk);
				unset($stats_glr);
				unset($stats_gsh);
				unset($stats_lft);
				unset($stats_lbk);
				unset($stats_llr);
				unset($stats_lsh);
			     }
		     }
	     }

	print_footer();
     }
  else
    {
	print_header("Error",0);
	show_error("405 Method Not Allowed");
	print_footer();
     }
?>