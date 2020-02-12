/* Drop down menu*/

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



