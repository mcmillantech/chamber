<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber of Commerce
//	File	members.php
//	
//  Maintain member database
//	Author	John McMillan
//
//	Version	1.3
//
//	15/10/12	UpdateInvoice added
// ------------------------------------------------------
/*
function startup()
function MoveToRecord()
function search()
function fetchRow()
function doUpdate()
function updateAddr()
function updateInvoice()
function updateContacts()
function updateServices()
function updatePayments()
function updateGold()
function mtencode($dsc)
function doInsert()
function doDelete()
function checkEmailChange()
*/


// ------------------------------------------------
//  Start up
//
//  Read 1st record, set defaults
// ------------------------------------------------
function startup()
{
	global $mbrLine, $row, $dbConnection;

	$query = "SELECT * FROM members ORDER BY Company";
	$result = mysqli_query($dbConnection, $query) 
		or die("Query failed $query: " . mysqli_error($dbConnection));
	$last = mysqli_num_rows($result) - 1;
	$row = 0;
	$view = 'addr';
	$mbrLine = mysqli_fetch_array($result, MYSQLI_ASSOC);
//print_r($mbrLine);
	$_SESSION['mbrLine'] = $mbrLine;
	$_SESSION['last'] = $last;
	$_SESSION['row'] = $row;
	$_SESSION['view'] = $view;
	$_SESSION['key'] = $mbrLine['Company'];
	$_SESSION['index'] = $mbrLine['Company'];

	mysqli_free_result($result);

}

// ------------------------------------------------
//  Move to a different row
//
//  Row number is in HTTP parameter 'record'
// ------------------------------------------------
function MoveToRecord()
{
	global $mbrLine, $row, $dbConnection;

	if ($row < 0)
		$row = 0;
	$last = $_SESSION['last'];
	if ($row > $last)
		$row = $last;

	$query = "SELECT * FROM members ORDER BY Company LIMIT $row,1";
	$result = mysqli_query($dbConnection, $query) 
		or die("Query failed : " . mysqli_error($dbConnection));
	$mbrLine = mysqli_fetch_array($result, MYSQLI_ASSOC);

	mysqli_free_result($result);
	$_SESSION['row'] = $row;
	$_SESSION['index'] = $mbrLine['Company'];
	$_SESSION['mbrLine'] = $mbrLine;
}

// ------------------------------------------------
//  Search for a record
//
//  Search string returned in post
// ------------------------------------------------
function search()
{
	global $row, $dbConnection;
						// Locate the search match - get the match in $cos	

//	$str = $_POST['Search'] . '%';
	$str = $_GET['v'] . '%';
	$query = "SELECT Company FROM members WHERE Company LIKE '$str'";
	$result = mysqli_query($dbConnection, $query) 
		or die("Search 1 failed $query : " . mysqli_error($dbConnection));
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$cos = $line['Company'];
	mysqli_free_result($result);
						// Now find the index to the row
	$query = "SELECT Company FROM members WHERE Company <= '$cos'";
	$result = mysqli_query($dbConnection, $query) 
		or die("Search 2 failed $query : " . mysqli_error($dbConnection));
	$row = mysqli_num_rows($result) - 1;
	$_SESSION['row'] = $row;
	mysqli_free_result($result);
						// And read the record
	$_SESSION['row'] = $row;
}

// ------------------------------------------------
//  fetchRow
//
//  Fetch the current row from database
//  Not used
// ------------------------------------------------
function fetchRow()
{
	global $row, $mbrLine, $dbConnection;

	$query = "SELECT * FROM members ORDER BY Company LIMIT $row,1";
	$result = mysqli_query($dbConnection, $query) 
		or die("Fetch row failed $query : " . mysqli_error($dbConnection));
	$mbrLine = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_free_result($result);
	$_SESSION['index'] = $mbrLine['Company'];
}

// ------------------------------------------------
//  Perform update
//
//  As there are five sub forms, an update is 
//  needed for each
// ------------------------------------------------
function doUpdate()
{
//	checkEmailChange();
	global $view, $mbrLine, $dbConnection;

	foreach($_POST as $key => $str)
		$_POST[$key] = addslashes($str);
	
	$index = $_SESSION['mbrLine']['inx'];	// The original customer name

	$_SESSION['tab'] = $_POST['thisTab'];

	$upd = updateAddr();
	$upd .= updateInvoice();
	$upd .= updateServices();
	$upd .= updatePayments();
	$upd .= updateGold();

	$sql = "UPDATE members SET "
		. "Company='" . $_POST['Company']
		. "', Class='" . $_POST['Class']
		. "', Class2='" . $_POST['Class2']
		. "', ShortDsc='" . $_POST['ShortDsc']
		. $upd
		. " WHERE inx = '$index'";

	mysqli_query($dbConnection, $sql)
		or die ("Update failed : " . mysqli_error($dbConnection));
}

// ------------------------------------------------
//  The page updates
//
//  Take data from forms, build SQL
// ------------------------------------------------
function updateAddr()
{
	global $mbrLine;

	$str = "', Addr1='" . $_POST['Addr1']
		. "', Addr2='" . $_POST['Addr2']
		. "', Addr3='" . $_POST['Addr3']
		. "', Addr4='" . $_POST['Addr4']
		. "', Postcode='" . $_POST['Postcode']
		. "', Phone='" . $_POST['Phone']
		. "', Fax='" . $_POST['Fax']
		. "', EmailSale='" . $_POST['EmailSale'];
	return $str;
}

// ------------------------------------------------
function updateInvoice()
{
	global $mbrLine;

	$str = "', InvA1='" . $_POST['InvA1']
		. "', InvA2='" . $_POST['InvA2']
		. "', InvA3='" . $_POST['InvA3']
		. "', InvA4='" . $_POST['InvA4']
		. "', InvPostCode='" . $_POST['InvPostCode'];
	return $str;
}

