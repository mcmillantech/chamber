<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber of Commerce
//	File	PayAccount.php
//			Lunch booking on account
//
//	Author	John McMillan, McMillan Technology
// ------------------------------------------------------
?>
<!doctype html>

<html>

<head>
<title>Payment for Chamber booking</TITLE>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script> 
</head>

<body>
<?php

	session_start();
	require "Header.html";
	require "connect2.php";
	$config = setConfig();

	const TEST_EMAIL = false;

	confirmBooking();

// -----------------------------------
//	Process successful receipt
//
// -----------------------------------
function confirmBooking()
{
	$config = setConfig();

			// Record the success into booking table
	$dbConnection = mysqli_connect ('localhost', $config['dbuser'], $config['dbpw'])
		or die("Could not connect : " . mysqli_connect_error());
	mysqli_select_db($dbConnection, $config['dbname']) 
		or die("Could not select database : " . mysqli_error($dbConnection));

	echo "<h3>Booking on account</h3>";
	$ref = $_SESSION['bookingRef'];
	
	$query = "SELECT * FROM bookings WHERE ref=$ref";	// Fetch the booking from the table
	$result = mysqli_query($dbConnection, $query)
		or myError (ERR_PA_CONF, "Fetch bookings: (" . $mysqli->errno . ") " . $mysqli->error);
   	$booking = mysqli_fetch_array($result, MYSQL_ASSOC);
   	mysqli_free_result($result);

   			// -------- Display info to visitor ---------------
	list($year, $mon, $day) = split("-", $booking['Date']);
	$pDate = "$day/$mon/$year";
	echo "<p>Thank you for booking places for the business briefing on $pDate</p>";
	echo "<p>We have reserved places for:<br>";
	for ($i=1; $i<5; $i++)
	{
		$fld = "Attendee" . $i;
		$at = $booking[$fld];
		if ($at != '')
			echo " $at<br>";
	}
	echo "</p>";

			// -----  Generate email to client
	$booking = fetchBooking($ref);
	$dta = buildEmail($ref, $booking);
	$htmlText = $dta['html'];
	$to = $dta['email'];
	sendMail($htmlText, $to, "confirmation");

	$dta = buildInvoice($ref, $booking, $pDate);
	$htmlText = $dta['html'];
	sendMail($htmlText, $to, "an invoice");

	echo "<p>Please transfer &pound;" . number_format($booking['Price'],2) . " to</p>";
	echo "<div style='margin-left: 20px'>";
		echo "<p>Barclays Bank<br>";
		echo "Sort code 20-83-50<br>";
		echo "Account   80846368</p>";
	echo "</div>";
	echo "<p>Using reference Lunch$ref</p>";

//	echo "<p>We have send a confimation and invoice to " . $booking['Email'];
			// ------- Mark booking as complete
	$sql = "UPDATE bookings SET State=1, Method='Bank' WHERE ref=$ref";
	mysqli_query($dbConnection, $sql);
}

// ----------------------------------------
//	sendMail
//
//	Send the confirmation email
//
//	Parameters	HTML of message body
//				Address of recipient
// ----------------------------------------
function sendMail($message, $to, $type)
{
	$from = officerMail('BookingFrom');
	$replyTo = officerMail('BookingReply');

	$subject = 'Sudbury Chamber Lunch Booking';

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= "From: $from\r\n"
//		. 'CC: john@mcmillantech.co.uk' . "\r\n" 
	    . "Reply-To: $replyTo\r\n" 
	    . "X-Mailer: PHP/" . phpversion();
//echo "Hdr $headers<br>";
//return;
	if (TEST_PAY_RECEIVED)
	{
		echo "Test email text<br>";
		return;
	}
	
	if (TEST_EMAIL) {
		echo "Headers $headers<br>";
		echo "Msg<br>$message<br>";
	}
	else {
		$reply = mail($to, $subject, $message, $headers);
		if ($reply)
			print "We have sent $type to $to<br>";
		else
			print "$type failed to $to<br>";
	}
}


