<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber of Commerce
//	File	BookMeeting.php
//			Book onto business briefing
//
//	Author	John McMillan, McMillan Technolo0gy
//
//	This is called from briefings.php
//	Parameter is the briefing data
// ------------------------------------------------------

?>
<!doctype html>
 
<html>
 
<head>
<title>Book Sudbury Chamber Meeting</TITLE>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script>
<style>
.bookForm
{
	position:		absolute;
	margin-left:	60px;
	width:			300px;
	padding:		5px;
	border:			solid 1px;
	visibility: 	hidden;
}
</style>

</head>

<body onLoad="checkMobile()" >

<?php
	require "Header.html";
	require "connect2.php";
	require "AutoSearch.php";

	$config = setConfig();
	$sqlD = $_GET['date'];

								// Make a printing date and fetch the speaker from table Briefings
	$pDate = substr($sqlD, 8, 2) . '/' . substr($sqlD, 5, 2) . '/' . substr($sqlD, 0, 4);
	$sql = "SELECT * FROM briefings WHERE Date='$sqlD'";
	$result = $mysqli->query($sql);
	$meet = mysqli_fetch_array($result, MYSQL_ASSOC);
	$speaker = $meet['Speaker'];
	
	echo "<div id='meeting_details'>";			// Present the meeting details
		echo "<h1>Meeting Booking: ";
		echo "$pDate </h1>";
		echo "<h3>Speaker</h3>";
		echo $meet['Speaker'];
		echo "&nbsp;&nbsp;&nbsp;" . $meet['Bio'];
	echo "</div>";
	echo "<div style='min-height:100px'>";
		echo "<h3>Topic</h3>";
	   	echo $meet['Subject'];
	 echo "</div>";
	 // From here, display a table. First row presents buttons for member and guest
	 // Below that is a row containing a cell with id 'vary'.
	 // Handlers from the buttons copy html forms into that cell
?>
   	
	<table>
		<tr>
			<td>Please select whether you are a Chamber member or a guest visitor</td>
		</tr>
		<tr>
			<td>
			<button onClick='doMember()'>Chamber member</button>&nbsp;&nbsp;
			<button onClick='doGuest()'>Non member</button>
			</td>
		</tr>
		<tr style='height:180px; vertical-align:top'>
			<td id='vary'>
			</td>
		<tr>
   	</table>

<?php
	memberForm($sqlD);
	guestForm($sqlD);

	require "Footer.html";	

// ----------------------------------------------
//	Content form to process members
//
//  The doGuest() handler copies the html
//	from the member id into the table data
//  element 'vary'.
//
//	The form presents a search box. On selection
//	that opens the member contacts form.
// ----------------------------------------------
function memberForm($sqlD)
{
?>
	<div id='member' style='visibility: hidden'>
		<p>Start typing your company name in the box. 
   		Click on your company when it appears in the drop down list.<br></p>
		<div style='height:20px'>
			<span style='margin-left:20px; margin-right:20px'>Member company</span>
<?php
			$as = new AutoSearch('mbr', 'members', 'Company', 'memberList');
			$as->spanStyle('position:absolute;');
			$as->show(20);
			mbrContactsForm($sqlD);

		echo "</div>";
	echo "</div>";
}


// ----------------------------------------------
//	Content of guest form
//
//  The doGuest() handler copies the html
//	from the guest id into the table data
//  element 'vary'.
// ----------------------------------------------
function guestForm($sqlD)
{
	echo "<div id='guest' style='width 250px; display: none'>";
	echo "<form id='guestForm' ";
		echo "action='BookMeeting2.php?date=$sqlD&type=guest' method='post'";
		echo " onsubmit='return validateMbrBookForm()'>";
		echo "<div class='frmPrompt'>Organisation</div>";
		echo "<span class='frmInput'><input type='text' id='mbrco' name='mbrco'></span>";

		echo "<p style='clear:both'> <br></p>";
		echo "<span class='frmPrompt'>Attendees</span>";
		echo "<span class='frmInput'>"
			."<input type='text' id ='other1' name='attendee1' value=''></span>";
		echo "<p style='clear:both'>";
		echo "<span class='frmInput'>"
			."<input type='text' id ='other2' name='attendee2' value=''></span>";
		echo "<p style='clear:both'> </p>";
		echo "<span class='frmInput'>"
			."<input type='text' id ='other3' name='attendee3' value=''></span>";
		echo "<p style='clear:both'> </p>";
		echo "<span class='frmInput'>"
			."<input type='text' id ='other4' name='attendee4' value=''></span>";

		echo "<p style='clear:both'> <br></p>";
		echo "<span class='frmPrompt'>Contact</span>";
		echo "<span class='frmInput'>"
			."<input type='text' id ='contact' name='contact' value=''></span>";

		echo "<p style='clear:both'> <br></p>";
		echo "<span class='frmPrompt'>Contact email</span>";
		echo "<span class='frmInput'>"
			."<input type='text' id ='email' name='email' value=''></span><br><br>";
		echo "<input type='submit' value='Submit'>";
	echo "</form>";
	echo "\n</div>";
}

