<?php

  require("config.php");
  require("functions.php");

  $scriptFilename = $_SERVER["PHP_SELF"];

  $pageAnalytics = 0;

  $pageTitle = "Player Requests";

  include $inc_header;

?>
<p><a href="<?=$scriptFilename?>?do=cleanup">Player Cleanup</a></p>
<?

  if($_POST["submit"])
    {
     }
  elseif($_GET["playerA"] != "" && $_GET["playerB"] != "")
    {
	$playerA = $_GET["playerA"];
	$playerB = $_GET["playerB"];

	if($playerA == $playerB)
	    {
		print("<p class=\"notifyError\">You have selected the same players, please return to the previous page and check your selection</p>\n");
		include $inc_footer;
		exit;
	     }

	$fetchInfo = mysql_query("SELECT `player_codename`, `player_teams` FROM `web_players` WHERE `player_id` = '$playerB'");

	if(mysql_num_rows($fetchInfo) == "0")
	    {
		print("<p class=\"notifyError\">The system was unable to process your request</p>\n");
	     }
	else
	    {
		$infoRow = mysql_fetch_assoc($fetchInfo);

		$player_teams = $infoRow[player_teams];

		$fetchPrimary = mysql_query("SELECT `player_teams` FROM `web_players` WHERE `player_id` = '$playerA' LIMIT 1");

		if(mysql_num_rows($fetchPrimary) == "0")
		    {
			print("<p class=\"notifyError\">The incorrect player was not found or was not assigned to any teams and has been deleted.</p>\n");
		     }
		else
		    {
			$primaryRow = mysql_fetch_assoc($fetchPrimary);

			$primaryTeams = $primaryRow[player_teams];

			$newTeams = $primaryTeams . $player_teams;

			$updateTeams = mysql_query("UPDATE `web_players` SET `player_teams` = '$newTeams' WHERE `player_id` = '$playerA' LIMIT 1");

			$updateScores = mysql_query("UPDATE `web_scores` SET `score_player` = '$playerA' WHERE `score_player` = '$playerB'");

			$updateRanks = mysql_query("UPDATE `web_ranks` SET `player_id` = '$playerA' WHERE `player_id` = '$playerB'");

			$updateAlias = mysql_query("UPDATE `web_alias` SET `player_id` = '$playerA' WHERE `player_id` = '$playerB'");

			print("<p class=\"notifySuccess\">Player has been updated.</p>\n");
		     }

		$deleteSecondary = mysql_query("DELETE FROM `web_players` WHERE `player_id` = '$playerB' LIMIT 1");
	     }
     }
  elseif($_GET["delete"] != "" && $_GET["from"] != "")
    {
	$req_id = $_GET["delete"];
	$table = $_GET["from"];

	$deleteQuery = mysql_query("DELETE FROM `$table` WHERE `req_id` = '$req_id' LIMIT 1");

	if(!$deleteQuery)
	    {
		print("<p class=\"notifyError\">The system was unable to process your request</p>\n");
	     }
	else
	    {
		print("<p class=\"notifySuccess\">The system removed players that were not assigned to teams</p>\n");
	     }
     }
  elseif($_GET["do"] == "cleanup")
    {
	$deleteQuery = mysql_query("DELETE FROM `web_players` WHERE `player_teams` = ''");

	if(!$deleteQuery)
	    {
		print("<p class=\"notifyError\">The system was unable to process your request</p>\n");
	     }
	else
	    {
		print("<p class=\"notifySuccess\">The system removed players that were not assigned to teams</p>\n");
	     }
     }
  else
    {
	$fetchPlayers = mysql_query("SELECT `web_centers`.`center_title`, `web_players`.* FROM `web_centers`, `web_players` WHERE `web_centers`.`center_number` = `web_players`.`player_center` ORDER BY `web_players`.`player_codename` ASC") or die(mysql_error());

	if(mysql_num_rows($fetchPlayers) == "0")
	    {
		$playerArray[] = "Not Available";
		$playerCenter[] = "Not Available";
	     }
	while($playerRow = mysql_fetch_assoc($fetchPlayers))
	    {
		foreach($playerRow as $key=>$value)
		    {
			${$key} = $value;
		     }

		//center_title 	player_id 	player_center 	player_codename

		$playerArray[$player_id] = $player_codename;
		$playerCenter[$player_id] = $center_title;
	     }

	$fetchCenters = mysql_query("SELECT `center_number`, `center_title` FROM `web_centers` ORDER BY `center_title` ASC");

	if(mysql_num_rows($fetchCenters) == "0")
	    {
		$centerArray[] = "Not Available";
	     }
	while($centerRow = mysql_fetch_assoc($fetchCenters))
	    {
		foreach($centerRow as $key=>$value)
		    {
			${$key} = $value;
		     }

		$centerArray[$center_number] = $center_title;
	     }

	function optionPlayerArray($playerArray,$playerCenter)
	    {
		foreach($playerArray as $player_id=>$player_codename)
		    {
			print("<option value=\"$player_id\">$player_codename ($playerCenter[$player_id])</option>");
		     }
	     }

	$fetchMerge = mysql_query("SELECT * FROM `request_merge` ORDER BY `req_stamp` DESC");

	if(mysql_num_rows($fetchMerge) == "0")
	    {
		print("No merge requests<br>\n");
	     }
	while($mergeRow = mysql_fetch_assoc($fetchMerge))
	    {
		$req_id = $mergeRow[req_id];
		$req_correct = $mergeRow[req_correct];
		$req_incorrect = $mergeRow[req_incorrect];

		print("<a href=\"./stats_player.php?playerId=$req_incorrect\">$playerArray[$req_incorrect]</a> ($playerCenter[$req_incorrect]) merge into <a href=\"./stats_player.php?playerId=$req_correct\">$playerArray[$req_correct]</a> ($playerCenter[$req_correct]) <a href=\"$scriptFilename?playerA=$req_correct&playerB=$req_incorrect\">Merge</a> <a href=\"$scriptFilename?delete=$req_id&from=request_merge\">Delete</a><br>\n");
	     }

	$fetchCenter = mysql_query("SELECT * FROM `request_center` ORDER BY `req_stamp` DESC");

	if(mysql_num_rows($fetchCenter) == "0")
	    {
		print("No center requests<br>\n");
	     }
	while($centerRow = mysql_fetch_assoc($fetchCenter))
	    {
		$req_id = $mergeRow[req_id];
		$req_player = $centerRow[req_player];
		$req_center = $centerRow[req_center];

		print("<a href=\"./stats_player.php?playerId=$req_player\">$playerArray[$req_player]</a> ($playerCenter[$req_player]) center changed to $centerArray[$req_center] <a href=\"#\">Change</a> <a href=\"$scriptFilename?delete=$req_id&from=request_center\">Delete</a><br>\n");
	     }
     }

  include $inc_footer;

?>