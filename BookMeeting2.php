<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber of Commerce
//	File	BookMeeting.php
//			Book onto business briefing
//
//	First step is to fetch the attendees from the post data
//	Then fetch the booking ref & price from the system table
//	Find contact and his email
//	Write record to booking table
//	Postto carddetails for Braintree SDK
//
//	Author	John McMillan, McMillan Technology
// ------------------------------------------------------
    session_start();
?>
<!doctype html>
 
<html>
 
<head>
<title>Confirm Meeting Booking</TITLE>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script> 
<script src="Ajax.js"></script> 
</head>

<body onLoad="checkMobile()" >

<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
    require "Header.html";
	require "connect2.php";

	$config = setConfig();

	echo "<h3>Confirm Meeting Booking</h3>";

	$payMethod = "Bank";
	$type = $_GET['type'];
	$co = $_POST['mbrco'];
	$sqlDate = $_GET['date'];
	$attendee1 = $_POST['attendee1'];
	$attendee2 = $_POST['attendee2'];
	$attendee3 = $_POST['attendee3'];
	$attendee4 = $_POST['attendee4'];

	$attend = setAttendees();
	$count = count($attend);

	$sys = systemTable();
	$price = $sys['price'];
	$bookingRef = $sys['bookingRef'];

											// Find the primary contact
	if ($type == 'member')					// For members
	{
		$sql = "SELECT * FROM contacts WHERE compname =? AND mainflag=1";
		if (!($stmt = $mysqli->prepare($sql)))
			myError(ERR_BM2_PREP1, 
				"Prepare failure: (" . $mysqli->errno . ") " . $mysqli->error);
	
		if (!$stmt->bind_param('s', $co))
			myError(ERR_BM2_BIND1, 
				"Bind failed: (" . $mysqli->errno . ") " . $mysqli->error);
		if (!$stmt->execute())
			myError(ERR_BM2_EX1, 
				"Read error: (" . $mysqli->errno . ") " . $mysqli->error);

		$result = $stmt->get_result();
		if ($result == false)
			echo "We do not have a main contact for the $co. Please contact the Secretary";
		$contact = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$ctName = $contact['name'];
		$email = $contact['email'];
		mysqli_free_result($result);

/*
	This code is part of card payments. Do not remove from the file
		$query = "SELECT Method FROM members WHERE Company ='$co'";
		$result = $mysqli->query($query)
			or die (mysqli_error($mysqli));
		$member = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$payMethod = $member['Method'];
		mysqli_free_result($result); */
	}
	else
	{
		$ctName = $_POST['contact'];
		$email = $_POST['email'];
	}
	$_SESSION['bookingRef'] = $bookingRef;
	
											// Present the data to customer
	echo "Company $co<br>";
	echo "Attendees for the lunch<br>";
	foreach ($attend as $name)
		echo "$name<br>";
	$total = $count * $price;
	$total = number_format($total,2);
	echo "<br>$count attending, price is &pound;" . $total . "<br>";

	
//		This code removed after exec meeting of April 2019
//	$link = '"carddetails.php?price=' . $total . '"';
//	echo "\n<br><button onclick='window.location.assign($link)'>Pay by card</button>";
//	if ($payMethod == "BACS")
	{
		$link2 = '"PayAccount.php?price' . $total . '"';
		echo "&nbsp;&nbsp;<button onclick='window.location.assign($link2)'>Pay by bank transfer</button>";
	}

                                    // Write an unconfirmed record to the table
	$sql = "INSERT INTO bookings (Date, Company, Method, Name, Ref, State, "
		. "Attendee1, Attendee2, Attendee3, Attendee4, Price, Email) "
		. "VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?)";
	if (!($stmt = $mysqli->prepare($sql)))
		myError(ERR_BM2_PREP2, 
			"Prepare failure: (" . $mysqli->errno . ") " . $mysqli->error);

	if (!$stmt->bind_param('ssssissssds', $sqlDate, $co, $payMethod, $ctName, $bookingRef,
		$attendee1, $attendee2, $attendee3, $attendee4, $total, $email))
		myError(ERR_BM2_BIND2, 
			"Bind failed: (" . $mysqli->errno . ") " . $mysqli->error);

	if (!$stmt->execute())
		myError(ERR_BM2_EX2, 
			"Data error: (" . $mysqli->errno . ") " . $mysqli->error);

// -----------------------------------------------------
//	Process system table
//
//	Make and return next booking ref
//	Fetch and return price
// -----------------------------------------------------
function systemTable()
{
	global $mysqli;

	$ret = array();

	$sql = "SELECT * FROM system";			// System table: make next booking ref
	$result = $mysqli->query($sql)
		or die (mysqli_error($mysqli));
	$record = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$type = $_GET['type'];					// Fetch member or guest price
	if ($type == 'member')
		$price = $record['memberprice'];
	else
		$price = $record['guestprice'];
		
	$bookingRef = $record['booking'];		// Update next booking on system table
	mysqli_free_result($result);
	$sql = "UPDATE system SET booking=" . ($bookingRef + 1);
	$mysqli->query($sql)
		or die (mysqli_error($mysqli));

	$ret['price'] = $price;
	$ret['bookingRef'] = $bookingRef;
	return $ret;
}

// -----------------------------------------------------
// Loop through the post data, create array of attendees
//
// -----------------------------------------------------
function setAttendees()
{
	$attend = array();						// Holds list of attendees
	$count = 0;

	foreach ($_POST as $key => $value)
	{
		if (substr($key, 0, 2) == 'cb')		// A check box
		{
			array_push($attend, $value);
			$count++;
		}
		if (substr($key, 0, 5) == 'other')	// Other contacts (member)
		{
			if ($value <> '')
			{
				array_push($attend, $value);
				$count++;
			}
		}
		if (substr($key, 0, 6) == 'attend')	// Guest contacts
		{
			if ($value <> '')
			{
				array_push($attend, $value);
				$count++;
			}
		}
		if (substr($key, 0, 2) == 'co')		// The company name
			$co = $value;
	}

	return $attend;
}

?>
</body>
</html>
