<?php

  function verifySession()
    {
	global $connect;
	global $mysql_db;
	global $inc_header;
	global $inc_footer;

	if(!isset($_COOKIE[lqc_scores_session]))
	    {
		notifyBox("notifyError","Your session was not found",0,0);
		include $inc_footer;
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
			notifyBox("notifyError","Your session has expired. Please relogin.","3","./login.php?do=logout");
			include $inc_footer;
			exit;
		     }
	     }
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
  function fetch_game_info($event_id,$game_type)
    {
	$fetchEvent = mysql_query("SELECT * FROM `web_events` WHERE `id` = '$event_id' LIMIT 1") or die(mysql_error());
	$eventRow = mysql_fetch_assoc($fetchEvent);

	$event_title = $eventRow[title];

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

	$return_array = array('title' => $event_title, 'game_pack_sets' => $game_pack_sets, 'game_format' => $game_format, 'game_title' => $game_title, 'format_num' => $format_num);

	return $return_array;
     }

?>