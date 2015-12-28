<?php

  require("config.php");
  require("functions.php");

  $scriptFilename = $_SERVER["PHP_SELF"];

  $pageAnalytics = 0;

  $pageTitle = "Rank Individuals";

  include $inc_header;

  if($_GET["getEvent"] != "" && is_numeric($_GET["getEvent"]) && $_GET["getType"] != "" && isset($_COOKIE[lqc_scores_session]))
    {
	verify_session();

	$getEvent = $_GET["getEvent"];
	$getType = $_GET["getType"];

	$fetchTeams = mysql_query("SELECT `team_id` FROM `web_teams` WHERE `team_event` = '$getEvent'");

	if(mysql_num_rows($fetchTeams) == "0")
	    {
		notifyBox("notifyError","There are no teams for this event. Please do not run this script until the tournament is complete as it is CPU intensive.",5,"./schedule.php?getEvent=$getEvent&getType=$getType");
	     }

	while($teamRow = mysql_fetch_assoc($fetchTeams))
	    {
		$team_id = $teamRow[team_id];

		$fetchPlayers = mysql_query("SELECT `player_id` FROM `web_players` WHERE `player_teams` LIKE '%[$team_id]%'");

		while($playerRow = mysql_fetch_assoc($fetchPlayers))
		    {
			$player_id = $playerRow[player_id];

			$fetchRanks = mysql_query("SELECT `score_rank`, `score_twr_rank`, `score_score` FROM `web_scores` WHERE `score_event` = '$getEvent' AND `score_player` = '$player_id' AND `score_game_type` = '$getType'");

			if(mysql_num_rows($fetchRanks) == "0")
			    {
				$playerGames = "0";
				$playerAvg = "0.0";
				$playerAvgRank = "0.0";
				$playerAvgTwrRank = "0.0";
			     }
			else
			    {
				$playerGames = mysql_num_rows($fetchRanks);
			     }
			while($rankRow = mysql_fetch_assoc($fetchRanks))
			    {
				$rankArray[] = $rankRow[score_rank];
				$twrRankArray[] = $rankRow[score_twr_rank];
				$scoreArray[] = $rankRow[score_score];
			     }

			if($playerGames != "0")
			    {
				$playerAvg = round(array_sum($scoreArray)/count($scoreArray), 1);
				$playerAvgRank = round(array_sum($rankArray)/count($rankArray), 1);
				$playerAvgTwrRank = round(array_sum($twrRankArray)/count($twrRankArray), 1);

				$deleteQuery = mysql_query("DELETE FROM `rank_indiv` WHERE `rank_player` = '$player_id' AND `rank_event` = '$getEvent' AND `rank_game_type` = '$getType'");

				$insertQuery = mysql_query("INSERT INTO `rank_indiv` (`rank_player`, `rank_event`, `rank_game_type`, `rank_games`, `rank_avg`, `rank_avg_rank`, `rank_avg_twr_rank`) VALUES ('$player_id', '$getEvent', '$getType', '$playerGames', '$playerAvg', '$playerAvgRank', '$playerAvgTwrRank')");

				if(!$insertQuery)
				    {
					$errorArray[] = 1;
				     }
			     }

			unset($rankArray);
			unset($twrRankArray);
			unset($scoreArray);
		     }
	     }

	if(count($errorArray) > 0)
	    {
		notifyBox("notifyError","There was one or more errors with your request, it may or may not have been completed.",5,"./schedule.php?getEvent=$getEvent&getType=$getType");
	     }
	else
	    {
		notifyBox("notifySuccess","Your request was completed.",5,"./schedule.php?getEvent=$getEvent&getType=$getType");
	     }
     }
  else
    {
	notifyBox("notifyError","You do not have access to this page",0,0);
     }

  include $inc_footer;
?>