<?php

  require("conf.php");
  require("func.php");

  $scriptFilename = $_SERVER["PHP_SELF"];
  $pageAnalytics = 0;
  $pageTitle = "Percentile Ranks";

  include $inc_header;

  if($_GET["getType"] != "")
    {
	$getType = $_GET["getType"];

	$fetchData = mysql_query("SELECT `web_players`.`player_codename` , `rank_ptile` . *, `web_centers`.`center_title` FROM `web_players` , `rank_ptile`, `web_centers` WHERE `web_players`.`player_id` = `rank_ptile`.`ptile_player` AND `web_players`.`player_center` = `web_centers`.`center_number` AND `rank_ptile`.`ptile_game_type` = '$getType'");

	if(mysql_num_rows($fetchData) == "0")
	    {
		notifyBox("notifyError","There is no data available",0,0);
	     }
	while($dataRow = mysql_fetch_assoc($fetchData))
	    {
		foreach($dataRow as $key=>$value)
		    {
			${$key} = $value;
		     }

		$playerData[$ptile_player][] = $ptile_percentile;
		$playerInfo[$ptile_player][center] = $center_title;
		$playerInfo[$ptile_player][name] = $player_codename;
	     }

	$dataCount = count($playerData);

	if($dataCount != "0")
	    {
		foreach($playerData as $playerId=>$playerArray)
		    {
			$average = round(array_sum($playerArray)/count($playerArray),2);

			$dataCount = count($playerData[$playerId]);

			if($dataCount > 2)
			    {
				$averageArray[$playerId] = $average;
			     }
		     }

		arsort($averageArray);

		$i = 1;

		foreach($averageArray as $playerId=>$playerAverage)
		    {
			$player_codename = $playerInfo[$playerId][name];
			$player_center = $playerInfo[$playerId][center];

			print("$i - $player_codename - $playerAverage<br>");

			$i++;
		     }
	     }
     }

  include $inc_footer;
?>