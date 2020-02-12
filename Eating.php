<!doctype html>
<html>
 
<head>
<title>Eating Out in Sudbury Suffolk</title>
<meta name="description"
 content="Eating in Sudbury Suffolk">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<script src="Chamber.js"></script> 
</head>

<body onLoad="checkMobile()" >

<?php
	require "Header.html";
	require "Leftmenu.html";

	require "connect.php";
	mysql_select_db($db_name) 
		or die("Could not select database $db_name");

	if (!array_key_exists('class', $_GET))
		$class = 'Cafe';
	else
		$class = $_GET['class'];
	switch ($class)
	{
	case 'Cafe':
		$Title = 'Cafe Restaurants';
		break;
	case 'Pubs':
		$Title = 'Pub Food';
		break;
	case 'Rest':
		$Title = 'Restaurants';
		break;
	case 'Chips':
		$Title = 'Fish &amp; Chips &amp; Fast Food';
		break;
	case 'Sandw':
		$Title = 'Sandwiches';
		break;
	}
	print "<h1>Lunch in Sudbury</h1>";
	print "<p>Sudbury is well served for eaters, with a wide variety of "
		. "restaurants, cafes, pubs, sandwich bars and fast food "
		. "outlets.</p>";
	print "<h2>$Title</h2>";

	$query = "SELECT * FROM eating WHERE Type = '$class'";
	$result = mysql_query($query)
		or die("Query failed : " . mysql_error());
	while ($line = mysql_fetch_array($result))
	{
		print '<p><b><font color="#C04040">';
		print $line['Name'] . '<br>';
		print '</font></b>' . $line['Address'] . '<br>';
		print 'Tel: ' . $line['Phone'] . '<br>';
		print 'Theme: ' . $line['Theme'] . '<br>';
		$covers = $line['Covers'];
		if ($covers <> '')
			print "Covers: $covers<br>";
		$notes = $line['Notes'];
		if ($notes <> '')
			print "$notes<br>";
	}
	if ($class == 'Pubs')
	{
		print '<br><font color="#A00402"><b>';
		print 'Other Public Houses</b></font><br>';
		print '<br>Black Horse East St.<br>';
		print 'Royal Oak King St.<br>';
		print 'The Prince of Wales New St.<br>';
	}
	if ($class == 'Sandw')
	{
		print '<br><font color="#A00402"><b>';
		print 'Sandwiches are also sold at:</b></font><br>';
		print 'Marks and Spencer<br>';
		print 'Boots<br>';
		print 'Newsagents<br>';
		print 'Supermarkets<br>';
	}
	mysql_free_result($result);
	mysql_close($link);

	print '</td>';
//	print '</table>';
?>

<td width="22%" valign="top">	
    <h3 class="banner">Eating Places</h3>
    <br>
	<p><a class="barsr" href="Eating.php?class=Cafe">Cafe Restaurants</a></p>
	<p><a class="barsr" href="Eating.php?class=Rest">Restaurants</a></p>
    <p><a class="barsr" href="Eating.php?class=Pubs">Pub Food</a></p>
    <p><a class="barsr" href="Eating.php?class=Chips">Fish &amp; Chips &amp; Fast Food</a></p>
    <p><a class="barsr" href="EatPicnics.shtml">Picnic Areas</a></p>
    <p><a class="barsr" href="Eating.php?class=Sandw">Sandwiches</a></p>
</td></tr>
</table>

<?php
	require_once('Footer.html');
?>

</body>
</html>