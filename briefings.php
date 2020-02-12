<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber
//	File	Briefings.php
//			Lists the lunch meetings
//			Starts from BRIEF_LAST
//
//	Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------
?>
<!doctype html>
 
<html lang="en-GB">
 
<head>
<title>Sudbury Chamber of Commerce Meetings</TITLE>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script> 
</head>

<body onLoad="checkMobile()" >

<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	require "Header.html";
	require "connect2.php";
	$config = setConfig();
?>
	<H1>Lunchtime Meetings and Briefings</H1>
	<P>We meet on the second Wednesday of every month at the Bridge Project (through the 
	Courtyard Cafe), Gainsborough Street. 
	The cost is low, there is always a speaker on a topic of some interest and it is a great 
	way to network and meet other people in business.</P>
	<h2>Programme</h2>
	<div style="float:left; width:60%">
<?php

	date_default_timezone_set("Europe/London");
	$today = date('Y-m-d G:i:s');
	$query = "SELECT *, DATE_FORMAT(Date,'%D %M %Y')as sDate FROM briefings" .
		" WHERE Date > " . BRIEF_LAST . " ORDER BY Date DESC";
	$result = $mysqli->query($query)
		or myError(ERR_MB_BR_EX1, 
			"Read error: (" . $mysqli->errno . ") " . $mysqli->error);
	while ($line = mysqli_fetch_array($result, MYSQL_ASSOC))
		showMeeting($line, $today);

	mysqli_free_result($result);
	mysqli_close($mysqli);
?>

	</div>
	<div style="float:left; width: 40%">
	<p><img src="images/Lunch09-19.jpg" alt="Meeting with Robert Hobbs" width="100%" align="center"></p>
	</p>Robert Hobbs of Babergh DC briefing Chamber members</p>
	<p><img src="images/clunch.jpg" alt="Chamber Meeting" width="100%" align="center"></p>
	<p>Networking at Chamber briefing</p>

	</div>
	<div style="clear :both"> </div>
	
<?php
// ----------------------------------------
//	Show details of one meeting
//	Include a button to book this meeting
//	by date
//
//	Parameters	Meeting record
//				Today's date
// ----------------------------------------
function showMeeting($line, $today)
{

	echo '<h3>' . $line['sDate'] . '</h3>';
	echo "</p><p style='width:95%'>" . $line['Speaker'] . ': ';
	echo $line['Bio'];
	echo "</p><p style='width:95%'>";
	echo $line['Subject'] . "</p>";
	if ($line['Date'] > $today)
	{
		$link = '"BookMeeting.php?date=' . $line['Date'] . '"';
		echo "\n<button type='button' onclick='window.location.assign($link)'> Book </button>";
	}
	echo "\n";
}

?>

<P>Bookings for all the above can be made with Andy Howes on 01787 227722
 or on <a href="MailTo:secretary@sudbury.org.uk">secretary@sudbury.org.uk</a>.</P>
<?php
	require "Rightmenu.html";
	require "Footer.html";
?>
</body>
</html>