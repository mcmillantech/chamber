<!doctype html>
 
<html>
 
<head>
<title>Sudbury Chamber of Commerce News</TITLE>
<meta name=viewport content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script> 
</head>

<body onLoad="checkMobile()" >

<?php
	require "Header.html";
	require "connect2.php";
//	require "Leftmenu.html";

	$config = setConfig();

	$dbConnection = mysqli_connect ('localhost', $config['dbuser'], $config['dbpw'])
		or die("Could not connect : " . mysqli_connect_error());

	mysqli_select_db($dbConnection, $config['dbname']) 
		or die("Could not select database : " . mysqli_error($dbConnection));

//	date_default_timezone_set("Europe/London");
//	$today = date('Y-m-d G:i:s');

	$query = "SELECT * FROM news ORDER BY date DESC";
	$result = mysqli_query($dbConnection, $query)
		or die ("There has been an error. We apologise for the inconvenience " 
			. mysqli_error($dbConnection));

	echo "<h1>Chamber News</h1>";

	while ($line = mysqli_fetch_array($result, MYSQL_ASSOC))
		showLine ($line);

//	require "Rightmenu.html";
	require "Footer.html";

function showLine($line)
{
	echo "<h2>" . $line["title"] . "</h2>";
	echo $line["item"];
}
?>

</BODY>
</HTML>