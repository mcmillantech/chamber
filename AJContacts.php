<?php 
// ------------------------------------------------------
//	File	AJContacts.php
//			Make a list of contacts for a company
//
//	Parameter
//		co		Company
//
//	Called from function ASSelect (Ajax.js)
//	to select contacts for lunch booking
//
//	Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------
	$co = $_GET['co'];
							// Restore ampersands (e.g. Holmes & Hill
	$co = str_replace("%26", '&', $co);
	require_once "connect2.php";		// Connect to database

	$sql = "SELECT name FROM contacts WHERE compname='$co'";
	$result = $mysqli->query($sql) 
		or die("Query failed : " . mysqli_error());

	$list = '';
	$n = 1;					// Index to contact
	while ($record = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$dta = $record['name'];
		$cb = 'cb' . $n++;
		$list .= "<input type='checkbox' name='$cb' class='ctcheck' value='$dta'>$dta<br>";
	}
	mysqli_free_result($result);
	echo $list;


?>

