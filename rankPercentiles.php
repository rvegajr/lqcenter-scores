<?php

  require("conf.php");
  require("func.php");

  $scriptFilename = $_SERVER["PHP_SELF"];
  $pageAnalytics = 0;
  $pageTitle = "Rank Percentiles";

  include $inc_header;

  //if($_GET["getEvent"] != "" && is_numeric($_GET["getEvent"]) && $_GET["getType"] != "" && isset($_COOKIE[lqc_scores_session]))
  if($_GET["getEvent"] != "" && is_numeric($_GET["getEvent"]) && $_GET["getType"] != "")
    {
	//verifySession();

	$getEvent = $_GET["getEvent"];
	$getType = $_GET["getType"];

	$fetchIndiv = mysql_query("SELECT `rank_player` FROM `rank_indiv` WHERE `rank_event` = '$getEvent' AND `rank_game_type` = '$getType' ORDER BY `rank_avg_twr_rank` ASC");

	if(mysql_num_rows($fetchIndiv) == "0")
	    {
		notifyBox("notifyError","You must first run individual ranks before running this script.",5,"./schedule.php?getEvent=$getEvent&getType=$getType");
	     }

	$i = 1;

	while($indivRow = mysql_fetch_assoc($fetchIndiv))
	    {
		$rankArray[$i] = $indivRow[rank_player];

		$i++;
	     }

	$inArray = count($rankArray);

	if($inArray > 0)
	    {
		$deleteQuery = mysql_query("DELETE FROM `rank_ptile` WHERE `ptile_event` = '$getEvent' AND `ptile_game_type` = '$getType'");

		foreach($rankArray as $rank=>$player)
		    {
			$numBelow = $inArray-$rank;
			$percentileRaw = ($numBelow/$inArray)*100;
			$percentile = round($percentileRaw,2);

			$insertQuery = mysql_query("INSERT INTO `rank_ptile` (`ptile_player`, `ptile_event`, `ptile_game_type`, `ptile_percentile`) VALUES ('$player', '$getEvent', '$getType', '$percentile')");

			if(!$insertQuery)
			    {
				$errorArray[] = 1;
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
     }
  else
    {
	notifyBox("notifyError","You do not have access to this page",0,0);
     }

  include $inc_footer;
?>