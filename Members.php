<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber
//	File	Members.php
//			Lists the members for a short (3char)
//			class passed in the URI
//
//	Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------
?>
<!doctype html>
 
<html lang="en-GB">
 
<head>
<title>Sudbury Chamber Members</TITLE>
<meta name="description"
 content="Members of Sudbury Chamber of Commerce">
<meta name=viewport content="width=device-width, initial-scale=1.0">

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script> 
</head>

<body onLoad="checkMobile()" >

<?php
	require "Header.html";
//	require "Leftmenu.html";
	require "connect2.php";
	$config = setConfig();

	if (array_key_exists('class', $_GET))
		doClass();
	else
		doSearch();

	mysqli_close($mysqli);

//	require "Rightmenu.html";
	require "Footer.html";

// --------------------------------------
//  Process search button
//
//  HTTP parameters
//		stext	Search text
//		By		Company name or dsc
// --------------------------------------
function doSearch()
{
	global $mysqli;

	$sText = $_POST['stext'];
	if ($_POST['By'] == 'Co')
		$query = 'SELECT * FROM members WHERE Company LIKE "%' . $sText . '%"'
			. 'AND Class <> "Non" AND Member=1';
	else
		$query = 'SELECT * FROM members WHERE (ShortDsc LIKE "%' . $sText . '%"'
			. 'OR Description LIKE "%' . $sText . '%") '
			. 'AND Class <> "Non" AND Member=1';
	$result = $mysqli->query($query);
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC))
    	showMember($line);
	mysqli_free_result($result);

}

// --------------------------------------
//  Generate a page for a class
//
// --------------------------------------
function doClass()
{
	global $mysqli;

	$sql = 'SELECT * FROM classes WHERE Short = ?';
	if (!($stmt = $mysqli->prepare($sql)))
		myError(ERR_MB_CL_PREP1, 
			"Prepare failure: (" . $mysqli->errno . ") " . $mysqli->error);

	if (!$stmt->bind_param('s', $key))
		myError(ERR_MB_CL_BIND1, 
			"Bind failed: (" . $mysqli->errno . ") " . $mysqli->error);
	$key = $_GET['class'];
	if (!$stmt->execute())
		myError(ERR_MB_CL_EX1, 
			"Read error: (" . $mysqli->errno . ") " . $mysqli->error);
	$result = $stmt->get_result();

	$line = mysqli_fetch_array($result, MYSQL_ASSOC);
	mysqli_free_result($result);
	$stmt->close();

	$sql = 'SELECT * FROM members WHERE class = ? AND Member=1';
	if (!($stmt = $mysqli->prepare($sql)))
		myError(ERR_MB_CL_PREP2, 
			"Prepare failure: (" . $mysqli->errno . ") " . $mysqli->error);

	if (!$stmt->bind_param('s', $class))
		myError(ERR_MB_CL_BIND2, 
			"Bind failed: (" . $mysqli->errno . ") " . $mysqli->error);
	$class = $line['Class'];
	if (!$stmt->execute())
		myError(ERR_MB_CL_EX2, 
			"Read error: (" . $mysqli->errno . ") " . $mysqli->error);
	$result = $stmt->get_result();

	echo "<h2>$class</h2>";

    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC))
    	showMember($line);
	mysqli_free_result($result);
	$stmt->close();
}

// --------------------------------------
//  Show one member
//
// --------------------------------------
function showMember($line)
{
	print '<h3>' . $line['Company'] . '</h3>';

									// Show address and maybe logo
	print '<table width = 100%><tr>';
		print '<td width=70%>';
		print '<b>' . $line['ShortDsc'] . '<br></b>';
		print $line['Addr1'] . '<br>';
		if ($line['Addr2'] <> '')
			print $line['Addr2'] . '<br>';
		if ($line['Addr3'] <> '')
			print $line['Addr3'] . '<br>';
		if ($line['Addr4'] <> '')
			print $line['Addr4'] . '<br>';
		print $line['Postcode'] . '<br>';
		print 'Phone: ' . $line['Phone'] . '<br>';
		print 'Email: ' . $line['EmailSale'] . "<br>\n";

	print '</td>';
	print '<td width=30% valign=top>';
		$logo = $line['Logo'];
		if ($logo <> '')
			print '<img src="images/' . $logo . '" width=150>';
	print '</td></tr>';
	print '</table>';

	if ($line['Gold'] == 1)			// Check for picture
	{
		$photo = $line['Picture'];
		if ($photo <> '')
		{
			print '<p align=center>';
			print '<img src="images/' . $photo . '" height=150></p>';
		}
		print $line['Description'] . '<br>';
		$url = $line['Website'];
		if (stripos($url, "http") === FALSE)
			$url = "http://" . $url;
		print "Web site: <a href='$url' target='blank'>$url</a><br>";
	}
}
?>

</body>
</html>