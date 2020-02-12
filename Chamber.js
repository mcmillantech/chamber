// ------------------------------------------------------
//  Project	Sudbury Chamber
//	File	Chamber.js
//
//	Javascript functions	
//
//	Author	John McMillan, McMillan Technology
// ------------------------------------------------------
/*
function checkMobile()
function setMobile()
function setDesktop()
function goHome()
function goMenu()
function goBusdir()
function mopen(id)
function mclose()
function mclosetime()
function mcancelclosetime()
*/
mobile = 0;

function checkMobile()
{
//	alert ("Check");

	var size = screen.width;
	if (size < 810)
		mobile = 1;

	if (mobile==1)
		setMobile();
	else
		setDesktop();
}

function setMobile()
{
/*	var el = document.getElementById("mobileHeader");
//	el.style.display = 'block';
	el = document.getElementById("deskHeader");
	el.style.display = 'none';
	el = document.getElementById("RightDeskBar");
	if (el != null)
		el.style.display = 'none';
	el = document.getElementById("leftDeskMenu");
	el.style.display = 'none';
*/
	winWidth = window.innerWidth;		// Set width of main panel
//	if (winWidth < 800) winWidth = 800;
	panelWidth = winWidth * 0.95;
//	alert (panelWidth);
	
	el = document.getElementById("MainPanel");
	el.style.width = panelWidth+'px';
//	el.style.width = '96%';

}

// -------------------------------------------------
// Setup for desktop viewer
// 
// Set the widths of main panel, header and footer
// Target is 900px, 
// -------------------------------------------------
function setDesktop()
{
	winWidth = window.innerWidth;	// Find width of main panel
//	var pSize = 650;
	var pSize = 900;                // Set target width
	
	if (winWidth < pSize)           // Allow for small screens (remove?)
	{
		panelWidth = winWidth * 0.9;    // Set to 90 and 95%
		topWidth  = winWidth * 0.95;
	}
	else
	{
		panelWidth = pSize;     // Set to 900 and 1000px
		topWidth  = pSize + 100;
	}
                                        // Set width and margin for main panel
	var el = document.getElementById('MainPanel');
	el.style.width = panelWidth+'px';
	var mgn = (winWidth - panelWidth)/2 + 'px';
	el.style.marginLeft = mgn;
                                        // and now for header and footer
	el = document.getElementById('header');
	el.style.width = topWidth+'px';
	var mgn = (winWidth - topWidth)/2 + 'px';
	el.style.marginLeft = mgn;
	el = document.getElementById('footer');
	el.style.width = topWidth+'px';
	el.style.marginLeft = mgn;
	

	el = document.getElementById("deskHeader");
//	el.style.display = 'block';
	var el = document.getElementById("RightDeskBar");
	if (el !== null)
//		el.style.display = 'block';
	el = document.getElementById("leftDeskMenu");
//	el.style.display = 'block';


}

function goHome()
{
	window.location.assign("index.html");
}

function goMenu()
{
	window.location.assign("Mobilemenu.html");
}

function goBusdir()
{
	window.location.assign("Mobdirectory.html");
}

<!-- Drop down menu -->

var timeout     = 00;
var closetimer	= 0;
var ddmenu      = 0;

// open drop down
function mopen(id)
{	
	// cancel close timer
	mcancelclosetime();

	if(ddmenu)
		ddmenu.style.visibility = 'hidden';

	// show new menu
	ddmenu = document.getElementById(id);
	ddmenu.style.visibility = 'visible';

}
// close showed layer
function mclose()
{
	if(ddmenu)
		ddmenu.style.visibility = 'hidden';
}

// go close timer
function mclosetime()
{
	closetimer = window.setTimeout(mclose, timeout);
}

// cancel close timer
function mcancelclosetime()
{
	if(closetimer)
	{
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}

// -------------------------------------------------
//	Validate member booking form
//
//	Check all the boxes, and input fields
//
//	There must be at least one contact selected
// -------------------------------------------------
function validateMbrBookForm()
{
	var checks = document.getElementsByClassName('ctcheck');
	var valid = false;

	for (i = 0; i < checks.length; i++) 
	{
		if (checks[i].checked == true)
		{
			valid = true;
		}
	}
	var el = document.getElementById('other1');
	if (el.value != '')
		valid = true;
	el = document.getElementById('other2');
	if (el.value != '')
		valid = true;

	if (valid)
	{
		el  = document.getElementById('message');
		el.innerHTML = "Please wait while we connect to the payment process";
	}
	else
		alert ("Please select at least one attendee");
	return valid;
}
