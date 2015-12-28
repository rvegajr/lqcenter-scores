<?
  require("config.php");
  require("functions.php");

  print_header();

  $fetch_centers = mysql_query("SELECT * FROM `web_centers` ORDER BY `center_number` ASC");

  while($center_row = mysql_fetch_assoc($fetch_centers))
    {
	$center_number = $center_row[center_number];
	$center_title = $center_row[center_title];

	$center_array[$center_number] = $center_title;
     }

?>

<br>

<table width="800" cellspacing="1" cellpadding="2" align="center">
<?
  $fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$event_id' ORDER BY `team_id` ASC");

  $i = 0;

  if(mysql_num_rows($fetch_teams) == "0")
    {
	print("No teams assigned for event");
     }
  while($row = mysql_fetch_assoc($fetch_teams))
    {
	$team_id = $row[team_id];
	$team_name = $row[team_name];

	print("<td valign=\"top\" align=\"center\"><b>$team_name</b>");

	$fetch_players = mysql_query("SELECT * FROM `web_players` WHERE player_teams LIKE '%[$team_id]%'");

	if(mysql_num_rows($fetch_players) == "0")
	    {
		print("No players assigned to team");
	     }
	while($player_row = mysql_fetch_assoc($fetch_players))
	    {
		$codename = $player_row[player_codename];
		$center = $player_row[player_center];

		print("\n<br>$player_row[player_codename] ($center_array[$center])");
	     }

	print("</td>");

	$i++;

	if($i > 3)
	   {
		print("</tr><tr><td colspan=\"4\"><br></td></tr><tr>");
		$i = 0;
	    }
     }
?>
</tr></table>
<?=print_footer();?>