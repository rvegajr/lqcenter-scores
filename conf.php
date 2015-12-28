<?php

  $mysql_user = "lqcenter_mysql";
  $mysql_password = "";

  $mysql_db = "lqcenter_scores";

  $connect = mysql_connect("localhost","$mysql_user","$mysql_password") or die("Unable to connect to MySQL Database");

  mysql_select_db($mysql_db);

  $path			= "/home/lqcenter/public_html/scores";

  $inc_header		= "$path/bin/header.php";
  $inc_footer		= "$path/bin/footer.php";
  $inc_global		= "$path/glob.php";

  date_default_timezone_set('America/Chicago');

  include $inc_global;
?>