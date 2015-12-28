<?
  //This script will strictly rank a game if ranks are not available

  require("config.php");
  require("functions.php");

  if($_GET["getGame"] != "" && $_GET["getType"] != "" && $_GET["getEvent"] && isset($_COOKIE[lqc_scores_session]))
    {
	verify_session();

	$getGame = $_GET["getGame"];
	$getType = $_GET["getType"];
	$getEvent = $_GET["getEvent"];

	print_header("Admin",0);

	$fetch_scores = mysql_query("SELECT * FROM `web_scores` WHERE `score_event` = '$getEvent' AND `score_game` = '$getGame' AND `score_game_type` = '$getType'");

	if(mysql_num_rows($fetch_scores) == "0")
	    {
		print("<p align=\"center\"><b>No games found</b></p>\n\n<p align=\"center\"><a href=\"./schedule.php?game_type=$game_type&eventId=$eventId\">Click here to continue</a></p>\n\n");
		//print("<META HTTP-EQUIV=Refresh CONTENT=\"1; URL=./rank_team_scores.php?game_type=$getType&eventId=$getEvent\">\n\n");
	     }

	while($scoreRow = mysql_fetch_assoc($fetch_scores))
	    {
		$score_id = $scoreRow[score_id];
		$score_score = $scoreRow[score_score];

		$rank_array[$score_id] = $score_score;
	     }

	arsort($rank_array);

	$ranki = 1;

	foreach($rank_array as $score_id=>$score_score)
	    {
		mysql_query("UPDATE `web_scores` SET `score_rank` = '$ranki' WHERE `score_id` = '$score_id' LIMIT 1");
		$ranki++;
	     }

	print("<p align=\"center\"><b>Ranks Updated</b></p>\n\n");
	print("<p align=\"center\">Redirecting...</p>\n\n");
	print("<p align=\"center\"><a href=\"./score_entry.php?getEvent=$getEvent&getGame=$getGame&getType=$getType&entryType=$entryType\">Click here to return to game</a></p>\n\n");
	print("<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=./rank_team_scores.php?game_type=$getType&eventId=$getEvent\">\n\n");
     }
  else
    {
	print_header("Admin",0);
	show_error("405 Method Not Allowed");
	print_footer();
     }
?>