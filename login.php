<?
  require("config.php");
  require("functions.php");

  if($_POST['submit'])
    {
	if($_POST['form_email'] == "")
	    {
		print_header(Error,0);
		show_error("You must enter your e-mail address");
		print_footer();
	     }
	elseif($_POST['form_password'] == "")
	    {
		print_header(Error,0);
		show_error("You must enter your password");
		print_footer();
	     }
	else
	    {
		$form_email = $_POST['form_email'];
		$form_password = $_POST['form_password'];

		$find_user = mysql_query("SELECT * FROM `web_admin` WHERE user_email='$form_email' AND user_password=MD5('$form_password')");

		if(mysql_num_rows($find_user) == "0")
		    {
			print_header(Error,0);
			show_error("We were unable to verify your information");
			print_footer();
		     }
		else
		    {
			$mysql_row = mysql_fetch_array($find_user);

			$ipaddress = $_SERVER['REMOTE_ADDR'];

			$query = mysql_query("UPDATE `web_admin` SET `user_lastvisit` = UNIX_TIMESTAMP(), `user_lastip` = '$ipaddress' WHERE `user_email` = '$form_email' LIMIT 1") or die(mysql_error());

			$data[pm_events] = $mysql_row[pm_events];
			$data[pm_create_event] = $mysql_row[pm_create_event];
			$data[pm_score_entry] = $mysql_row[pm_score_entry];
			$data[user_id] = $mysql_row[user_id];
			$data[user_email] = $mysql_row[user_email];
			$data[user_password] = $mysql_row[user_password];
			$data[user_fname] = $mysql_row[user_fname];
			$data[user_level] = $mysql_row[user_level];

			$string = serialize($data);

			setcookie("lqc_scores_session", $string, time()+60*60*24, '/');

			print_header("Creating Session",0);

			echo "<p align=\"center\">You are being redirected</p>\n\n";
			echo "<p align=\"center\"><a href=\"index.php\">Click here to continue</a></p>\n\n";
			echo "<META HTTP-EQUIV=Refresh CONTENT=\"2; URL=index.php\">\n\n";

			print_footer();
		     }
	     }
     }
  elseif($_GET["do"] == "logout")
    {
	setcookie("lqc_scores_session", $_COOKIE[lqc_scores_session], time()-60*60*24, '/');

	print_header("Closing Session",0);

	print("<p align=\"center\">Logging out, please wait...</p>");
	print("<meta http-equiv=\"refresh\" content=\"2; URL=./index.php\">");

	print_footer();
     }
  else
    {
	print_header(Login,0);
?>
<table width="330" cellspacing="1" cellpadding="2" align="center" bgcolor="#dadada">
  <tr height="25" bgcolor="#f0f0f0">
   <td>&nbsp; <b>Administrator Login</b></td>
 </tr>
  <tr height="25" bgcolor="#ffffff">
   <td valign="top">
	<form method="post" action="./login.php">
	<table width="300" cellspacing="1" cellpadding="1" align="center">
	  <tr>
	   <td><b>E-mail</b><br><input type="text" name="form_email" size="20"></td>
	   <td><b>Password</b><br><input type="password" name="form_password" size="20"></td>
	 </tr>
	  <tr>
	   <td colspan="2"><input type="submit" name="submit" value="Login"></td>
	 </tr>
	</table>
	</form></td>
 </tr>
</table>
<?
	print_footer();
     }
?>