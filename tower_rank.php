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

	$fetch_games = mysql_query("SELECT * FROM `web_games` WHERE event='$eventId' AND type='$game_type' AND score_entry='1'");

	while($gameRow = mysql_fetch_array($fetch_games))
	    {
		$gameId = $gameRow[id];

		//print("Game ID: $gameId<br>Pack Sets: $event_pack_sets<br><br>");

		$num_i = 1;
		$num_c = 1;

		for($pack_sets = 1; $pack_sets < $event_pack_sets+1; $pack_sets++)
		   {
			$team_col_name = "team" . $pack_sets;
			$team_id = $gameRow[$team_col_name];

			//print("$team_id - ");

			$fetch_score = mysql_query("SELECT score_id, score_rank FROM `web_scores` WHERE score_event='$eventId' AND score_team='$team_id' AND score_game='$gameId'");

			while($scoreRow = mysql_fetch_array($fetch_score))
			    {
				$score_id = $scoreRow[score_id];
				$score_rank = $scoreRow[score_rank];

				//print("$score_id = $score_rank - ");

				if($format_num == "1")
				    {
					$rank_array[$score_id] = $score_rank;
				     }
				else
				    {
					$rank_array[$num_c][$score_id] = $score_rank;
				     }
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

			//print("<br>");
		    }

		//print("format_num = $format_num<br>num_f = $num_f<br>num_c = $num_c<br>num_i = $num_i<br><br>");

		//print_r($rank_array);

		if($format_num == "1")
		    {
			$t = 1;

			asort($rank_array);

			foreach($rank_array as $score_id=>$column_data)
			    {
				//print("Score ID: $score_id - Rank: $column_data - New Rank: $t<br><br>");

				mysql_query("UPDATE `web_scores` SET score_twr_rank='$t' WHERE score_id='$score_id' LIMIT 1");

				$t++;
			     }
		     }
		else
		    {
			for($rank_sets = 1; $rank_sets < $num_c+1; $rank_sets++)
			    {
				$t = 1;

				asort($rank_array[$rank_sets]);

				//print("Rank set: $rank_sets<br>");

				foreach($rank_array[$rank_sets] as $score_id=>$column_data)
				    {
					//print("Score ID: $score_id - Rank: $column_data - New Rank: $t<br><br>");

					mysql_query("UPDATE `web_scores` SET score_twr_rank='$t' WHERE score_id='$score_id' LIMIT 1");

					$t++;
				     }
			     }
		     }

		//print("<br><br>");

		unset($rank_array);
	     }

	print("<p align=\"center\"><b>Tower Ranks Updated</b></p>\n\n");
	print("<p align=\"center\">You will automatically be redirected</p>\n\n");
	print("<p align=\"center\"><a href=\"./schedule.php?getType=$game_type&getEvent=$eventId\">Click here to return to schedule</a></p>\n\n");
	print("<META HTTP-EQUIV=Refresh CONTENT=\"2; URL=./schedule.php?getType=$game_type&getEvent=$eventId\">\n\n");

	print_footer();
     }
  else
    {
	print_header("Admin",0);
	show_error("405 Method Not Allowed");
	print_footer();
     }
?>