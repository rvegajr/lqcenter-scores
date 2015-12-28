<?
  require("config.php");
  require("functions.php");

  if($_GET["eventId"] != "" && $_GET["game_type"] != "" && isset($_COOKIE[lqc_scores_session]))
    {
	verify_session();

	$eventId = $_GET["eventId"];
	$game_type = $_GET["game_type"];

	$event_pack_sets = fetch_pack_sets($eventId,$game_type);

	$game_format = fetch_game_format($eventId,$game_type);
	$format_num = fetch_format_num($game_format,$event_pack_sets);

	$num_f = round($event_pack_sets/$format_num);

	print_header("Admin",0);

	$fetch_games = mysql_query("SELECT * FROM `web_games` WHERE event='$eventId' AND type='$game_type' ORDER BY `time`, `id` ASC");

	if(mysql_num_rows($fetch_games) == "0")
	    {
		print("<p align=\"center\"><b>No games found</b></p>\n\n<p align=\"center\"><a href=\"./schedule.php?getType=$game_type&getEvent=$eventId\">Click here to continue</a></p>\n\n");
		print("<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=./schedule.php?getType=$game_type&getEvent=$eventId\">\n\n");
	     }
	else
	    {
	      //Delete previous entries for event under this game type

		$rank_games_col = $game_type . "_games";
		$rank_score_col = $game_type . "_score";
		$rank_pts_col = $game_type . "_pts";

		mysql_query("UPDATE `web_teams` SET $rank_games_col = 0, $rank_score_col = 0, $rank_pts_col = 0 WHERE team_event='$eventId'");
	     }

	while($gameRow = mysql_fetch_assoc($fetch_games))
	    {
		$num_i = 1;
		$num_c = 1;

		$game_id = $gameRow[id];

		//print("Game ID: $game_id - Pack Sets: $event_pack_sets<br><br>\n");

		for($pack_sets = 1; $pack_sets < $event_pack_sets+1; $pack_sets++)
		    {
			$team_col_name = "team" . $pack_sets;
			$deduc_col_name = "team" . $pack_sets . "_deduc";
			$score_col_name = "team" . $pack_sets . "_score";
			$pts_col_name = "team" . $pack_sets . "_pts";

			$team_id = $gameRow[$team_col_name];
			$team_deduction = $gameRow[$deduc_col_name];
			$team_points = $gameRow[$pts_col_name];

			$check_scores = mysql_query("SELECT score_id FROM `web_scores` WHERE score_team='$team_id' AND score_game='$game_id'");

			if(mysql_num_rows($check_scores) != "0")
			    {
			      //Fetch total score (scores + individual deductions) for game

				$score_query = mysql_query("SELECT SUM(score_score + score_deduc) AS total_score FROM `web_scores` WHERE score_team='$team_id' AND score_game='$game_id'");
				$scoreRow = mysql_fetch_assoc($score_query);
				$total_score = $scoreRow[total_score];

				if($total_score == "") $team_score = 0;
				elseif($total_score != "0") $team_score = $total_score-$team_deduction;
				else $team_score = $total_score;

			      //Update team score for this game

				$update_score = mysql_query("UPDATE `web_games` SET $score_col_name='$team_score' WHERE id='$game_id'");

				if(!$update_score) show_error("Unable to update score");

				//print("Team: $team_id | Score: $total_score | Team Points: $team_points<br>");

			      //Fetch total score & update in team ranks

				$find_rank = mysql_query("SELECT * FROM `web_teams` WHERE team_id='$team_id' AND team_event='$eventId' LIMIT 1");

				$rank_data = mysql_fetch_assoc($find_rank);
				$tmrk_id = $rank_data[tmrk_id];

				$old_games = $rank_data[$rank_games_col];
				$old_score = $rank_data[$rank_score_col];
				$old_pts = $rank_data[$rank_pts_col];

				$new_games = $old_games+1;
				$new_score = $old_score+$total_score;
				$new_pts = $old_pts+$team_points;

				mysql_query("UPDATE `web_teams` SET $rank_games_col = $new_games, $rank_score_col = $new_score, $rank_pts_col = $new_pts WHERE `web_teams`.`team_id` = $team_id LIMIT 1");

				//print("Old Games: $old_games - Old Score: $old_score - Old Points: $old_pts || New Games: $new_games - New Score: $new_score - New Points: $new_pts<br><br>");

				if($format_num == "1")
				    {
					$rank_array[$team_col_name] = $total_score;
				     }
				else
				    {
					$rank_array[$num_c][$team_col_name] = $total_score;
				     }
			     }
			else
			    {
				//print("Team $team_id has no scores from this game<br>\n");
			     }

			if($num_i == $format_num && $num_c < $num_f)
			    {
				$num_i = 1;
				$num_c++;
			     }
			else
			    {
				$num_i++;
			     }
		     }

		//Perform rankings

		if(isset($rank_array))
		    {
			if($format_num == "1")
			    {
				$t = 1;

				arsort($rank_array);

				foreach($rank_array as $team_col_name=>$column_data)
				    {
					$rank_col_name = $team_col_name . "_rank";

					//print("Team Col Name: $team_col_name - Score: $column_data - New Rank: $t<br>");
					//print("Updating '$rank_col_name' with value of $t for game id $game_id<br><br>");

					mysql_query("UPDATE `web_games` SET $rank_col_name='$t' WHERE id='$game_id' LIMIT 1");

					$t++;
				     }
			     }
			else
			    {
				for($rank_sets = 1; $rank_sets < $num_c+1; $rank_sets++)
				    {
					$t = 1;

					arsort($rank_array[$rank_sets]);

					//print("Rank set: $rank_sets<br>");

					foreach($rank_array[$rank_sets] as $team_col_name=>$column_data)
					    {
						$rank_col_name = $team_col_name . "_rank";

						//print("Team Col Name: $team_col_name - Score: $column_data - New Rank: $t<br>");
						//print("Updating '$rank_col_name' with value of $t for game id $game_id<br><br>");

						mysql_query("UPDATE `web_games` SET $rank_col_name='$t' WHERE id='$game_id' LIMIT 1");

						$t++;
					     }
				     }
			     }
		     }

		unset($rank_array);
	     }

	$prelim_drops = fetch_prelim_drops($eventId);

	if($game_type == "pre" && $prelim_drops == "1")
	    {
		print("<p align=\"center\"><b>Drops Performed</b></p>\n\n");

		$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId'");

		while($teamRow = mysql_fetch_assoc($fetch_teams))
		     {
			$team_id = $teamRow[team_id];

			$teams[] = $team_id;

			$fetch_players = mysql_query("SELECT player_id FROM `web_players` WHERE player_teams LIKE '%[$team_id]%'");

			while($player_row = mysql_fetch_assoc($fetch_players))
			    {
				$player_id = $player_row[player_id];

				$fetch_max = mysql_query("SELECT MAX(score_score) AS max_score FROM `web_scores` WHERE score_event='$eventId' AND score_game_type='pre' AND score_player='$player_id'");
				$maxRow = mysql_fetch_assoc($fetch_max);
				$max_score = $maxRow[max_score];

				$fetch_min = mysql_query("SELECT MIN(score_score) AS min_score FROM `web_scores` WHERE score_event='$eventId' AND score_game_type='pre' AND score_player='$player_id'");
				$minRow = mysql_fetch_assoc($fetch_min);
				$min_score = $minRow[min_score];

				$score_drop = $max_score + $min_score;

				$drop_array[$team_id][] = $score_drop;

				//print("Player ID: $player_id - Team ID: $team_id - Max: $max_score - Min: $min_score - Total: $score_drop<br>");
			     }
		      }
	     }

	if(isset($drop_array) && isset($teams))
	    {
		foreach($teams as &$team_id)
		    {
			$drop_sum = array_sum($drop_array[$team_id]);

			$update_drops = mysql_query("UPDATE `web_teams` SET pre_drops='$drop_sum' WHERE team_id='$team_id' AND team_event='$eventId' LIMIT 1");

			if(!$update_drops) show_error("Unable to update drops");
		     }

		$update_teams = mysql_query("SELECT team_id, SUM(pre_score - pre_drops) AS drop_score FROM `web_teams` WHERE team_event='$eventId' GROUP BY team_id");

		while($updateRow = mysql_fetch_assoc($update_teams))
		    {
			$team_id = $updateRow[team_id];
			$drop_score = $updateRow[drop_score];

			$update_drop_score = mysql_query("UPDATE `web_teams` SET pre_drop_score='$drop_score' WHERE team_id='$team_id' AND team_event='$eventId' LIMIT 1");

			if(!$update_drop_score) show_error("Unable to update drop score");
		     }
	     }

	if($_GET["onlyPts"] == "true")
	    {
		print("<p align=\"center\"><b>Team Scores & Ranks Updated...</b></p>\n\n");
		print("<p align=\"center\">Redirecting...</p>\n\n");
		print("<p align=\"center\"><a href=\"./schedule.php?getType=$game_type&getEvent=$eventId\">Click here to return to schedule</a></p>\n\n");
		print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./schedule.php?getType=$game_type&getEvent=$eventId\">\n\n");
	     }
	else
	    {
		print("<p align=\"center\"><b>Team Scores & Ranks Updated... Updating tower ranks</b></p>\n\n");
		print("<p align=\"center\">Redirecting...</p>\n\n");
		print("<p align=\"center\"><a href=\"./schedule.php?getType=$game_type&getEvent=$eventId\">Click here to return to schedule</a></p>\n\n");
		print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./tower_rank.php?game_type=$game_type&eventId=$eventId\">\n\n");
	     }
     }
  else
    {
	print_header("Admin",0);
	show_error("405 Method Not Allowed");
	print_footer();
     }
?>