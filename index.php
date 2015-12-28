<?
  require("config.php");
  require("functions.php");

  print_header("Welcome","Events");

  if(isset($_COOKIE[lqc_scores_session]))
    {
	verify_session();

	$user_array = unserialize(stripslashes($_COOKIE[lqc_scores_session]));

	$pm_events = $user_array[pm_events];
     }
?>
<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Player Statistics</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr height="35" bgcolor="#ffffff">
   <td align="center">&nbsp; <a href="./stats_overall.php">Overall Individual Statistics</a> &nbsp; | &nbsp; <a href="./stats_player.php">Side-by-side Player Comparison</a></td>
 </tr>
</table>

<br>

<table width="790" cellspacing="1" cellpadding="2" class="table_bl_top" align="center">
  <tr>
   <td><b>Game Statistics</b></td>
 </tr>
</table>

<table width="790" cellspacing="1" cellpadding="0" bgcolor="#aaaaaa" align="center">
  <tr height="22" bgcolor="#ededed">
   <td>&nbsp; Tournament</td>
   <td align="center">Stats</td>
   <td align="center">Rosters</td>
   <td align="center">Prelims</td>
   <td align="center">Playoffs</td>
   <td align="center">Consoles</td>
   <td align="center">Finals</td>
 </tr>
<?

/*  if(isset($COOKIE[lqc_scores_session]))
    {
	if(is_numeric($pm_events))
	    {
		$fetch_events = mysql_query("SELECT * FROM `web_events` WHERE `event_id` = '$pm_events' ORDER BY `stamp` DESC");
	     }
	elseif($pm_events == "all")
	    {
		$fetch_events = mysql_query("SELECT * FROM `web_events` ORDER BY `stamp` DESC");
	     }
	else
	    (

	     }
     }
  else
    {*/
	$fetch_events = mysql_query("SELECT * FROM `web_events` ORDER BY `stamp` DESC");
//     }

  while($eventRow = mysql_fetch_assoc($fetch_events))
    {
	$eventId = $eventRow[id];

	$check_rosters = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId'");
	$check_pre = mysql_query("SELECT * FROM `web_games` WHERE event='$eventId' AND type='pre'");
	$check_pla = mysql_query("SELECT * FROM `web_games` WHERE event='$eventId' AND type='pla'");
	$check_con = mysql_query("SELECT * FROM `web_games` WHERE event='$eventId' AND type='con'");
	$check_fin = mysql_query("SELECT * FROM `web_games` WHERE event='$eventId' AND type='fin'");
?>
	  <tr bgcolor="#ffffff" height="45">
	   <td>&nbsp; <span class="large"><b><?=$eventRow[title]?></b></span><br>&nbsp; <span class="small"><?=date("F j, Y", $eventRow[stamp]);?></span></td>
	   <td class="small" align="center"><a href="./stats_event.php?eventId=<?=$eventId?>">Overall</a></td>
<?
	//Check teams
	if(mysql_num_rows($check_rosters) != "0" || isset($_COOKIE[lqc_scores_session]))
	    {
		$num_teams = mysql_num_rows($check_rosters);
		print("	   <td class=\"small\" align=\"center\"><a href=\"./teams.php?eventId=$eventId\">$num_teams Teams</a></td>\n");
	     }
	else
	    {
		print("	   <td class=\"small_grey\" align=\"center\">-</td>\n");
	     }

	//Check for prelims stats
	if(mysql_num_rows($check_pre) != "0" || isset($_COOKIE[lqc_scores_session]))
	    {
		print("	   <td class=\"small\" align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getType=pre\">Schedule</a><br>
<a href=\"./team_scores.php?game_type=pre&eventId=$eventId\">Team Scores</a><br>
<a href=\"./statsIndiv.php?getEvent=$eventId&getType=pre\">Individual</a></td>\n");
	     }
	else
	    {
		print("	   <td class=\"small_grey\" align=\"center\">Schedule<br>Team Scores<br>Individual</td>\n");
	     }

	//Check for playoffs stats
	if(mysql_num_rows($check_pla) != "0" || isset($_COOKIE[lqc_scores_session]))
	    {
		print("	   <td class=\"small\" align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getType=pla\">Schedule</a><br><a href=\"./team_scores.php?game_type=pla&eventId=$eventId\">Team Scores</a><br><a href=\"./statsIndiv.php?getEvent=$eventId&getType=pla\">Individual</a></td>\n");
	     }
	else
	    {
		print("	   <td class=\"small_grey\" align=\"center\">Schedule<br>Team Scores<br>Individual</td>\n");
	     }

	//Check for consoles stats
	if(mysql_num_rows($check_con) != "0" || isset($_COOKIE[lqc_scores_session]))
	    {
		print("	   <td class=\"small\" align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getType=con\">Schedule</a><br><a href=\"./team_scores.php?game_type=con&eventId=$eventId\">Team Scores</a><br><a href=\"./statsIndiv.php?getEvent=$eventId&getType=con\">Individual</a></td>\n");
	     }
	else
	    {
		print("	   <td class=\"small_grey\" align=\"center\">Schedule<br>Team Scores<br>Individual</td>\n");
	     }

	//Check for finals stats
	if(mysql_num_rows($check_fin) != "0" || isset($_COOKIE[lqc_scores_session]))
	    {
		print("	   <td class=\"small\" align=\"center\"><a href=\"./schedule.php?getEvent=$eventId&getType=fin\">Schedule</a><br><a href=\"./team_scores.php?game_type=fin&eventId=$eventId\">Team Scores</a><br><a href=\"./statsIndiv.php?getEvent=$eventId&getType=fin\">Individual</a></td>\n");
	     }
	else
	    {
		print("	   <td class=\"small_grey\" align=\"center\">Schedule<br>Team Scores<br>Individual</td>\n");
	     }
?>
	 </tr>
<?
     }

  print("</table>\n\n");

  print_footer();
?>