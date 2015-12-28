<?php

  require("config.php");
  require("functions.php");

  $scriptFilename = $_SERVER["PHP_SELF"];

  $pageAnalytics = 1;

  $pageTitle = "Player Requests";

  include $inc_header;

  if($_POST["submit"])
    {
	foreach($_POST as $key=>$val)
	    {
		${$key} = $val;
	     }

	$ip = $_SERVER['REMOTE_ADDR'];

	if($do == "merge")
	    {
		if($playerA == $playerB)
		    {
			print("<p class=\"notifyError\">You have selected the same players, please return to the previous page and check your selection</p>\n");
			include $inc_footer;
			exit;
		     }
		else
		    {
			$insertQuery = mysql_query("INSERT INTO `request_merge` (`req_stamp`, `req_ip`, `req_correct`, `req_incorrect`) VALUES (UNIX_TIMESTAMP(), '$ip', '$playerA', '$playerB')");

			if(!$insertQuery)
			    {
				print("<p class=\"notifyError\">The system was unable to process your request, please try again later.</p>\n");
				include $inc_footer;
				exit;
			     }
			else
			    {
				print("<p class=\"notifySuccess\">Your request has been submitted. <a href=\"$scriptFilename\">Click here</a> to submit another request.</p>\n");
				include $inc_footer;
				exit;
			     }
		     }
	     }
	elseif($do == "center")
	    {
		$insertQuery = mysql_query("INSERT INTO `request_center` (`req_stamp`, `req_ip`, `req_player`, `req_center`) VALUES (UNIX_TIMESTAMP(), '$ip', '$player', '$center')");

		if(!$insertQuery)
		    {
			print("<p class=\"notifyError\">The system was unable to process your request, please try again later.</p>\n");
			include $inc_footer;
			exit;
		     }
		else
		    {
			print("<p class=\"notifySuccess\">Your request has been submitted. <a href=\"$scriptFilename\">Click here</a> to submit another request.</p>\n");
			include $inc_footer;
			exit;
		     }
	     }
	elseif($do == "codename")
	    {
		$insertQuery = mysql_query("INSERT INTO `request_codename` (`req_stamp`, `req_ip`, `req_player`, `req_codename`) VALUES (UNIX_TIMESTAMP(), '$ip', '$player', '$codename')");

		if(!$insertQuery)
		    {
			print("<p class=\"notifyError\">The system was unable to process your request, please try again later.</p>\n");
			include $inc_footer;
			exit;
		     }
		else
		    {
			print("<p class=\"notifySuccess\">Your request has been submitted. <a href=\"$scriptFilename\">Click here</a> to submit another request.</p>\n");
			include $inc_footer;
			exit;
		     }
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

	function optionOneArray($singleArray)
	    {
		foreach($singleArray as $singleKey=>$singleValue)
		    {
			print("<option value=\"$singleKey\">$singleValue</option>");
		     }
	     }
?>
<div id="boxTitle">Request players to be merged</div>
<div id="clear"></div>

<form method="post" action="<?=$scriptFilename?>">
<input type="hidden" name="do" value="merge">
<table border="0" width="750" align="center">
  <tr height="100">
   <td>Correct Player</td>
   <td><select name="playerA"><?=optionPlayerArray($playerArray,$playerCenter);?></select></td>
   <td>Incorrect Player</td>
   <td><select name="playerB"><?=optionPlayerArray($playerArray,$playerCenter);?></select></td>
   <td><input type="submit" name="submit" value="Submit"></td>
 </tr>
</table>
</form>

<div id="boxTitle">Request player center to be changed</div>
<div id="clear"></div>

<form method="post" action="<?=$scriptFilename?>">
<input type="hidden" name="do" value="center">
<table border="0" width="590" align="center">
  <tr height="100">
   <td>Player</td>
   <td><select name="player"><?=optionPlayerArray($playerArray,$playerCenter);?></select></td>
   <td>Correct Center</td>
   <td><select name="center"><?=optionOneArray($centerArray);?></select></td>
   <td><input type="submit" name="submit" value="Submit"></td>
 </tr>
</table>
</form>

<div id="boxTitle">Request player name to be changed</div>
<div id="clear"></div>

<form method="post" action="<?=$scriptFilename?>">
<input type="hidden" name="do" value="codename">
<table border="0" width="590" align="center">
  <tr height="100">
   <td>Player</td>
   <td><select name="player"><?=optionPlayerArray($playerArray,$playerCenter);?></select></td>
   <td>New codename</td>
   <td><input type="text" name="codename" maxlength="10"></td>
   <td><input type="submit" name="submit" value="Submit"></td>
 </tr>
</table>
</form>

<?
     }

  include $inc_footer;

?>