// ------------------------------------------------
// Removed with new contact scheme
function updateContacts()
{
	$str = "', Contact1='" . $_POST['Contact1']
		. "', FirstName='" . $_POST['FirstName']
		. "', Contact2='" . $_POST['Contact2']
		. "', Email1='" . $_POST['Email1']
		. "', Email2='" . $_POST['Email2']
		. "', Direct1='" . $_POST['Direct1']
		. "', Direct2='" . $_POST['Direct2']
		. "', Position1='" . $_POST['Position1']
		. "', Position2='" . $_POST['Position2'];
	return $str;
}

// ------------------------------------------------
//  The next pages have data from check boxes
//
//  HTML only posts data if the box is ticked
//  so if there's a post for that field, it's true
// ------------------------------------------------
function updateServices()
{
	$str = "', Gold="
		. ((array_key_exists('Gold',$_POST)) ? 1 : 0)
		. ", Suffolk="
		. ((array_key_exists('Suffolk',$_POST)) ? 1 : 0)
		. ", XtraSvs="
		. ((array_key_exists('XtraSvs',$_POST)) ? 1 : 0)
		. ", XtraAmt=" . $_POST['XtraAmt']
		. ", XtraDsc='" . $_POST['XtraDsc']
		. "', SubsFlag="
		. ((array_key_exists('SubsFlag',$_POST)) ? 1 : 0)
		. ", SubsRate=" . $_POST['SubsRate']
		. ", WebsFlag="
		. ((array_key_exists('WebsFlag',$_POST)) ? 1 : 0)
		. ", WebRate=" . $_POST['WebRate'];
	return $str;
}

// ------------------------------------------------
function updatePayments()
{
	$str = ", Method='" . $_POST['Method']
		. "', Member=" . $_POST['Member']
//		. ((array_key_exists('Member',$_POST)) ? 1 : 0)
		. ", TotalDue=" . $_POST['TotalDue']
		. ", Affiliate="
		. ((array_key_exists('Affiliate',$_POST)) ? 1 : 0)
		. ", Receipt=" . $_POST['Receipt']
		. ", PaidThis="
		. ((array_key_exists('PaidThis',$_POST)) ? 1 : 0)
		. ", Paid1="
		. ((array_key_exists('Paid1',$_POST)) ? 1 : 0)
		. ", DatePaidT='" . $_POST['DatePaidT']
		. "', TresNotes='" . $_POST['TresNotes'];
	return $str;
}

// ------------------------------------------------
function updateGold()
{
	$dsc = $_POST['Description'];		// Change special characters in dsc to html
	$dsc = htmlentities($dsc, ENT_QUOTES);
	$dsc = nl2br($dsc);
	$str = "', Website='" . $_POST['Website']
		. "', Description='" . $dsc 
		. "', Logo='" . $_POST['Logo'] 
		. "', Picture='" . $_POST['Picture'] 
		. "'";
	return $str;
}

/*
function mtencode($dsc)
{
print "<pre>$dsc\n";
print "update Gold $dsc</pre>";
return $dsc;
} */

// ------------------------------------------------
//  Insert new member
//
// ------------------------------------------------
function doInsert()
{
	global $row, $mbrLine, $dbConnection;

	$row = 0;
	$ins = "INSERT INTO members (Company) VALUES ('') "
		. "ON DUPLICATE KEY UPDATE Company = ''";
	$query = "SELECT * FROM members ORDER BY Company LIMIT $row,1";
	mysqli_query ($dbConnection, $ins);
	$result = mysqli_query($dbConnection, $query)
		or die("Insert search failed $query : " . mysqli_error($dbConnection));
	$mbrLine = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_free_result($result);
//	$_SESSION['index'] = '';
}

// ------------------------------------------------
//  Insert new member
//
// ------------------------------------------------
function doDelete()
{
	global $dbConnection;

	$index = $_SESSION['mbrLine']['inx'];	// The original customer name
//echo "Deleting record $index <br>\n";
	$sql = "DELETE FROM members WHERE inx = $index";
	$result = mysqli_query ($dbConnection, $sql)
		or die("Delete failed $query : " . mysqli_error($dbConnection));

	MoveToRecord();
}

// ------------------------------------------------
//	Probably no need - was for MH
//
// ------------------------------------------------
function checkEmailChange()
{
	global $mbrLine;

	$txt = "";
	$em1Old = $mbrLine['Email1'];
	$em2Old = $mbrLine['Email2'];
	$em1New = $_POST['Email1'];
	$em2New = $_POST['Email2'];
//echo "$em1Old $em1New $em2Old $em2New <br>"; 
	if (($em1Old != $em1New) || ($em2Old != $em2New))
		$txt = "Michael\r\n";
	else
		return;
		
	$txt .= "The email has been changed on the database for " . $mbrLine['Company'] . "\r\n";
	$txt .= "The old values were $em1Old and $em2Old\r\n";
	$txt .= "The new values are $em1New and $em2New\r\n\r\n";
	$txt .= "Generated automatically\r\n";
//echo $txt;

	$subject = 'Chamber Data Changed';
	$headers = 'From: chamber2@sudbury.org.uk' . "\r\n" .
	    'Reply-To: secretary@sudbury.org.uk' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();
	$from = 'mheyland@sudbury.org.uk';
	$to = 'andy@andyhowesphotography.com, mcmillan.technology@uwclub.net, whatif.rb@gmail.com';

	$reply = mail($to, $subject, $txt, $headers);
	if ($reply)
		print "Mail sent re email change";
	else
		print "Email failure";

}
?>
