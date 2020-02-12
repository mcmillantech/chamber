<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber
//	File	BusinessHelp.php
//
//	Author	John McMillan, McMillan Technology
//
// ------------------------------------------------------

?>
<!doctype html>
<html>
<head>

<title>Chamber Accomplishments</title>
<meta name="description"
 content="Accomplishments of the Sudbury, Suffolk Chamber of Commerce and Induastry">
<meta name="keywords" content="Business help provided by the Sudbury Chamber of Commerce"> 

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<meta name=viewport content="width=device-width, initial-scale=1.0">
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script> 
</head>

<body onLoad="checkMobile()" >

<!--#include virtual="Header.html" -->

<?php
	require "connect2.php";
	require("Header.html");
	echo "<h2>Help given to Businesses</h2>";

	echo "<p style='margin: 0px 20px 0px -20px'>";
	echo "The Chamber works hard to protect and promote business in Sudbury. ";
	echo "Here are some of the things we have done in recent years.</p>";
	
	$result = $mysqli->query("SELECT * FROM accomplish");
	while ($line =  mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$text = $line['Text'];
		echo "<p>$text</p>";
	}

	require "Footer.html";	
?>
</body>
</html>
