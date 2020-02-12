<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber
//	File	Committee.php
//			Lists the committee members
//
//	Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------
?>
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
	require_once "view.php";

	$config = setConfig();

	$dbConnection = mysqli_connect ('localhost', $config['dbuser'], $config['dbpw'])
		or die("Could not connect : " . mysqli_connect_error());
	mysqli_select_db($dbConnection, $config['dbname']) 
		or die("Could not select database : " . mysqli_error($dbConnection));

	$dta = array();
	$dtaMbr = array();

	$dta['officers'] = doOfficers();
	$dta['members'] = doMembers();
//	print_r($dta);
	showView("Committee.html", $dta);

	require "Footer.html";

// ----------------------------------
//	Process the officers
//
//	Return array of officers
// ----------------------------------
function doMembers()
{
	global $dbConnection;

	$dtaMbr = array();

	$sql = "SELECT * from committee WHERE office=''";
	$result = mysqli_query($dbConnection, $sql)
		or die ("There has been an error. We apologise for the inconvenience " 
			. mysqli_error($dbConnection));

	While ($mbr = mysqli_fetch_array($result, MYSQL_ASSOC))
	{
		array_push($dtaMbr, $mbr);
	}
	mysqli_free_result($result);

	return $dtaMbr;
}

// ----------------------------------
//	Process the officers
//
//	Return array of officers
// ----------------------------------
function doOfficers()
{
	$dtaOff = array();

	$roles = array
	(
		"president", "chairman", "vice chairman", "treasurer", "secretary"
	);

	foreach ($roles as $office)
	{
		if (!$mbr = getOfficer($office) )
			continue;
		$mbr['office'] = ucwords($mbr['office']);
		array_push($dtaOff, $mbr);
	}

	return $dtaOff;
}

// ----------------------------------
//	Fetch record for one officer
//
//	Parameter	Ofice
//
//	Returns		Officer record
//				false if no officer
// ----------------------------------
function getOfficer($office)
{
	global $dbConnection;

	$query = "SELECT * FROM committee WHERE office = '$office'";
	$result = mysqli_query($dbConnection, $query)
		or die ("There has been an error. We apologise for the inconvenience " 
			. mysqli_error($dbConnection));
//
	if (mysqli_num_rows($result) == 0)
		return false;
	$line = mysqli_fetch_array($result, MYSQL_ASSOC);
	mysqli_free_result($result);

	return $line;
}

function getIncludeFile($file)
{
	
}