// --------------------------------------------
// --------------------------------------------
function buildInvoice($ref, $record, $pDate)
{
	global $mysqli;

	$dta = array();

	$email = $record['Email'];
	$co = $record['Company'];
	$name = $record['Name'];
	$price = number_format($record['Price'],2);
	date_default_timezone_set("Europe/London");	// Today's date
	$dt = date('d F Y');


	$sql = "SELECT * FROM members WHERE Company='$co' ";			// Fetch address etc
	$result = $mysqli->query($sql)
		or die (mysqli_error($mysqli));
	$member = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_free_result($result);

	$html = "<html>\n";
	$html .= "<body>\n";
	$html .= "<h1>Invoice</h1>";
	$html .= "<p>$co<br>\n";
	$html .= $member['Addr1'] . "<br>\n";
	$html .= $member['Addr2'] . "<br>\n";
	$html .= $member['Addr3'] . "<br>\n";
	$html .= $member['Addr4'] . "</p>\n";
	$html .= "</p>\n";
	
	$html .= "<p>Date: $dt</p>\n";
	$html .= "<p>Chamber of Commerce lunch $pDate &nbsp;&nbsp;&nbsp;&pound;$price</p>\n";
	$html .= "<p> </p>\n";
	$html .= "<p>Please remit to<br>\n";
	$html .= "    Barclays Bank<br>\n";
	$html .= "    Sort code 20-83-50<br>\n";
	$html .= "    Account 80846368</p>\n";
	$html .= "Please quote reference Lunch$ref</p>\n";
	$html .= "<p> </p>\n";
	
//	$html .= "</table>\n";

	$html .= "<p>Sudbury and District Chamber of Commerce and Industry<br>\n";
//	$html .= "<span style='font-size:80%'>1 Bank Buildings, Sudbury, Suffolk, CO10 2SP</span></p>\n";
	$html .= "<span style='font-size:80%'>" . ADDRESS . "</span></p>\n";
	$html .= "</body>\n";
	$html .= "</html>\n";

	$dta['email'] = $email;
	$dta['html'] = $html;
//	echo $dta['html'];
	return $dta;
}

// --------------------------------------------
//	Build the text of confirmation email
//
//	Parameter Booking reference
//
//	Returns	Array containing recipient's email
//			HTML for the message body
//
//  Fetch the booking details from the table
//	and assemble the text
//	Some details have to go into an html table	
// --------------------------------------------
function buildEmail($ref, $record)
{
	$dta = array();

	$email = $record['Email'];
	$co = $record['Company'];
	$name = $record['Name'];
	$html = "<html>\n";
	$html .= "<body>\n";
	$html .= "<p>Dear $name</p>\n";
	$html .= "<p>Thank you for booking onto the Chamber of Commerce Lunch.</p>\n";
	$html .= "Details are:";
	$html .= "<table style='border-collapse:collapse;'><tr>\n";
	$html .= "<td width='130px'>Company:</td>\n";
	$html .= "<td>$co</td>";
	$html .= "<tr><td>Attendees:</td></tr>\n";

	for ($x = 1; $x < 5; $x++)
	{
		$fld="Attendee" . $x;
		if ($record[$fld] != '')
		{
			$at = $record[$fld];
			$html .= "<tr><td>&nbsp;</td><td>$at</td></tr>\n";
		}
	}
	$html .= "</table>\n";

	$secretary = officerMail('Secretary');
	$paid = number_format($record['Price'],2);

	$html .= "<p>Payment received&nbsp;&nbsp;&nbsp; £$paid</p>\n";
	$html .= "<p>Your booking reference is $ref</p>\n";
	$html .= "<p>If you need to add more attendees, please submit them on a second booking.</p>\n";
	$html .= "<p>To cancel, please contact the secretary $secretary by the Monday lunchtime before the meeting.</p>\n";

	$html .= "<p>Sudbury and District Chamber of Commerce and Industry<br>\n";
//	$html .= "<span style='font-size:80%'>1 Bank Buildings, Sudbury, Suffolk, CO10 2SP</span></p>\n";
	$html .= "<span style='font-size:80%'>" . ADDRESS . "</span></p>\n";
	$html .= "</body>\n";
	$html .= "</html>\n";

	$dta['email'] = $email;
	$dta['html'] = $html;

	return $dta;
}

// --------------------------------------------
//	Fetch booking details from database
//
//	Returns record holding the booking
// --------------------------------------------
function fetchBooking($ref)
{
	global $mysqli;

	$sql = "SELECT * FROM bookings WHERE Ref=$ref ";			// Make next booking ref
	$result = $mysqli->query($sql)
		or die (mysqli_error($mysqli));
	$record = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_free_result($result);
	
	return $record;
}

// ----------------------------------------
//	Fetch the email of a Chamber officer
//
//	Parameter	Email type
// ----------------------------------------
function officerMail($type)
{
	global $mysqli;

	$sql = "SELECT * FROM officermails WHERE type='$type'";
	$result = $mysqli->query($sql)
		or die (mysqli_error($mysqli));
	$record = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$mail = $record['email'];
	mysqli_free_result($result);
	
	return $mail;
}

?>
</body>
</html>

