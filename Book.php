<!doctype html>
 
<html lang="en-GB">
 
<head>
<title>Sudbury Chamber of Commerce Meetings</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script> 
</head>

<body onLoad="checkMobile()" >

<?php
	require "Header.html";
	require "connect2.php";
	$config = setConfig();

	$mtgDate = $_GET['ev'];
//	$dates = getNextDate();
	$pDate = printDate($mtgDate);
	echo "<h1>Meeting on " . $pDate . "</h1>";

//	$sqlD = $dates['sql'];
	$sql = "SELECT * FROM briefings WHERE date='$mtgDate'";
	$result = $mysqli->query($sql);
	if (mysqli_num_rows($result) == 0)
	{
		$meet = array
		(
			'Speaker' => 'To be confirmed',
			'Bio' => '',
			'Subject' => "We are sorry, this meeting has not been finalised.<br>"
				. "<br>Please try later or contact 01787 227722."
		);
	}
   	else
   		$meet = mysqli_fetch_array($result, MYSQL_ASSOC);

	echo "<div>";
	echo "<h3>Speaker</h3>";
	echo $meet['Speaker'];
	echo "&nbsp;&nbsp;&nbsp;" . $meet['Bio'];
	echo "</div>";
	echo "<div style='min-height:100px'>";
	echo "<h3>Topic</h3>";
   	echo $meet['Subject'];
	echo "</div>";

	echo "<div style='min-height:80px'>";
	if (mysqli_num_rows($result) > 0)
	{
		echo "Venue the Bridge Project, Gainsborough Street, Sudbury, CO10 2EU<br>";
		echo "From 12.30 to 2.00pm<br>";
		echo "Cost &pound;11.00 for Chamber members, &pound;12.00 for non members<br>";
		echo "To book, please click the button below<br>";
		echo "<p><button type='button' "
			. "onClick='window.location=\"BookMeeting.php?date=$sqlD\"'>Book</button></p>";
		echo "</div>";
	}

	require "Rightmenu.html";
	require "Footer.html";

function printDate($sqlDate)
{
	list($year, $mon, $day) = split("-", $sqlDate);
	$pDate = "$day/$mon/$year";
	return $pDate;
}

function getNextDate()
{
	$month = date('m');				// Find the current month and year
	$year = date('Y');
									// What day does 1st of the month fall on?
	$thisMonth = mktime(0,0,0,$month,1,$year);
	$dom = date('w', $thisMonth);
	if ($dom < 4)					// Find the 2nd Wednesday
		$day = 11-$dom;
	else
		$day = 18-$dom;
									// Make the date of the event
	$mtgDate = mktime(0,0,0,$month,$day,$year);
	$sqlDate = date('Y-m-d', $mtgDate);
	$pDate = date("j F Y", $mtgDate);

	$dates = array();
	$dates['print'] = $pDate;
	$dates['sql'] = $sqlDate;

	return $dates;
}

?>
</body>
</html>
