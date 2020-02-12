<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber of Commerce
//	File	bookingpaid.php
//			Return from Braintree
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
	require_once "bootstrap.php";

										// Instantiate a Braintree Gateway 
$gateway = new Braintree_Gateway(
	[
	    'environment' => $environment,
	    'merchantId' => $merchantId,
	    'publicKey' => $publicKey,
	    'privateKey' => $privateKey
	]
);

	$nonceFromTheClient = $_POST["nonce"];
										// Then, create a transaction:
	$result = $gateway->transaction()->sale([
	    'amount' => $_POST["amount"],
	    'paymentMethodNonce' => "$nonceFromTheClient",
	    'options' => [ 'submitForSettlement' => true ]
	]);

	if ($result->success) {
		doSuccess($result);
	} else if ($result->transaction) {
		doFailure($result->transaction->processorResponseText);
	} else {
		$msg = $result->message;
	    echo "We are sorry, payment has not been accepted:<br>";
	    echo "Message was: $msg <br><br>";
	    echo "Please try again or contact secretary@sudbury.org.uk to book manually";
	}

function doFailure($msg)
{
	global $msgMailFail;

    echo "We are sorry, payment has not been accepted:<br>";
	echo "The message was $msg<br>";
	echo $msgMailFail;
/*	echo "Please try again or contact the Chamber secretary on ";
	echo "<a href='MailTo:secretary@sudbury.org.uk'>chamber@sudbury.org.uk</a>";
	echo "or 01787 227722 to book manually."; */
}

// -----------------------------------
//	Process successful receipt
//
// -----------------------------------
function doSuccess($BTresult)
{
	$config = setConfig();

			// Record the success into booking table
	$dbConnection = mysqli_connect ('localhost', $config['dbuser'], $config['dbpw'])
		or die("Could not connect : " . mysqli_connect_error());
	mysqli_select_db($dbConnection, $config['dbname']) 
		or die("Could not select database : " . mysqli_error($dbConnection));

	echo "<h3>Booking confirmed</h3>";
	$ref = $_SESSION['bookingRef'];
	
	$query = "SELECT * FROM bookings WHERE ref=$ref";	// Fetch the booking from the table
	$result = mysqli_query($dbConnection, $query)
		or die ("There has been an error. Please send the following message to admin@sudbury.org.uk<br>" 
			. mysqli_error($dbConnection));
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
	$dta = buildEmail($ref);
	$htmlText = $dta['html'];
	$to = $dta['email'];
	sendMail($htmlText, $to);

	echo "<p>We have taken &pound;" . number_format($booking['Price'],2) . " from your card</p>";
	echo "<p>We have send a confimation and receipt to " . $booking['Email'];
	
			// ------- Mark booking as complete
	$id = $BTresult->transaction->id;
//	echo "success!: $id";
	$last4 = $BTresult->transaction->creditCard["last4"];
	$sql = "UPDATE bookings SET State=1, Method='Btr', Trans='$id', Last4='$last4' " 
		. "WHERE ref=$ref";
//	echo " $sql";
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
function sendMail($message, $to)
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
	$reply = mail($to, $subject, $message, $headers);
	if ($reply)
		print "Mail sent to $to<br>";
	else
		print "Mail failed to $to<br>";
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
function buildEmail($ref)
{
	global $mysqli;

	$dta = array();

	$sql = "SELECT * FROM bookings WHERE Ref=$ref ";			// Make next booking ref
	$result = $mysqli->query($sql)
		or die (mysqli_error($mysqli));
	$record = mysqli_fetch_array($result, MYSQLI_ASSOC);

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
	$html .= "<span style='font-size:80%'>" . ADDRESS . "</span></p>\n";
	$html .= "</body>\n";
	$html .= "</html>\n";

	$dta['email'] = $email;
	$dta['html'] = $html;
	return $dta;
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

