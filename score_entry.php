<?php

  require("config.php");
  require("functions.php");

  verify_session();

  verify_action(pm_score_entry);

  if($_POST['submit'])
    {
	foreach($_POST as $key=>$value )
	    {
		${$key} = $value;
	     }

	$gameInfo = fetch_game_info($getEvent,$getType);

	$fetch_game = mysql_query("SELECT * FROM `web_games` WHERE event='$getEvent' AND id='$getGame' LIMIT 1");

	if(mysql_num_rows($fetch_game) == "0")
	    {
		show_error("Failed to load game");
		print_footer();
		exit;
	     }
	else
	     {
		$gameRow = mysql_fetch_assoc($fetch_game);
	     }

	if($entryType == "full")
	    {
		$score_array = array('score_beams', 'score_ratio', 'score_rank', 'score_color', 'score_score', 'score_gft', 'score_gbk', 'score_glr', 'score_gsh', 'score_lft', 'score_lbk', 'score_llr', 'score_lsh', 'score_deduc', 'score_scanned_image');
	     }
	else
	    {
		$score_array = array('score_color', 'score_rank', 'score_score', 'score_deduc');
	     }

	print_header("Admin","Score Entry");

	for($teamRows = 1; $teamRows < $gameInfo[game_pack_sets]+1; $teamRows++)
	   {
		$team_col = "team" . $teamRows;
		$set_team = $gameRow[$team_col];

		$fetch_players = mysql_query("SELECT * FROM `web_players` WHERE player_teams LIKE '%[$set_team]%'");

		if(mysql_num_rows($fetch_players) == "0")
		    {
			//show_error("No players on team");
		     }
		while($player_row = mysql_fetch_assoc($fetch_players))
		    {
			$player_id = $player_row[player_id];

			$player_codename = return_alias($player_id,$set_team);

			$score_if_exist = mysql_query("SELECT * FROM `web_scores` WHERE score_game='$getGame' AND score_team='$set_team' AND score_player='$player_id'");

			if(mysql_num_rows($score_if_exist) == "0")
			    {
				foreach($score_array as $array_index=>$score_key)
				    {
					//$score_key is the name of the field

					$score_value = $player_data[$set_team][$player_id][$score_key];

					if($score_value != "-")
					    {
						$insert_values[$score_key] = $score_value;
					     }
				     }

				$insert_value_count = count($insert_values);

				if($insert_value_count != "1")
				    {
					$insert_value_i = 1;

					$insert_query_fields .= "(`score_event`, `score_player`, `score_team`, `score_game`, `score_game_type`, `score_scanned_image`, ";
					$insert_query_values .= "('$getEvent', '$player_id', '$set_team', '$getGame', '$getType', '', ";

					foreach($insert_values as $insert_index=>$insert_value)
					    {
						$insert_query_fields .= "`$insert_index`";

						$insert_query_values .= "'$insert_value'";

						if($insert_value_i < $insert_value_count)
						    {
							$insert_query_fields .= ", ";
							$insert_query_values .= ", ";

							$insert_value_i++;
						     }
					     }

					$insert_query_fields .= ")";
					$insert_query_values .= ")";

					//print("$insert_query_fields / $insert_query_values<br><br>");

					$insert_query = mysql_query("INSERT INTO `web_scores` $insert_query_fields VALUES $insert_query_values") or die(mysql_error());

					$insert_query_fields = "";
					$insert_query_values = "";
					unset($insert_values);
				    }
			     }
			else
			    {
				$data_row = mysql_fetch_assoc($score_if_exist);

				foreach($score_array as $array_index=>$score_key)
				    {
					//$score_key is the name of the field

					$score_value = $player_data[$set_team][$player_id][$score_key];

					if($data_row[$score_key] != $score_value)
					    {
						$update_query = mysql_query("UPDATE `web_scores` SET `$score_key` = '$score_value' WHERE `score_id` = '$data_row[score_id]' LIMIT 1");
					     }
				     }
			     }
		     }
	     }

	mysql_query("UPDATE `web_games` SET `score_entry` = '1' WHERE `id` = '$getGame' LIMIT 1");

	if($rank_scores == "true")
	    {
		print("<p align=\"center\"><b>Scores Entered... Updating ranks</b></p>\n\n");
		print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./rank_game.php?getGame=$getGame&getType=$getType&getEvent=$getEvent\">\n\n");
	     }
	else
	    {
		print("<p align=\"center\"><b>Scores Entered... Updating teams</b></p>\n\n");
		print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./rank_team_scores.php?game_type=$getType&eventId=$getEvent\">\n\n");
	     }

	print("<p align=\"center\">Redirecting...</p>\n\n");
	print("<p align=\"center\"><a href=\"./score_entry.php?getEvent=$getEvent&getGame=$getGame&getType=$getType&entryType=$entryType\">Click here to return to game</a></p>\n\n");

	print_footer();
     }
  elseif($_GET["getEvent"] != "" && $_GET["getGame"] != "" && $_GET["getType"] != "")
    {
	$getEvent = $_GET["getEvent"];
	$getGame = $_GET["getGame"];
	$getType = $_GET["getType"];

	print_header("Admin","Score Entry");

	verify_event_pm($getEvent);

	$gameInfo = fetch_game_info($getEvent,$getType);

	$hex_array = fetch_hex_array($gameInfo[game_pack_sets],$gameInfo[game_format]);
	$soft_hex_array = fetch_soft_hex_array($gameInfo[game_pack_sets],$gameInfo[game_format]);

	$rgm_array = fetch_rgm_array($gameInfo[game_pack_sets],$gameInfo[game_format]);

	/*
	$gameInfo[title]
	$gameInfo[game_pack_sets]
	$gameInfo[game_title]
	$gameInfo[game_format]
	$gameInfo[format_num]
	*/

	$fetch_game = mysql_query("SELECT * FROM `web_games` WHERE event='$getEvent' AND id='$getGame' LIMIT 1");

	if(mysql_num_rows($fetch_game) == "0")
	    {
		show_error("Failed to load game");
		print_footer();
		exit;
	     }
	else
	     {
		$gameRow = mysql_fetch_assoc($fetch_game);
	     }

	if($_GET["entryType"] == "full")
	    {
?>

<table width="830" cellspacing="1" cellpadding="2" align="center" bgcolor="#aaaaaa">
  <tr bgcolor="#f6f6f6">
   <td height="30" colspan="16" align="center" class="large"><b><?=$gameInfo[title];?></b> - <?=$gameInfo[game_title]?> Score Entry</td>
 </tr>
  <tr bgcolor="#ebebeb">
   <td colspan="16" align="center" class="small"><a href="./schedule.php?getEvent=<?=$getEvent?>&getType=<?=$getType?>">Return to Schedule</a>  &nbsp; &bull; &nbsp;  <a href="./score_entry.php?getEvent=<?=$getEvent?>&getGame=<?=$getGame?>&getType=<?=$getType?>">Switch to Basic Mode</a>  &nbsp; &bull; &nbsp; <a href="./score_entry.php?getEvent=<?=$getEvent?>&getGame=<?=$getGame?>&getType=<?=$getType?>&entryType=full&scanField=true">Enable Scanned Fields</a></td>
 </tr>
  <tr bgcolor="#ffffff">
   <td rowspan="2"><b>Player</b></td>
   <td align="center" rowspan="2"><b>Beams</b></td>
   <td align="center" rowspan="2"><b>Ratio</b></td>
   <td align="center" rowspan="2"><b>Rank</b></td>
   <td align="center" rowspan="2"><b>Color</b></td>
   <td align="center" rowspan="2"><b>Score</b></td>
   <td align="center" colspan="4"><b>You Tagged</b></td>
   <td align="center" colspan="4"><b>Tagged You</b></td>
   <td align="center" rowspan="2"><b>Deduc</b></td>
   <td align="center" rowspan="2"><b>Twr<br>Rk</b></td>
 </tr>
  <tr bgcolor="#ffffff">
   <td align="center"><b>Ft</b></td>
   <td align="center"><b>Bk</b></td>
   <td align="center"><b>Lr</b></td>
   <td align="center"><b>Sh</b></td>
   <td align="center"><b>Ft</b></td>
   <td align="center"><b>Bk</b></td>
   <td align="center"><b>Lr</b></td>
   <td align="center"><b>Sh</b></td>
 </tr>
<form method="POST" action="./score_entry.php"><input type="hidden" name="getEvent" value="<?=$getEvent;?>"><input type="hidden" name="getGame" value="<?=$getGame;?>"><input type="hidden" name="getType" value="<?=$getType;?>"><input type="hidden" name="entryType" value="full">
<?
	     }
	else
	    {
?>
<table width="430" cellspacing="1" cellpadding="2" align="center" bgcolor="#aaaaaa">
  <tr bgcolor="#f6f6f6">
   <td height="30" colspan="5" align="center" class="large"><b><?=$gameInfo[title];?></b> - <?=$gameInfo[game_title]?> Score Entry</td>
 </tr>
  <tr bgcolor="#ebebeb">
   <td colspan="5" align="center" class="small"><a href="./schedule.php?getEvent=<?=$getEvent?>&getType=<?=$getType?>">Return to Schedule</a>  &nbsp; &bull; &nbsp;  <a href="./score_entry.php?getEvent=<?=$getEvent?>&getGame=<?=$getGame?>&getType=<?=$getType?>&entryType=full">Switch to Full Mode</a></td>
 </tr>
  <tr bgcolor="#ffffff">
   <td><b>Player</b></td>
   <td align="center"><b>Rank</b></td>
   <td align="center"><b>Score</b></td>
   <td align="center"><b>Deduc</b></td>
   <td align="center"><b>Twr Rk</b></td>
 </tr>
<form method="POST" action="./score_entry.php"><input type="hidden" name="getEvent" value="<?=$getEvent;?>"><input type="hidden" name="getGame" value="<?=$getGame;?>"><input type="hidden" name="getType" value="<?=$getType;?>"><input type="hidden" name="entryType" value="basic">
<?
	     }

	if($gameInfo[format_num] == "1")
	    {
		$twrLabels = 1;
	     }
	else
	    {
		$twrLabels = round($gameInfo[game_pack_sets]/$gameInfo[format_num])+1;
	     }

	$twrLabel_row = 0;
	$twrLabel_int = 1;

	for($teamRows = 1; $teamRows < $gameInfo[game_pack_sets]+1; $teamRows++)
	   {
		$team_col = "team" . $teamRows;
		$set_team = $gameRow[$team_col];
		$team_name = team_name($set_team);

		if($twrLabel_row == "0" && $twrLabel_int == "1" || $twrLabel_row == $gameInfo[format_num] && $twrLabel_int < $twrLabels)
		    {
			print("  <tr bgcolor=\"#f6f6f6\">\n");
			print("   <td colspan=\"16\" height=\"20\" class=\"small\"><b>Tower $twrLabel_int</b></td>\n");
			print(" </tr>\n");

			$twrLabel_row = 1;
			$twrLabel_int++;
		     }
		else
		    {
			$twrLabel_row++;
		     }

		print("  <tr bgcolor=\"$hex_array[$teamRows]\">\n");
		print("   <td height=\"30\" colspan=\"16\"><b>$team_name</b></td>\n");
		print(" </tr>\n");

		$color1 = "#ffffff";
		$color2 = $soft_hex_array[$teamRows];

		$row_count = 1;

		$fetch_players = mysql_query("SELECT * FROM `web_players` WHERE player_teams LIKE '%[$set_team]%'");

		if(mysql_num_rows($fetch_players) == "0")
		    {
			//show_error("No players on team");
		     }
		while($player_row = mysql_fetch_assoc($fetch_players))
		    {
			$player_id = $player_row[player_id];

			$row_color = ($row_count % 2) ? $color1 : $color2;

			$player_codename = return_alias($player_id,$set_team);

			$score_if_exist = mysql_query("SELECT * FROM `web_scores` WHERE score_game='$getGame' AND score_team='$set_team' AND score_player='$player_id'");

			if(mysql_num_rows($score_if_exist) == "0")
			    {
				print("  <tr bgcolor=\"$row_color\">\n");

				if($_GET["entryType"] == "full")
				    {
					print("   <td><b>$player_codename</b></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_beams]\" size=\"3\" maxlength=\"4\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_ratio]\" size=\"1\" maxlength=\"2\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_rank]\" size=\"1\" maxlength=\"2\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><select class=\"clear\" name=\"player_data[$set_team][$player_id][score_color]\"><option value=\"$rgm_array[$teamRows]\" selected>");
					print(ucfirst($rgm_array[$teamRows]));
					print("</option><option value=\"red\">Red</option><option value=\"green\">Green</option><option value=\"mixed\">Mixed</option></select></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_score]\" size=\"3\" maxlength=\"4\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_gft]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_gbk]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_glr]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_gsh]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_lft]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_lbk]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_llr]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_lsh]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_deduc]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\">$data_row[score_twr_rank]</td>\n");

					if($_GET["scanField"] == "true")
					    {
						print(" </tr>\n");
						print("  <tr bgcolor=\"$row_color\">\n");
						print("   <td>&nbsp;</td>\n");
						print("   <td colspan=\"15\"><input type=\"text\" name=\"player_data[$set_team][$player_id][score_scanned_image]\" size=\"50\" value=\"-\"></td>\n");
					     }
					else
					    {
						print("<input type=\"hidden\" name=\"player_data[$set_team][$player_id][score_scanned_image]\" value=\"-\">\n");
					     }
				     }
				else
				    {
					print("   <td><b>$player_codename</b></td>\n");
					print("   <td align=\"center\"><input type=\"hidden\" name=\"player_data[$set_team][$player_id][score_color]\" value=\"$rgm_array[$teamRows]\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_rank]\" size=\"3\" maxlength=\"4\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_score]\" size=\"3\" maxlength=\"4\" value=\"-\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_deduc]\" size=\"1\" maxlength=\"3\" value=\"-\"></td>\n");
					print("   <td align=\"center\">-</td>\n");
				     }

				print(" </tr>\n");
			     }
			else
			    {
				$data_row = mysql_fetch_assoc($score_if_exist);

				print("  <tr bgcolor=\"$row_color\">\n");

				if($_GET["entryType"] == "full")
				    {
					print("   <td><b>$player_codename</b></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_beams]\" size=\"3\" maxlength=\"4\" value=\"$data_row[score_beams]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_ratio]\" size=\"1\" maxlength=\"2\" value=\"$data_row[score_ratio]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_rank]\" size=\"1\" maxlength=\"2\" value=\"$data_row[score_rank]\"></td>\n");
					print("   <td align=\"center\"><select class=\"clear\" name=\"player_data[$set_team][$player_id][score_color]\"><option value=\"$data_row[score_color]\" selected>");
					print(ucfirst($data_row[score_color]));
					print("</option><option value=\"red\">Red</option><option value=\"green\">Green</option><option value=\"mixed\">Mixed</option></select></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_score]\" size=\"3\" maxlength=\"4\" value=\"$data_row[score_score]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_gft]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_gft]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_gbk]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_gbk]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_glr]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_glr]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_gsh]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_gsh]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_lft]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_lft]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_lbk]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_lbk]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_llr]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_llr]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_lsh]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_lsh]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_deduc]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_deduc]\"></td>\n");
					print("   <td align=\"center\">$data_row[score_twr_rank]</td>\n");

					if($data_row[score_scanned_image] != "")
					    {
						print(" </tr>\n");
						print("  <tr bgcolor=\"$row_color\">\n");
						print("   <td>&nbsp;</td>\n");
						print("   <td colspan=\"15\"><input type=\"text\" name=\"player_data[$set_team][$player_id][score_scanned_image]\" size=\"50\" value=\"$data_row[score_scanned_image]\"></td>\n");
					     }
					elseif($_GET["scanField"] == "autofill")
					    {
						$autofill_date_a = date("Ymd", $gameRow[time]);
						//$autofill_date_b = date("Ymd_Hi", $gameRow[time]);
						$autofill_date_b = date("Hi", $gameRow[time]);

						$autofill_rank_count = strlen($data_row[score_rank]);

						if($autofill_rank_count < 2)
						     {
							$autofill_rank = "0" . "$data_row[score_rank]";
						      }
						else
						     {
							$autofill_rank = "$data_row[score_rank]";
						      }

						$autofill_value = $autofill_date_a . "/" . $autofill_date_b . "_Page_" . $autofill_rank . ".jpg";

						print(" </tr>\n");
						print("  <tr bgcolor=\"$row_color\">\n");
						print("   <td>&nbsp;</td>\n");
						print("   <td colspan=\"15\"><input type=\"text\" name=\"player_data[$set_team][$player_id][score_scanned_image]\" size=\"50\" value=\"$autofill_value\"></td>\n");
					     }
					elseif($_GET["scanField"] == "true")
					    {
						print(" </tr>\n");
						print("  <tr bgcolor=\"$row_color\">\n");
						print("   <td>&nbsp;</td>\n");
						print("   <td colspan=\"15\"><input type=\"text\" name=\"player_data[$set_team][$player_id][score_scanned_image]\" size=\"50\" value=\"-\"></td>\n");
					     }
					else
					    {
						print("<input type=\"hidden\" name=\"player_data[$set_team][$player_id][score_scanned_image]\" value=\"-\">\n");
					     }
				     }
				else
				    {
					print("   <td><b>$player_codename</b></td>\n");
					print("   <td align=\"center\"><input type=\"hidden\" name=\"player_data[$set_team][$player_id][score_color]\" value=\"$data_row[score_color]\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_rank]\" size=\"3\" maxlength=\"4\" value=\"$data_row[score_rank]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_score]\" size=\"3\" maxlength=\"4\" value=\"$data_row[score_score]\"></td>\n");
					print("   <td align=\"center\"><input class=\"clear\" type=\"text\" name=\"player_data[$set_team][$player_id][score_deduc]\" size=\"1\" maxlength=\"3\" value=\"$data_row[score_deduc]\"></td>\n");
					print("   <td align=\"center\">$data_row[score_twr_rank]</td>\n");
				     }

				print(" </tr>\n");
			     }

			$row_count++;
		     }
	     }

	print("  <tr bgcolor=\"#f6f6f6\">\n");
	print("   <td height=\"30\" colspan=\"16\" align=\"center\"><table border=\"0\" cellspacing=\"3\" cellpadding=\"0\"><tr><td><input type=\"checkbox\" name=\"rank_scores\" value=\"true\" class=\"clear\"></td><td class=\"small\">Check this box to rank scores (for games without provided <b>overall</b> game rank)</td></tr></table></td>\n");
	print(" </tr>\n");
	print("  <tr bgcolor=\"#ebebeb\">\n");
	print("   <td height=\"25\" colspan=\"16\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Submit\"></form></td>\n");
	print(" </tr>\n");
	print("</table>\n\n");

	print_footer();
     }
?>