// ------------------------------------------
//	Form to select contacts for members
//
//	Allows selection from recorded contacts
//	and others to be input
//
//	Parameter	SQL date
// -------------------------------------------
function mbrContactsForm($sqlD)
{
	echo "\n<div id='memberContacts' style='visibility:none'>";
		echo "<form id='memberForm' action='BookMeeting2.php?date=$sqlD&type=member' method='post'";
			echo " onsubmit='return validateMbrBookForm()'>";
			echo "<b>Select attendees</b>";

								// This division is filled in by AJContacts.php
								// from ASSelect (Ajax.js)	
			echo "\n<div id='listMbrContacts'>";
			echo "</div>";

			echo "\n<br><b>Other attendees</b><br>";
			echo "<input type='hidden' id='mbrco' name='mbrco'><br>";
			echo "<input type='text' id ='other1' name='other1' value=''><br>";
			echo "<input type='text' id ='other2' name='other2' value=''><br><br>";

			echo "<input type='submit' value='Submit'>";
		echo "</form>";
	echo "</div>";
	echo "<div id='message'>&nbsp</div>\n";
}

?>

<script>

// ----------------------------------------------
//	Handler for chamber member
//
//	Fetch the member form, which is in id member
//	and copy the html of the form into 
//	element vary
// ----------------------------------------------
function doMember()
{
	var el = document.getElementById("member");
	var txt = el.innerHTML;
	el = document.getElementById('vary');
	el.innerHTML = txt;
}

// ----------------------------------------------
//	Handler for guest
//
//	Same process as doMember
// ----------------------------------------------
function doGuest()
{
	var el = document.getElementById("guest");
	var txt = el.innerHTML;
	el = document.getElementById('vary');
	el.innerHTML = txt;
}

// -----------------------------------------------------
//  Event handler for search input box
//
//  This is called on key up in the box
//
//	Makes an AJAX call to ASListBuild which returns
//	JSON with an array of items: 'rec', value1, value2
// -----------------------------------------------------
function ASEditKeyUp(id, table, column, handler)
{
	var inputId = id + 'Input';			// Locate the input box and dd list
	var listId = id + 'List';
	var hAjax = openAjax();
										// Fetch the text from the input
	var el = document.getElementById(inputId);
	var str = el.value;

	hAjax.onreadystatechange=function()
	{
		if (this.readyState == 4 && this.status == 200)		// The list content is returned
		{
			var html = '';						// Build the html for the list
		    var httxt = hAjax.responseText;
		    var obj = JSON.parse(httxt);		// Build an object (array) from the JSON
		    for (i=0; i<obj.length; i++)		// Trawl each record
		    {
			    var rec = obj[i];
			    var co = rec[2];				// Extract the company
				var call = 'ASSelect("' + co + '")';	// Click handler
				html += "<span class='pcl' onMouseOver='ajaxMouseOver(this)' "
					+ "onMouseOut='ajaxMouseOut(this)' onClick='" + call + "'>" + co + "</span><br>";
			}
			var el = document.getElementById(listId);	// and show in the drop down
			el.innerHTML = html;
			el.style.visibility = 'visible';			// Show the drop down
		}
	}
	var el = document.getElementById(inputId);
	var src = el.value;
	var str = "AjListBuild.php?srch=" + src;
	hAjax.open("GET",str,true);
	hAjax.send();
}

</script>

</body>
</html>
