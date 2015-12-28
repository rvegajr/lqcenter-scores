<?php
  require("config.php");
  require("functions.php");

  header('Content-type: image/gif');

  foreach($_GET as $key=>$value)
    {
	${$key} = $value;
     }

  if($getPlayer != "" && is_numeric($getPlayer))
    {
	$getPlayer = $getPlayer;
     }
  else
    {
	$getPlayer = 0;
     }

  $image = @imagecreatetruecolor(700,400) or die("Cannot Initialize new GD image stream");

  //Colors
  $bgcolor = imagecolorallocate($image, 238, 238, 238);

  $dark_grey = imagecolorallocate($image, 150, 150, 150);
  $light_grey = imagecolorallocate($image, 200, 200, 200);
  $white = imagecolorallocate($image, 255, 255, 255);
  $red = imagecolorallocate($image, 255, 0, 0);
  $black = imagecolorallocate($image, 0, 0, 0);

  //Draw background
  imagefilledrectangle($image, 2, 2, 697, 397, $white);

  if($eventId != "" && is_numeric($eventId))
    {
	$whereQuery = "`score_event` = '$eventId' AND `score_player` = '$getPlayer'";
     }
  else
    {
	$whereQuery = "`score_player` = '$getPlayer'";
     }

  $fetch_graph_scores = mysql_query("SELECT `score_score` FROM `web_scores` WHERE $whereQuery");

  if(mysql_num_rows($fetch_graph_scores) == "0")
    {
	imagestring($image, 7, 240, 180, 'No scores found for player', $black);
	$numScores = 0;
      }
  else
    {
	$numScores = mysql_num_rows($fetch_graph_scores);
     }

  while($scoreRow = mysql_fetch_assoc($fetch_graph_scores))
    {
	$scoreArray[] = $scoreRow[score_score];
     }

  if($numScores != "0")
    {
	$scoreMax = max($scoreArray);
	$scoreMin = min($scoreArray);

	$xAxisStart = 40;
	$xAxisEnd = 690;
	$xAxisSize = $xAxisEnd-$xAxisStart;
	$xAxisUnit = floor($xAxisSize/$numScores);

	$yAxisStart = 20;
	$yAxisEnd = 380;
	$yAxisSize = $yAxisEnd-yAxisStart;
	$yAxisUnit = 100;

	$yAxisMax = ceil($scoreMax/$yAxisUnit)*$yAxisUnit;
	$yAxisMin = floor($scoreMin/$yAxisUnit)*$yAxisUnit;

	$yAxisSpread = $yAxisUnit+($yAxisMax-$yAxisMin);

	$yAxisDiv = round($yAxisSpread/$yAxisUnit);

	$yAxisInc = floor($yAxisSize/$yAxisDiv);

	//Draw x-axis
	imageline($image, $xAxisStart, $yAxisEnd, $xAxisEnd, $yAxisEnd, $black);

	//Draw y-axis
	imageline($image, $xAxisStart, $yAxisStart, $xAxisStart, $yAxisEnd, $black);

	//Draw y-axis guidelines
	for($drawYguide = 0; $drawYguide < $yAxisDiv; $drawYguide++)
	   {
		$yGuide = $yAxisEnd-($yAxisInc*$drawYguide);

		$yGuideLabel = $yAxisMin+($yAxisUnit*$drawYguide);

		imagestring($image, 2, $xAxisStart-30, $yGuide-6, $yGuideLabel, $black);

		if($drawYguide != "0")
		    {
			imageline($image, $xAxisStart, $yGuide, $xAxisEnd, $yGuide, $dark_grey);
		     }
	    }

	//Draw x points
	for($drawXpt = 0; $drawXpt < $numScores; $drawXpt++)
	    {
		$score = $scoreArray[$drawXpt];

		$xPt = $xAxisStart+2+($xAxisUnit*$drawXpt);
		$yPtRough = floor($score/($yAxisUnit/$yAxisInc));

		if($yAxisMin != "0")
		    {
			$yOffset = ($yAxisMin/100)*$yAxisInc;
			$yPt = $yAxisEnd-($yPtRough-$yOffset);
		     }
		else
		    {
			$yPt = $yAxisEnd-$yPtRough;
		     }

		if($graphLabels != "off" && $score < 0)
		    {
			imagestringup($image, 2, $xPt-7, $yPt-5, $score, $red);
		     }
		elseif($graphLabels != "off")
		    {
			imagestringup($image, 2, $xPt-7, $yPt-5, $score, $black);
		     }

		imagefilledellipse($image, $xPt, $yPt, 4, 4, $black);

		$arrayPoints[x][$drawXpt] = $xPt;
		$arrayPoints[y][$drawXpt] = $yPt;
	     }

	for($cntC = 0; $cntC < $numScores; $cntC++)
	    {
		if($cntC < $numScores-1)
		    {
			$currVal = $cntC;
			$nextVal = $cntC+1;
		     }
		else
		    {
			$currVal = $cntC;
			$nextVal = $cntC;
		     }

		$currPointX = $arrayPoints[x][$currVal];
		$currPointY = $arrayPoints[y][$currVal];
		$nextPointX = $arrayPoints[x][$nextVal];
		$nextPointY = $arrayPoints[y][$nextVal];

		imageline($image, $currPointX, $currPointY, $nextPointX, $nextPointY, $black);
	     }
     }


  imagegif($image);
  imagedestroy($image);
?>