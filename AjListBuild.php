<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber of Commerce
//	File	AJListBuild.php
//			Make a list of members to match a string
//
//	Parameter
//		srch	String to match
//
//	Called from function ASEditKeyUp (Ajax.js)
//
//	Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------

?>
<?php 

$srch = $_GET['srch'];

makeMemberList($srch);

function makeMemberList($srch)
{
	require_once "connect2.php";		// Connect to database

	$sql = "SELECT inx, Company FROM members WHERE Company LIKE '$srch%' ORDER BY Company"
		. " LIMIT 0,10";
	$result = $mysqli->query($sql) 
		or die("Query failed : " . mysqli_error());

	$list = array();
	while ($record = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$dta = $record['Company'];
		$dta = array (
			'rec',
			$record['inx'],
			$record['Company']
			);
		array_push($list, $dta);
	}
	$json = json_encode($list);
	mysqli_free_result($result);
	echo $json;
}

?>
