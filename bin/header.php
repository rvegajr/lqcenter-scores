<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<script type="text/javascript" src="./js/jquery.js"></script>
<script type="text/javascript" src="./js/jquery-ui.js"></script>
<script type="text/javascript" src="./js/main.js"></script>
<?
  if($pageTitle != "")
    {
	print("<title>LQCenter.com - $pageTitle</title>\n");
     }
  else
    {
	print("<title>LQCenter.com</title>\n");
     }

  $httphost = $_SERVER['SERVER_NAME'];

  if($pageAnalytics == "1")
    {
	print("<script type=\"text/javascript\" src=\"./js/analytics.js\"></script>\n");
     }
?>
<link rel="stylesheet" type="text/css" href="./style.css">
</head>

<body>

<div id="wrap">
  <div id="header">
    <div id="headerLeft"><a href="./index.php"><img border="0" src="./images/logo.gif" width="183" height="73" alt="LQCenter.com"></a></div>
<?
  if($headerTitle != "")
    {
	print("    <div id=\"headerRight\"><p align=\"right\">$headerTitle</p></div>\n");
     }
?>
  </div>
