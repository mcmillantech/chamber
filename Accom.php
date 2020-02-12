<!doctype html>

<html>
<head>
<title>Accommodation near Sudbury, Suffolk, England</TITLE>
<meta name="description"
 content="Accommodation near Sudbury, Suffolk, an attactive English market town surrounded by attractive countryside an classic English villages">
<meta name=viewport content="width=device-width, initial-scale=1.0">

<meta name="keywords" content="101 Dalmations, England, countryside">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script> 

</head>

<body onLoad="checkMobile()" >


<?php
	require "Header.html";

									// Fetch the class from the link
	$class = $_GET['class'];
	require "connect.php";
	mysql_select_db($db_name) 
		or die("Could not select database");

									// Fetch the class title
	$query = "SELECT * FROM actitles WHERE ID = '$class'";
	$result = mysql_query($query)
		or die("Query failed : " . mysql_error());
	$line = mysql_fetch_array($result);
	$title = $line['Title'];
	mysql_free_result($result);

								// Build SQL query for this class
	$query = "SELECT a.*, m.Company, m.member, m.Phone as mPhone FROM accom a "
		. "LEFT OUTER JOIN members m"
		. " ON m.company = a.name "
		. "WHERE a.Type = '$class'"
		. " ORDER BY member DESC, Name";
	$result = mysql_query($query)
		or die("Query failed : " . mysql_error());

	echo '<div class="acmain">';
	showList($class, $title, $result);
	echo '</div>';
	echo '<div class="acclasses" >';
	showClasses();
	echo '</div>';
	echo '<div style="clear:both">';
	echo '</div>';

// --------------------------------------------------
//	Show the list for this class
//
// --------------------------------------------------
function showList($class, $title, $result)
{
	echo '<h2 align="center">';
		if($class[0] == 'G')
			echo 'Guest Houses ';
		else
			echo 'Hotels and Inns ';
		echo "$title</h2>";
	if($class[0] == 'H')
	{
    	echo '<p align="center"><b>Not all the businesses listed below ';
		echo 'offer full hotel&nbsp;services</b></p>';
	}
	while ($line = mysql_fetch_array($result))
		showLine($line);

	mysql_free_result($result);
}

// --------------------------------------------------
//	Show details for a premise
//
// --------------------------------------------------
function showLine($line)
{
	echo "\n" . '<p><b><font color="#C04040">';
	echo $line['Name'] . '<br>';
	echo '</font></b>' . $line['Street'] . '<br>';
	echo $line['Village'] . '<br>';
	echo $line['Postcode'] . '<br>';
	echo $line['mPhone'] . '<br>';
	echo $line['Dsc'] . '<br>';
	
	$name = $line['Name'];
	$query = 'SELECT member, Gold, Description, Website, EmailSale, Picture FROM members WHERE company="' 
		. $name . '"';
	$result = mysql_query($query);
	$rows = mysql_num_rows($result);
	if ($rows > 0)
	{
		$mbr = mysql_fetch_array($result);
		mysql_free_result($result);
		if ($mbr['member'] == 1)
		{
			echo "Chamber of Commerce member<br>";
			if ($mbr['Gold'] == 1)			// Check for picture
			{
				$photo = $mbr['Picture'];
				if ($photo <> '')
				{
					echo '<p align=center>';
					echo '<img src="images/' . $photo . '" height=150></p>';
				}
				echo $mbr['Description'] . '<br>';
				$url = $mbr['Website'];
				echo 'Web site: <a href="http://' . $url . '">' . $url . '</a><br>';
				echo 'Email: ' . $mbr['EmailSale'] . "<br>";
			}
		}
	}
	
}

// --------------------------------------------------
// 	Right hand menu
//
// --------------------------------------------------
function showClasses()
{
    echo '<h3 class="banner">Hotels and Inns</h3>';
	$query2 = "SELECT * FROM actitles WHERE ID LIKE 'H%'";
	$result = mysql_query($query2)
		or die("Query failed : " . mysql_error());

	while ($line = mysql_fetch_array($result))
	{
		echo '<p><a class="barsr" href="Accom.php?class=' . $line['ID'] .	'"> ';
		echo $line['Title'] . "</a></p>\n";
	}
	mysql_free_result($result);

    echo '<h3 class="banner">Guest Houses</h3>';
	$query2 = "SELECT * FROM actitles WHERE ID LIKE 'GH%'";
	$result = mysql_query($query2)
		or die("Query failed : " . mysql_error());

	while ($line = mysql_fetch_array($result))
	{
		echo '<p><a class="barsr" href="Accom.php?class=' . $line['ID'] .	'"> ';
		echo $line['Title'] . "</a></p> \n";
	}
	mysql_free_result($result);
}
?>
<p>Disclaimer: The information is provided for guidance only and the Sudbury
Chamber of Commernce do not
inspect or recommend accommodation. Please mention you saw the details on the
Chamber of commerce web-site when making a booking. </p>
<p>Chamber of Commerce members can add details about
their business on this site for standard membership fee + &pound;15 for the gold
service</p>


<?php

//	require "Rightmenu.html";
	require "Footer.html";

?>

</body>
</html>