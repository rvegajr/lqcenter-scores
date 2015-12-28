<?
  function verify_session()
    {
	global $connect;
	global $mysql_db;
	global $inc_header;
	global $inc_footer;

	if(!isset($_COOKIE[lqc_scores_session]))
	    {
		show_error("Your session was not found");
		print_footer();
		exit;
	     }
	else
	    {
		$user_array = unserialize(stripslashes($_COOKIE[lqc_scores_session]));

		$user_email = $user_array[user_email];
		$user_password = $user_array[user_password];
		$user_lastip = $_SERVER['REMOTE_ADDR'];

		$secure_check = mysql_query("SELECT * FROM `web_admin` WHERE user_email='$user_email' AND user_password='$user_password' AND user_lastip='$user_lastip'");

		if(mysql_num_rows($secure_check) == "0")
		    {
			show_error("Your session has expired, please logout and relogin");
			print_footer();
			exit;
		     }
	     }
     }
  function verify_action($pm)
    {
	$user_array = unserialize(stripslashes($_COOKIE[lqc_scores_session]));

	$user_email = $user_array[user_email];
	$user_password = $user_array[user_password];

	$pm_query = mysql_query("SELECT $pm FROM `web_admin` WHERE user_email='$user_email' AND user_password='$user_password'");

	$pm_row = mysql_fetch_assoc($pm_query);
	$pm_value = $pm_row[$pm];

	if($pm == "0")
	    {
		fatalError("You do not have sufficient privileges to perform this action");
		print_footer();
		exit;
	     }
     }
  function verify_event_pm($getEvent)
    {
	$user_array = unserialize(stripslashes($_COOKIE[lqc_scores_session]));

	$user_email = $user_array[user_email];
	$user_password = $user_array[user_password];

	$pm_query = mysql_query("SELECT pm_events FROM `web_admin` WHERE user_email='$user_email' AND user_password='$user_password'");

	$pm_row = mysql_fetch_assoc($pm_query);
	$pm_value = $pm_row[pm_events];

	$requested_event = "[$getEvent]";

	if($pm_value != "all" && $pm_value != $requested_event)
	    {
		if(!eregi('$requested_event', $pm_value))
		{
		fatalError("You do not have sufficient privileges to access this event");
		print_footer();
		exit;
		}
	     }
     }
  function fetch_game_info($event_id,$game_type)
    {
	$fetch_event = mysql_query("SELECT * FROM `web_events` WHERE id='$event_id' LIMIT 1") or die(mysql_error());
	$eventRow = mysql_fetch_assoc($fetch_event);

	$event_title = $eventRow[title];

	$center = return_center($eventRow[center]);

	if($game_type == "pre")
	    {
		$game_pack_sets = $eventRow[prelim_sets];
		$game_format = $eventRow[prelim_format];
		$game_title = "Prelim";
	     }
	elseif($game_type == "pla")
	    {
		$game_pack_sets = $eventRow[playoff_sets];
		$game_format = $eventRow[playoff_format];
		$game_title = "Playoff";
	     }
	elseif($game_type == "con")
	    {
		$game_pack_sets = $eventRow[console_sets];
		$game_format = $eventRow[console_format];
		$game_title = "Consoles";
	     }
	elseif($game_type == "fin")
	    {
		$game_pack_sets = $eventRow[final_sets];
		$game_format = $eventRow[final_format];
		$game_title = "Finals";
	     }

	if($game_format == "tq")
	    {
		$format_num = 3;
	     }
	elseif($game_format == "dq")
	    {
		$format_num = 2;
	     }
	elseif($game_format == "none")
	    {
		$format_num = 1;
	     }

	$return_array = array('title' => $event_title, 'game_pack_sets' => $game_pack_sets, 'game_format' => $game_format, 'game_title' => $game_title, 'format_num' => $format_num, 'center' => $center);

	return $return_array;
     }
  function fetch_pack_sets($eventId,$game_type)
    {
	$fetch_event = mysql_query("SELECT * FROM `web_events` WHERE id='$eventId' LIMIT 1") or die(mysql_error());
	$eventRow = mysql_fetch_assoc($fetch_event);

	if($game_type == "pre")
	    {
		$event_pack_sets = $eventRow[prelim_sets];
	     }
	elseif($game_type == "pla")
	    {
		$event_pack_sets = $eventRow[playoff_sets];
	     }
	elseif($game_type == "con")
	    {
		$event_pack_sets = $eventRow[console_sets];

	     }
	elseif($game_type == "fin")
	    {
		$event_pack_sets = $eventRow[final_sets];
	     }

	return $event_pack_sets;
     }
  function fetch_game_title($eventId,$game_type)
    {
	$fetch_event = mysql_query("SELECT * FROM `web_events` WHERE id='$eventId' LIMIT 1") or die(mysql_error());
	$eventRow = mysql_fetch_assoc($fetch_event);

	if($game_type == "pre")
	    {
		$game_title = "Prelim";
	     }
	elseif($game_type == "pla")
	    {
		$game_title = "Playoff";
	     }
	elseif($game_type == "con")
	    {
		$game_title = "Consoles";
	     }
	elseif($game_type == "fin")
	    {
		$game_title = "Finals";
	     }

	return $game_title;
     }
  function fetch_game_format($eventId,$game_type)
    {
	$fetch_event = mysql_query("SELECT * FROM `web_events` WHERE id='$eventId' LIMIT 1") or die(mysql_error());
	$eventRow = mysql_fetch_assoc($fetch_event);

	if($game_type == "pre")
	    {
		$game_format = $eventRow[prelim_format];
	     }
	elseif($game_type == "pla")
	    {
		$game_format = $eventRow[playoff_format];
	     }
	elseif($game_type == "con")
	    {
		$game_format = $eventRow[console_format];
	     }
	elseif($game_type == "fin")
	    {
		$game_format = $eventRow[final_format];
	     }

	return $game_format;
     }
  function fetch_prelim_drops($eventId)
    {
	$fetch_event = mysql_query("SELECT prelim_drops FROM `web_events` WHERE id='$eventId' LIMIT 1") or die(mysql_error());
	$eventRow = mysql_fetch_assoc($fetch_event);

	$prelim_drops = $eventRow[prelim_drops];

	return $prelim_drops;
     }
  function fetch_prelim_rankby($eventId)
    {
	$fetch_event = mysql_query("SELECT prelim_rankby FROM `web_events` WHERE id='$eventId' LIMIT 1") or die(mysql_error());
	$eventRow = mysql_fetch_assoc($fetch_event);

	$prelim_rankby = $eventRow[prelim_rankby];

	return $prelim_rankby;
     }
  function fetch_hex_array($event_pack_sets,$game_format)
    {
	if($game_format == "tq")
	    {
		$rgy_color = array(1 => '#ffbbbb', 2 => '#beffbb', 3 => '#fffabf', 4 => '#ffbbbb', 5 => '#beffbb', 6 => '#fffabf', 7 => '#ffbbbb', 8 => '#beffbb', 9 => '#fffabf', 10 => '#ffbbbb', 11 => '#beffbb', 12 => '#fffabf',);
	     }
	elseif($game_format == "dq")
	    {
		$rgy_color = array(1 => '#ffbbbb', 2 => '#beffbb', 3 => '#ffbbbb', 4 => '#beffbb', 5 => '#ffbbbb', 6 => '#beffbb', 7 => '#ffbbbb', 8 => '#beffbb', 9 => '#ffbbbb', 10 => '#beffbb', 11 => '#ffbbbb', 12 => '#beffbb');
	     }
	elseif($game_format == "none")
	    {
		$rgy_color = array(1 => '#fffabf', 2 => '#fffabf', 3 => '#fffabf', 4 => '#fffabf', 5 => '#fffabf', 6 => '#fffabf', 7 => '#fffabf', 8 => '#fffabf', 9 => '#fffabf', 10 => '#fffabf', 11 => '#fffabf', 12 => '#fffabf');
	     }

	$return_array = array_slice($rgy_color, 0, $event_pack_sets, true);

	return $return_array;
     }
  function fetch_soft_hex_array($event_pack_sets,$game_format)
    {
	if($game_format == "tq")
	    {
		$rgy_color = array(1 => '#ffe6e6', 2 => '#e6ffe7', 3 => '#fffde6', 4 => '#ffe6e6', 5 => '#e6ffe7', 6 => '#fffde6', 7 => '#ffe6e6', 8 => '#e6ffe7', 9 => '#fffde6', 10 => '#ffe6e6', 11 => '#e6ffe7', 12 => '#fffde6',);
	     }
	elseif($game_format == "dq")
	    {
		$rgy_color = array(1 => '#ffe6e6', 2 => '#e6ffe7', 3 => '#ffe6e6', 4 => '#e6ffe7', 5 => '#ffe6e6', 6 => '#e6ffe7', 7 => '#ffe6e6', 8 => '#e6ffe7', 9 => '#ffe6e6', 10 => '#e6ffe7', 11 => '#ffe6e6', 12 => '#e6ffe7');
	     }
	elseif($game_format == "none")
	    {
		$rgy_color = array(1 => '#fffde6', 2 => '#fffde6', 3 => '#fffde6', 4 => '#fffde6', 5 => '#fffde6', 6 => '#fffde6', 7 => '#fffde6', 8 => '#fffde6', 9 => '#fffde6', 10 => '#fffde6', 11 => '#fffde6', 12 => '#fffde6');
	     }

	$return_array = array_slice($rgy_color, 0, $event_pack_sets, true);

	return $return_array;
     }
  function fetch_rgm_array($event_pack_sets,$game_format)
    {
	if($game_format == "tq")
	    {
		$rgy_color = array(1 => 'red', 2 => 'green', 3 => 'mixed', 4 => 'red', 5 => 'green', 6 => 'mixed', 7 => 'red', 8 => 'green', 9 => 'mixed', 10 => 'red', 11 => 'green', 12 => 'mixed');
	     }
	elseif($game_format == "dq")
	    {
		$rgy_color = array(1 => 'red', 2 => 'green', 3 => 'red', 4 => 'green', 5 => 'red', 6 => 'green', 7 => 'red', 8 => 'green', 9 => 'red', 10 => 'green', 11 => 'red', 12 => 'green');
	     }
	else
	    {
		$rgy_color = array(1 => 'mixed', 2 => 'mixed', 3 => 'mixed', 4 => 'mixed', 5 => 'mixed', 6 => 'mixed', 7 => 'mixed', 8 => 'mixed', 9 => 'mixed', 10 => 'mixed', 11 => 'mixed', 12 => 'mixed');
	     }

	$return_array = array_slice($rgy_color, 0, $event_pack_sets, true);

	return $return_array;
     }
  function fetch_format_num($game_format,$pack_sets)
    {
	if($game_format == "tq")
	    {
		$format_num = 3;
	     }
	elseif($game_format == "dq")
	    {
		$format_num = 2;
	     }
	elseif($game_format == "none")
	    {
		$format_num = 1;
	     }

	return $format_num;
     }
  function show_error($error)
    {
	print("<p align=\"center\" class=\"red\"><b>Error</b></p>\n");
	print("<p align=\"center\" class=\"red\">$error</p>\n");
     }
  function fatalError($error)
    {
	print("<br>\n\n");
	print("<table width=\"480\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\" class=\"table_border_red\">\n");
	print("	  <tr>\n");
	print("	   <td bgcolor=\"#ebd1d1\" class=\"bold_red\" height=\"35\">&nbsp; The following errors were found:</td>\n");
	print("	 </tr>\n");
	print("	  <tr>\n");
	print("	   <td bgcolor=\"f5f9fd\" height=\"35\">&nbsp; $error</td>\n");
	print("	 </tr>\n");
	print("</table>\n\n");
	print("<br>\n\n");

	print_footer();
	exit;
     }
  function player_center($player_id)
    {
	$find_center = mysql_result(mysql_query("SELECT web_centers.center_title FROM `web_players`, `web_centers` WHERE web_centers.center_number = web_players.player_center AND player_id='$player_id'"),0);
	print($find_center);
     }
  function center_name($center_number)
    {
	$find_center = mysql_result(mysql_query("SELECT center_title FROM `web_centers` WHERE center_number='$center_number'"),0);
	print($find_center);
     }
  function event_title($event_id)
    {
	$fetch_title = mysql_result(mysql_query("SELECT title FROM `web_events` WHERE id='$event_id'"),0);
	print($fetch_title);
     }
  function return_event_title($event_id)
    {
	$fetch_title = mysql_result(mysql_query("SELECT title FROM `web_events` WHERE id='$event_id'"),0);
	
	return $fetch_title;
     }
  function print_team_name($team_id)
    {
	$fetch_title = mysql_result(mysql_query("SELECT team_name FROM `web_teams` WHERE team_id='$team_id'"),0);
	print($fetch_title);
     }
  function team_name($team_id)
    {
	$fetch_name = mysql_result(mysql_query("SELECT team_name FROM `web_teams` WHERE team_id='$team_id'"),0);

	return $fetch_name;
     }
  function player_name($player_id,$team_id)
    {
	if($team_id == "0")
	    {
		$fetch_player = mysql_result(mysql_query("SELECT player_codename AS var FROM `web_players` WHERE player_id='$player_id'"),0);
		print($fetch_player);
	     }
	else
	    {
		$check_alias = mysql_query("SELECT * FROM `web_alias` WHERE player_id='$player_id' AND alias_team='$team_id'");

		if(mysql_num_rows($check_alias) != "0")
		    {
			$alias_row = mysql_fetch_assoc($check_alias);
			print($alias_row[alias_name]);
		     }
		else
		    {
			$fetch_player = mysql_result(mysql_query("SELECT player_codename AS var FROM `web_players` WHERE player_id='$player_id'"),0);
			print($fetch_player);
		     }
	     }
     }
  function return_alias($player_id,$team_id)
    {
	if($team_id == "0")
	    {
		$fetch_player = mysql_result(mysql_query("SELECT player_codename AS var FROM `web_players` WHERE player_id='$player_id'"),0);
		return $fetch_player;
	     }
	else
	    {
		$check_alias = mysql_query("SELECT * FROM `web_alias` WHERE player_id='$player_id' AND alias_team='$team_id'");

		if(mysql_num_rows($check_alias) != "0")
		    {
			$alias_row = mysql_fetch_assoc($check_alias);
			return $alias_row[alias_name];
		     }
		else
		    {
			$fetch_player = mysql_result(mysql_query("SELECT player_codename AS var FROM `web_players` WHERE player_id='$player_id'"),0);
			return $fetch_player;
		     }
	     }
     }
  function return_codename($player_id)
    {
	$fetch_player = mysql_result(mysql_query("SELECT player_codename AS var FROM `web_players` WHERE player_id='$player_id'"),0);
	return $fetch_player;
     }
  function return_center($center_number)
    {
	$find_center = mysql_result(mysql_query("SELECT center_title FROM `web_centers` WHERE center_number='$center_number'"),0);
	return $find_center;
     }
  function return_event_list()
    {
	$fetch_events = mysql_query("SELECT id, title FROM `web_events` ORDER BY `stamp` DESC");

	if(mysql_num_rows($fetch_events) == "0")
	    {
		$eventOptions = "<option value=\"0\">-</option>";
	     }
	else
	    {
		$eventOptions .= "<option value=\"0\">-</option>";
	     }

	while($eventRow = mysql_fetch_array($fetch_events))
	    {
		$eventOptions .= "<option value=\"$eventRow[id]\">$eventRow[title]</option>";
	     }

	return $eventOptions;
     }
  function player_list()
    {
	$fetch_players = mysql_query("SELECT web_centers.center_title, web_players.* FROM web_centers, web_players WHERE web_centers.center_number = web_players.player_center ORDER BY `player_codename` ASC");

	if(mysql_num_rows($fetch_players) == "0")
	    {
		print("<option value=\"0\">-</option>");
	     }
	else
	    {
		print("<option value=\"0\">-</option>");
	     }

	while($player_row = mysql_fetch_array($fetch_players))
	    {
		$center_title = substr($player_row[center_title], 0, 8);

		print("<option value=\"$player_row[player_id]\">$player_row[player_codename] ($center_title)</option>");
	     }
     }
  function player_name_list()
    {
	$fetch_players = mysql_query("SELECT * FROM `web_players` ORDER BY `player_codename` ASC");

	if(mysql_num_rows($fetch_players) == "0")
	    {
		print("<option value=\"0\">-</option>");
	     }
	else
	    {
		print("<option value=\"0\">-</option>");
	     }

	while($player_row = mysql_fetch_array($fetch_players))
	    {
		if($player_row[player_fname] == "")
		    {
			print("<option value=\"$player_row[player_id]\">$player_row[player_codename]</option>");
		     }
		else
		    {
			print("<option value=\"$player_row[player_id]\">$player_row[player_codename] ($player_row[player_fname] $player_row[player_lname])</option>");
		     }
	     }
     }
  function center_list()
    {
	$fetch_centers = mysql_query("SELECT * FROM `web_centers` ORDER BY `center_title` ASC");

	if(mysql_num_rows($fetch_centers) == "0")
	    {
		print("<option value=\"0\">-</option>");
	     }

	while($center_row = mysql_fetch_array($fetch_centers))
	    {
		print("<option value=\"$center_row[center_number]\">$center_row[center_title]</option>");
	     }
     }
  function team_list($eventId)
    {
	$fetch_teams = mysql_query("SELECT * FROM `web_teams` WHERE team_event='$eventId' ORDER BY `team_id` DESC");

	print("<option value=\"0\">-</option>");

	while($team_row = mysql_fetch_array($fetch_teams))
	    {
		print("<option value=\"$team_row[team_id]\">$team_row[team_name]</option>");
	     }
     }
  function print_header($title,$subtitle)
    {
	print("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");
	print("<html>\n");
	print("<head>\n");
	print("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\">\n");
	print("<script src=\"./scripts/jquery.js\" type=\"text/javascript\"></script>\n");
	print("<script src=\"./scripts/main.js\" type=\"text/javascript\"></script>\n");

	if(strlen($title) < 3)
	    {
		$display_title = mysql_result(mysql_query("SELECT title FROM `web_events` WHERE id='$title'"),0);
		print("<title>LQCenter.com - $display_title</title>\n");
	     }
	elseif($title != "")
	    {
		print("<title>LQCenter.com - $title</title>\n");
	     }
	else
	    {
		print("<title>LQCenter.com</title>\n");
	     }
	
	print("<link rel=\"stylesheet\" type=\"text/css\" href=\"./styles.css\">\n");
	print("</head>\n\n");

	print("<body>\n\n");

	if($title == "0")
	    {
		print("<table width=\"650\" height=\"85\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n");
		print("  <tr>\n");
		print("   <td><a href=\"./index.php\"><img border=\"0\" src=\"./images/logo.gif\" width=\"183\" height=\"73\" alt=\"LQCenter.com\"></a></td>\n");
		print(" </tr>\n");
		print("</table>\n");
	     }
	elseif($subtitle == "0")
	    {
		if(strlen($title) < 3)
		    {
			$display_title = mysql_result(mysql_query("SELECT title FROM `web_events` WHERE id='$title'"),0);
		     }
		else
		    {
			$display_title = $title;
		     }

		print("<table width=\"650\" height=\"85\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n");
		print("  <tr>\n");
		print("   <td><a href=\"./index.php\"><img border=\"0\" src=\"./images/logo.gif\" width=\"183\" height=\"73\" alt=\"LQCenter.com\"></a></td>\n");
		print("   <td align=\"right\" class=\"large\"><b>$display_title</b></td>\n");
		print(" </tr>\n");
		print("</table>\n");
	     }
	else
	    {
		if(strlen($title) < 3)
		    {
			$display_title = mysql_result(mysql_query("SELECT title FROM `web_events` WHERE id='$title'"),0);
		     }
		else
		    {
			$display_title = $title;
		     }

		print("<table width=\"650\" height=\"85\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n");
		print("  <tr>\n");
		print("   <td rowspan=\"2\"><a href=\"./index.php\"><img border=\"0\" src=\"./images/logo.gif\" width=\"183\" height=\"73\" alt=\"LQCenter.com\"></a></td>\n");
		print("   <td align=\"right\" class=\"large\" valign=\"bottom\"><b>$display_title</b></td>\n");
		print(" </tr>\n");
		print("  <tr>\n");
		print("   <td align=\"right\" valign=\"top\"><b>$subtitle</b></td>\n");
		print(" </tr>\n");
		print("</table>\n");
	     }
     }
  function printSimpleHeader($title,$subtitle)
    {
	print("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");
	print("<html>\n");
	print("<head>\n");
	print("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\">\n");
	print("<title>LQCenter.com</title>\n");
	print("<link rel=\"stylesheet\" type=\"text/css\" href=\"./styles.css\">\n");
	print("</head>\n\n");

	print("<body>\n\n");

		if(strlen($title) < 3)
		    {
			$display_title = mysql_result(mysql_query("SELECT title FROM `web_events` WHERE id='$title'"),0);
		     }
		else
		    {
			$display_title = $title;
		     }

		print("<table width=\"650\" height=\"35\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n");
		print("  <tr>\n");
		print("   <td align=\"center\" class=\"large\"><b>$display_title $subtitle</b></td>\n");
		print(" </tr>\n");
		print("</table>\n");
     }
  function print_footer()
    {
	if(isset($_COOKIE[lqc_scores_session]))
	    {
		print("<p align=\"center\">[ <a href=\"./login.php?do=logout\">Logout</a> ]</p>\n");
	     }
	else
	    {
		print("<p align=\"center\">[ <a href=\"./login.php\">Login</a> ]</p>\n");
	     }

	$end_year = date("Y");

	print("<p align=\"center\">Copyright &copy; 2007 - $end_year LQCenter.com</p>\n\n");

	print("</body>\n");
	print("</html>");
     }
  function notifyBox($type,$msg,$refresh,$url)
    {
	print("<p class=\"$type\">$msg");

	if($url != "0")
	    {
		print(" You are being redirected, or <a href=\"$url\">click here</a> to continue.</p>\n");
		print("<meta http-equiv=refresh content=\"$refresh; url=$url\">\n");
	     }
	else
	    {
		print("</p>\n");
	     }
     }
?>