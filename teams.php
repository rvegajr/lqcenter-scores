<?
  require("config.php");
  require("functions.php");

  if($_POST['submit'] && isset($_COOKIE[lqc_scores_session]))
    {
	verify_session();

	if($_POST['do'] == "add_team")
	    {
		print_header("Admin","Add Team");

		if($_POST['team_name'] == "" || $_POST['eventId'] == "")
		    {
			show_error("You must fill out all required fields");
		     }
		else
		    {
			$eventId = $_POST['eventId'];
			$team_name = $_POST['team_name'];

			$insert_query = mysql_query("INSERT INTO `web_teams` (`team_event`, `team_name`) VALUES ('$eventId', '$team_name')");

			if(!$insert_query)
			    {
				show_error("Unable to add team");
			     }
			else
			    {
				echo "<p align=\"center\"><b>Team Added</b></p>\n\n";
				echo "<p align=\"center\">You will be automatically redirected...</p>\n\n";
				echo "<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n";
				echo "<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n";
			     }
		     }

		print_footer();
	     }
	elseif($_POST['do'] == "assign_player")
	    {
		print_header("Admin","Assign Player");

		if($_POST['player_id'] == "" || $_POST['team_id'] == "0" || $_POST['eventId'] == "")
		    {
			show_error("You must fill out all required fields");
		     }
		else
		    {
			$eventId = $_POST['eventId'];
			$player_id = $_POST['player_id'];
			$team_id = $_POST['team_id'];

			$check_exist = mysql_query("SELECT * FROM `web_players` WHERE player_id='$player_id' AND player_teams LIKE '%[$team_id]%' LIMIT 1");

			if(mysql_num_rows($check_exist) == "0")
			   {
				$player_teams = mysql_result(mysql_query("SELECT player_teams FROM `web_players` WHERE player_id='$player_id'"),0);

				$add_team = "[" . $team_id . "]";
				$team_add = $player_teams . $add_team;

				$update_player = mysql_query("UPDATE `web_players` SET player_teams='$team_add' WHERE player_id='$player_id'");

				if(!$update_player)
				   {
					show_error("Failed to updated player");
				    }
				else
				   {
					echo "<p align=\"center\"><b>Player Assigned</b></p>\n\n";
					echo "<p align=\"center\">You will be automatically redirected...</p>\n\n";
					echo "<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n";
					echo "<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n";
				    }
			    }
			else
			   {
				show_error("Player already assigned to team");
				echo "<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n";
			    }
		     }

		print_footer();
	     }
	elseif($_POST['do'] == "add_player")
	    {
		print_header("Admin","Add Player");

		if($_POST['player_codename'] == "" || $_POST['player_center'] == "0" || $_POST['eventId'] == "")
		    {
			show_error("You must fill out all required fields");
		     }
		else
		    {
			$eventId = $_POST['eventId'];
			$player_codename = $_POST['player_codename'];
			$player_center = $_POST['player_center'];
			$player_override = $_POST['player_override'];

			$check_exist = mysql_query("SELECT * FROM `web_players` WHERE player_center='$player_center' AND player_codename LIKE '%$player_codename%'");

			if(mysql_num_rows($check_exist) == "0" || $player_override == "true")
			   {
				mysql_query("INSERT INTO `web_players` (`player_center`, `player_codename`, `player_teams`) VALUES ('$player_center', '$player_codename', '')") or die(mysql_error());

				print("<p align=\"center\"><b>Player added</b></p>\n\n");
				print("<p align=\"center\">You will be automatically redirected...</p>\n\n");
				print("<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n");
				print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n");
			    }
			else
			   {
				show_error("Player already exists");
				print("<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n");
			    }
		     }

		print_footer();
	     }
	elseif($_POST['do'] == "add_alias")
	    {
		print_header("Admin","Add Alias");

		if($_POST['player_id'] == "" || $_POST['team_id'] == "0" || $_POST['eventId'] == "")
		    {
			show_error("You must fill out all required fields");
		     }
		else
		    {
			$eventId = $_POST['eventId'];
			$player_id = $_POST['player_id'];
			$team_id = $_POST['team_id'];
			$alias_name = $_POST['alias_name'];

			$check_exist = mysql_query("SELECT * FROM `web_alias` WHERE player_id='$player_id' AND alias_event='$eventId' AND alias_team='$team_id'");

			if(mysql_num_rows($check_exist) == "0")
			    {
				$assign_alias = mysql_query("INSERT INTO `web_alias` (`player_id`, `alias_name`, `alias_event`, `alias_team`) VALUES ('$player_id', '$alias_name', '$eventId', '$team_id')");

				if(!$assign_alias)
				   {
					show_error("Failed to assign alias");
					print("<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n");
				    }
				else
				   {
					print("<p align=\"center\"><b>Alias added</b></p>\n\n");
					print("<p align=\"center\">You will be automatically redirected...</p>\n\n");
					print("<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n");
					print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n");
				    }
			     }
			else
			    {
				if($alias_name != "")
				    {
					$update_alias = mysql_query("UPDATE `web_alias` SET `alias_name` = '$alias_name' WHERE player_id='$player_id' AND alias_event='$eventId' AND alias_team='$team_id' LIMIT 1");

					if(!$update_alias)
					   {
						show_error("Failed to update alias");
						print("<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n");
					    }
					else
					   {
						print("<p align=\"center\"><b>Alias updated</b></p>\n\n");
						print("<p align=\"center\">You will be automatically redirected...</p>\n\n");
						print("<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n");
						print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n");
					    }
				     }
				else
				    {
					$delete_alias = mysql_query("DELETE FROM `web_alias` WHERE player_id='$player_id' AND alias_event='$eventId' AND alias_team='$team_id' LIMIT 1");

					if(!$delete_alias)
					   {
						show_error("Failed to delete alias");
						print("<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n");
					    }
					else
					   {
						print("<p align=\"center\"><b>Alias Deleted</b></p>\n\n");
						print("<p align=\"center\">You will be automatically redirected...</p>\n\n");
						print("<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n");
						print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n");
					    }
				     }
			     }
		     }
		print_footer();
	     }
	elseif($_POST['do'] == "change_team_name")
	    {
		print_header("Admin","Change Team Name");

		if($_POST['team_id'] == "0" || $_POST['team_name'] == "" || $_POST['eventId'] == "")
		    {
			show_error("You must fill out all required fields");
		     }
		else
		    {
			$eventId = $_POST['eventId'];
			$team_id = $_POST['team_id'];
			$team_name = $_POST['team_name'];

			$update_team = mysql_query("UPDATE `web_teams` SET team_name='$team_name' WHERE team_id='$team_id' LIMIT 1");

			if(!$update_team)
			    {
				show_error("Failed to update team name");
			    }
			else
			   {
				echo "<p align=\"center\"><b>Team name changed</b></p>\n\n";
				echo "<p align=\"center\">You will be automatically redirected...</p>\n\n";
				echo "<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n";
				echo "<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n";
			    }
		     }

		print_footer();
	     }
	elseif($_POST['do'] == "change_player_name")
	    {
		foreach($_POST as $key=>$value )
		    {
			${$key} = $value;
		     }

		print_header("Admin","Change Player Codename");

		if($player_codename == "" || $eventId == "")
		    {
			show_error("You must fill out all required fields");
		     }
		else
		    {
			$update_codename = mysql_query("UPDATE `web_players` SET `player_codename` = '$player_codename' WHERE `player_id` = '$player_id' LIMIT 1");

			if(!$update_codename)
			    {
				show_error("Failed to update codename");
			    }
			else
			   {
				echo "<p align=\"center\"><b>Player codename changed</b></p>\n\n";
				echo "<p align=\"center\">You will be automatically redirected...</p>\n\n";
				echo "<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n";
				echo "<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n";
			    }
		     }

		print_footer();
	     }
	elseif($_POST['do'] == "change_player_center")
	    {
		foreach($_POST as $key=>$value )
		    {
			${$key} = $value;
		     }

		print_header("Admin","Change Player Center");

		if($player_center == "-" || $eventId == "")
		    {
			show_error("You must fill out all required fields");
		     }
		else
		    {
			$update_center = mysql_query("UPDATE `web_players` SET `player_center` = '$player_center' WHERE `player_id` = '$player_id' LIMIT 1");

			if(!$update_center)
			    {
				show_error("Failed to update center");
			    }
			else
			   {
				echo "<p align=\"center\"><b>Player center changed</b></p>\n\n";
				echo "<p align=\"center\">You will be automatically redirected...</p>\n\n";
				echo "<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n";
				echo "<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n";
			    }
		     }

		print_footer();
	     }
     }


  // --------------------------------------------
  // Begin Printable Section
  // --------------------------------------------

  elseif($_GET["eventId"] != "" && $_GET["do"] == "print")
    {
	$eventId = $_GET["eventId"];

	if($_GET["cycle"] == "true" )
	    {
		$game_type = $_GET["game_type"];
		printSimpleHeader($eventId,"Rosters");

		if($game_type == "con")
		    {
			print("\n<meta http-equiv=\"refresh\" content=\"6; URL=./team_scores.php?game_type=fin&eventId=$eventId&print=standings&cycle=true\">\n\n");
		     }
		elseif($game_type == "fin")
		    {
			print("\n<meta http-equiv=\"refresh\" content=\"6; URL=./team_scores.php?game_type=con&eventId=$eventId&print=standings&cycle=true\">\n\n");
		     }
		else
		    {
			print("\n<meta http-equiv=\"refresh\" content=\"6; URL=./team_scores.php?game_type=$game_type&eventId=$eventId&print=standings&cycle=true\">\n\n");
		     }
	     }
	else
	    {
		print_header($eventId,"Rosters");
	     }

	print("<table width=\"720\" cellspacing=\"1\" cellpadding=\"3\" align=\"center\" bgcolor=\"#000000\">\n");
	print("  <tr>\n");

	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY `team_name` ASC");

	$teamCount = mysql_num_rows($fetch_teams);

	if($teamCount == "0")
	    {
	     }

	$colCount = 1;
	$rowCount = 1;

	while($teamRow = mysql_fetch_assoc($fetch_teams))
	    {
		$team_id = $teamRow[team_id];

		print("   <td bgcolor=\"#ffffff\" width=\"180\" align=\"center\" class=\"print\"><b>$teamRow[team_name]</b><br><br>\n");

		$fetch_players = mysql_query("SELECT * FROM `web_players` WHERE player_teams LIKE '%[$team_id]%'");

		if(mysql_num_rows($fetch_players) == "0")
		    {
			print("No players found\n");
		     }

		while($playerRow = mysql_fetch_assoc($fetch_players))
		    {
			$player_id = $playerRow[player_id];
			$player_codename = $playerRow[player_codename];
			$player_center = return_center($playerRow[player_center]);

			$check_alias = mysql_query("SELECT * FROM `web_alias` WHERE player_id='$player_id' AND alias_team='$team_id'");

			if(mysql_num_rows($check_alias) == "0")
			    {
				print("$player_codename<br>\n");
			     }
			else
			    {
				$alias_row = mysql_fetch_assoc($check_alias);
				print("$alias_row[alias_name] <i>($player_codename)</i><br>\n");
			     }
		     }

		print("</td>\n");

		if($colCount % 4 == 0)
		    {
			print(" </tr>\n");
			print("  <tr>\n");

			$rowCount++;
		     }
		elseif($teamCount == $colCount)
		    {
			$divRow = floor($rowCount*4);
			$colSpan = $divRow-$colCount;

			print("   <td bgcolor=\"#ffffff\" colspan=\"$colSpan\">&nbsp;</td>\n");
		     }

		$colCount++;
	     }

	print(" </tr>\n");
	print("</table>\n\n");

	print_footer();
     }

  // --------------------------------------------
  // Begin Admin Section
  // --------------------------------------------

  elseif($_GET["do"] != "" && isset($_COOKIE[lqc_scores_session]))
    {
	verify_session();

	if($_GET["do"] == "delete_team" && $_GET["team_id"] != "")
	    {
		$eventId = $_GET['eventId'];
		$team_id = $_GET['team_id'];

		print_header("Admin","Delete Team");

		$fetch_players = mysql_query("SELECT * FROM `web_players` WHERE player_teams LIKE '%[$team_id]%'");

		if(mysql_num_rows($fetch_players) != "0")
		    {
			show_error("You must first delete all players from the team");
		     }
		else
		    {
			$delete_team = mysql_query("DELETE FROM `web_teams` WHERE team_id='$team_id' LIMIT 1");

			if(!$delete_team)
			    {
				show_error("Failed to delete team");
			    }
			else
			   {
				echo "<p align=\"center\"><b>Deleted team</b></p>\n\n";
				echo "<p align=\"center\">You will be automatically redirected...</p>\n\n";
				echo "<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n";
				echo "<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n";
			    }			
		     }

		print_footer();
	     }
	elseif($_GET["do"] == "delete_player" && $_GET["team_id"] != "" && $_GET["player_id"] != "")
	    {
		print_header("Admin","Delete Player");

		$eventId = $_GET['eventId'];
		$team_id = $_GET['team_id'];
		$player_id = $_GET['player_id'];

		$find_player = mysql_query("SELECT * FROM `web_players` WHERE player_id='$player_id' AND player_teams LIKE '%[$team_id]%' LIMIT 1");

		if(mysql_num_rows($find_player) == "0")
		    {
			print("Player not found on specified team");
		     }
		else
		    {
			$player_row = mysql_fetch_assoc($find_player);
			$player_teams = $player_row[player_teams];

			$replace = "[$team_id]";
			$new_teams = str_replace($replace, "", $player_teams);

			$update_player = mysql_query("UPDATE `web_players` SET player_teams='$new_teams' WHERE player_id='$player_id' LIMIT 1");

			if(!$update_player)
			    {
				show_error("Unable to delete player");
			     }
			else
			    {
				print("<p align=\"center\"><b>Player Deleted</b></p>\n\n");
				print("<p align=\"center\">You will be automatically redirected...</p>\n\n");
				print("<p align=\"center\"><a href=\"./teams.php?eventId=$eventId\">Click here to continue</a></p>\n\n");
				print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./teams.php?eventId=$eventId\">\n\n");
			     }
		     }

		print_footer();
	     }
	else
	    {
		print_header("Admin",0);
		show_error("Unknown command");
		print_footer();
	     }
     }
  elseif($_GET["eventId"] != "" && isset($_COOKIE[lqc_scores_session]))
    {
	$eventId = $_GET["eventId"];

	print_header("Admin","Teams");

	$event_title = return_event_title($eventId);

	verify_session();

	verify_event_pm($eventId);
?>
<table width="430" cellspacing="1" cellpadding="2" align="center" bgcolor="#aaaaaa">
  <tr bgcolor="#f6f6f6">
   <td height="30" colspan="16" align="center" class="large">Player Maintenance</td>
 </tr>
  <tr height="18" bgcolor="#f0f0f0">
   <td class="small">&nbsp; <b>Add Player</b></td>
   <td class="small">&nbsp; <b>Change Player Codename</b></td>
   <td class="small">&nbsp; <b>Change Player Center</b></td>
 </tr>
  <tr height="25" bgcolor="#ffffff">
   <td valign="top">
	<form method="post" action="./teams.php">
	<input type="hidden" name="do" value="add_player">
	<input type="hidden" name="eventId" value="<?=$eventId?>">
	<table width="150" cellspacing="1" cellpadding="1" align="center">
	  <tr>
	   <td><b>Codename</b><br><input type="text" name="player_codename" size="20" maxlength="10"></td>
	 </tr>
	  <tr>
	   <td><b>Center</b><br><select name="player_center"><?=center_list();?></select></td>
	 </tr>
	  <tr>
	   <td><input type="checkbox" name="player_override" value="true"> Override existing</td>
	 </tr>
	  <tr>
	   <td><input type="submit" name="submit" value="Submit"></td>
	 </tr>
	</table>
	</form></td>
   <td valign="top">
	<form method="post" action="./teams.php">
	<input type="hidden" name="do" value="change_player_name">
	<input type="hidden" name="eventId" value="<?=$eventId?>">
	<table width="150" cellspacing="1" cellpadding="1" align="center">
	  <tr>
	   <td><b>Old Codename</b><br><select name="player_id"><?=player_list();?></select></td>
	 </tr>
	  <tr>
	   <td><b>New Codename</b><br><input type="text" name="player_codename" size="20" maxlength="10"></td>
	 </tr>
	  <tr>
	   <td><input type="submit" name="submit" value="Submit"></td>
	 </tr>
	</table>
	</form></td>
   <td valign="top">
	<form method="post" action="./teams.php">
	<input type="hidden" name="do" value="change_player_center">
	<input type="hidden" name="eventId" value="<?=$eventId?>">
	<table width="150" cellspacing="1" cellpadding="1" align="center">
	  <tr>
	   <td><b>Codename</b><br><select name="player_id"><?=player_list();?></select></td>
	 </tr>
	  <tr>
	   <td><b>New Center</b><br><select name="player_center"><option value="-">Select</option><?=center_list();?></select></td>
	 </tr>
	  <tr>
	   <td><input type="submit" name="submit" value="Submit"></td>
	 </tr>
	</table>
	</form></td>
 </tr>
</table>

<br>

<table width="430" cellspacing="1" cellpadding="2" align="center" bgcolor="#aaaaaa">
  <tr bgcolor="#f6f6f6">
   <td height="30" colspan="16" align="center" class="large"><b><?=$event_title;?></b> - Team Maintenance</td>
 </tr>
  <tr bgcolor="#f0f0f0">
   <td colspan="4" class="small">&nbsp; <b>Add Team</b></td>
 </tr>
  <tr height="25" bgcolor="#ffffff">
   <td colspan="4" valign="top">
	<form method="post" action="./teams.php">
	<input type="hidden" name="do" value="add_team">
	<input type="hidden" name="eventId" value="<?=$eventId?>">
	<table width="250" cellspacing="1" cellpadding="1" align="center">
	  <tr>
	   <td><b>Team Name</b></td>
	   <td><input type="text" name="team_name" size="20" maxlength="30"></td>
	   <td><input type="submit" name="submit" value="Add"></td>
	 </tr>
	</table>
	</form></td>
 </tr>
  <tr bgcolor="#f0f0f0">
   <td colspan="4" class="small">&nbsp; <b>Printing Options</b></td>
 </tr>
  <tr height="25" bgcolor="#ffffff">
   <td colspan="4" align="center"><a href="./teams.php?eventId=<?=$eventId?>&do=print">Print Teams Rosters</a></a>
 </tr>
</table>

<br>

<?
	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY `team_id` DESC");

	while($teamRow = mysql_fetch_assoc($fetch_teams))
	    {
		$team_id = $teamRow[team_id];
?>


<table width="430" cellspacing="1" cellpadding="2" align="center" bgcolor="#aaaaaa">
  <tr height="30" bgcolor="#f6f6f6">
   <td colspan="3" class="print">&nbsp;<b><?=$teamRow[team_name]?></b></td>
   <td align="center"><a href="./teams.php?do=delete_team&eventId=<?=$eventId?>&team_id=<?=$team_id?>" onClick="return confirm('Are you sure you want to delete this team?')">Delete</a></td>
 </tr>
  <tr height="18" bgcolor="#ebebeb">
   <td width="110" class="small">&nbsp; <b>Player Name</b></td>
   <td width="140" class="small">&nbsp; <b>Alias</b></td>
   <td width="130" class="small" align="center"><b>Home Center</b></td>
   <td width="50" align="center" class="small"><b>Options</b></td>
 </tr>
<?
		$fetch_players = mysql_query("SELECT * FROM `web_players` WHERE player_teams LIKE '%[$team_id]%'");

		if(mysql_num_rows($fetch_players) == "0")
		    {
			print("  <tr bgcolor=\"#ffffff\">\n   <td colspan=\"4\" align=\"center\">No players found</td>\n </tr>\n");
		     }

		while($playerRow = mysql_fetch_assoc($fetch_players))
		    {
			$player_id = $playerRow[player_id];
			$player_codename = $playerRow[player_codename];
			$player_center = return_center($playerRow[player_center]);

			print("  <tr bgcolor=\"#ffffff\">\n");
			print("   <td width=\"90\">$player_codename</td>\n");
			print("   <td width=\"110\" align=\"center\"><form method=\"post\" action=\"./teams.php\"><input type=\"hidden\" name=\"do\" value=\"add_alias\"><input type=\"hidden\" name=\"eventId\" value=\"$eventId\"><input type=\"hidden\" name=\"team_id\" value=\"$team_id\"><input type=\"hidden\" name=\"player_id\" value=\"$player_id\">");

			$check_alias = mysql_query("SELECT * FROM `web_alias` WHERE player_id='$player_id' AND alias_team='$team_id'");

			if(mysql_num_rows($check_alias) == "0")
			    {
				print("[<input class=\"clear\" type=\"text\" name=\"alias_name\" size=\"9\" maxlength=\"10\">]");
			     }
			else
			    {
				$alias_row = mysql_fetch_assoc($check_alias);
				print("[<input class=\"clear\" type=\"text\" name=\"alias_name\" value=\"$alias_row[alias_name]\" size=\"9\" maxlengt]h=\"10\">]");
			     }

			print(" <input class=\"clear\" type=\"submit\" name=\"submit\" value=\"Go\"></form></td>\n");
			print("   <td width=\"130\" align=\"center\">$player_center</td>\n");
			print("   <td width=\"60\" align=\"center\"><a href=\"./teams.php?do=delete_player&eventId=$eventId&team_id=$team_id&player_id=$player_id\" onClick=\"return confirm('Are you sure you want to delete this player?')\">Delete</a></td>\n");
			print(" </tr>\n");
		     }
?>
  <tr height="18" bgcolor="#ebebeb">
   <td class="small" align="center" colspan="2"><b>Add Player</b></td>
   <td class="small" align="center" colspan="2"><b>Change Team Name</b></td>
 </tr>
  <tr bgcolor="#ffffff">
   <td colspan="2" align="center"><form method="post" action="./teams.php"><input type="hidden" name="do" value="assign_player"><input type="hidden" name="eventId" value="<?=$eventId?>"><input type="hidden" name="team_id" value="<?=$team_id?>"><select name="player_id"><?=player_list();?></select> <input type="submit" name="submit" value="Add"></form></td>
   <td colspan="2" align="center"><form method="post" action="./teams.php"><input type="hidden" name="do" value="change_team_name"><input type="hidden" name="eventId" value="<?=$eventId?>"><input type="hidden" name="team_id" value="<?=$team_id?>"><input type="text" name="team_name" size="20" maxlength="30" value="<?=$teamRow[team_name]?>"> <input type="submit" name="submit" value="Go"></form></td>
 </tr>
</table>

<br>

<?
	     }

	print_footer();
     }

  // --------------------------------------------
  // Begin Public Section
  // --------------------------------------------

  elseif($_GET["eventId"] != "")
    {
	$eventId = $_GET["eventId"];

	print_header($eventId,"Rosters");

	if(isset($_COOKIE[lqc_scores_session]))
	    {
		verify_session();
	     }

?>
<table width="650" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Teams</b></td>
 </tr>
</table>

<table width="650" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr height="22" bgcolor="#ededed">
   <td width="260">&nbsp; Team Name</td>
   <td width="200" align="center">Player &bull; Alias</td>
   <td width="200" align="center">Center</td>
 </tr>
<?
	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY `team_name` ASC");

	while($teamRow = mysql_fetch_assoc($fetch_teams))
	     {
		$team_id = $teamRow[team_id];

		$fetch_players = mysql_query("SELECT * FROM `web_players` WHERE player_teams LIKE '%[$team_id]%'");

		$count_players = mysql_num_rows($fetch_players);

		print("	  <tr bgcolor=\"#ffffff\">\n");

		if($count_players == "0")
		    {
			print("	   <td colspan=\"3\">&nbsp; <span class=\"large\"><b>$teamRow[team_name]</b></span><br>&nbsp; <span class=\"small\">Players: $count_players</span></td>\n");
			print("	 </tr>\n");
		     }
		else
		    {
			print("	   <td rowspan=\"$count_players\">&nbsp; <span class=\"large\"><b>$teamRow[team_name]</b></span><br>&nbsp; <span class=\"small\">Players: $count_players</span></td>\n");
		     }

		$i = 0;

		while($playerRow = mysql_fetch_assoc($fetch_players))
		    {
			$player_id = $playerRow[player_id];
			$player_codename = $playerRow[player_codename];
			$player_center = $playerRow[player_center];

			if($i < $count_players && $i > 0)
			    {
				print("	  <tr bgcolor=\"#ffffff\">\n");
			     }

			$fetch_alias = mysql_query("SELECT * FROM `web_alias` WHERE player_id='$player_id' AND alias_team='$team_id'");

			if(mysql_num_rows($fetch_alias) == "0")
			    {
				print("	   <td height=\"20\" align=\"center\">$player_codename</td>\n");				
			     }
			else
			    {
				$aliasRow = mysql_fetch_assoc($fetch_alias);
				$alias_name = $aliasRow[alias_name];

				print("	   <td height=\"20\" align=\"center\">$player_codename &bull; $alias_name</td>\n");				
			     }

			print("	   <td align=\"center\">");
			center_name($player_center);
			print("</td>\n");
			print("	 </tr>\n");

			$i++;
		     }

		print("	  <tr bgcolor=\"#ffffff\">\n");
		print("	   <td colspan=\"3\"><img src=\"./images/clear.gif\" width=\"2\" height=\"2\"></td>\n");
		print("	 </tr>\n");
	      }

	print("	  <tr bgcolor=\"#ffffff\" height=\"20\">\n");
	print("	   <td colspan=\"3\"><a href=\"./teams.php?eventId=$eventId&do=print\">Print Teams Rosters</a></td>\n");
	print("	 </tr>\n");
	print("	</table>\n");

	print_footer();
     }
  else
    {
	print_header("Error",0);
	show_error("405 Method Not Allowed");
	print_footer();
     }
?>