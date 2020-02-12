// ---------------------------------------------
//  Ajax scripts 
//
// ---------------------------------------------
/*
*/

var	company;

// -----------------------------------------------------
//  Event handler for search boxes
//
//  Called on key up in the box
//
//	Parameters	id of input box
//				Database table and column
//				Handler, not used in this application
// -----------------------------------------------------
function ASEditKeyUp(inputId, table, column, handler)
{
	var hAjax = openAjax();

	var el = document.getElementById(inputId);
	var str = el.value;

	hAjax.onreadystatechange=function()
	{
		if (ajax_response(hAjax))		// The list content is returned: 'rec', inx, co name
		{								// Place it in the list element and show it
		    var httxt = hAjax.responseText;
			var el = document.getElementById(listId);
			el.innerHTML = httxt;
			el.style.visibility = 'visible';
		}
	}
	var el = document.getElementById(inputId);
	var src = el.value;
	var str = "ASListBuild.php?srch=" + src;
//	alert (str);
	hAjax.open("GET",str,true);
	hAjax.send();
}

function ASCloseList(id)
{
	var el= document.getElementById(id + 'List');
	el.style.visibility = 'hidden';
}


function ajaxMouseOver(el)
{
	el.style.fontWeight="bold";
}

function ajaxMouseOut(el)
{
	el.style.fontWeight="normal";
}


function openAjax()
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{					// code for all but old IE
	  xmlhttp=new XMLHttpRequest();
	}
	else
	{					// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	hAjax = xmlhttp;
	return xmlhttp;
}

// ------------------------------------------------------
//	Show the form to select members to attend an event
//
//	This is called when the company is selected
//	AJAX returns a check box and name for each contact
//	Show them in element formContacts
//
//	Then make the form visible
// ------------------------------------------------------
function ASSelect(dta)
{
	company = dta;				// Store the returned company

	ASCloseList('mbr');
	var hAjax = openAjax();

	hAjax.onreadystatechange=function()
	{
		if ((this.readyState == 4 && this.status == 200))		// The list content is returned
		{								// Place it in the list element and show it
		    var httxt = hAjax.responseText;
			var el = document.getElementById('listMbrContacts');
//			var el = document.getElementById('memberForm');
			el.innerHTML = httxt;

			el = document.getElementById('mbrco');
			el.value = company;

			el = document.getElementById('memberContacts');
			el.style.visibility = 'visible';
			el.style.border = 'solid 1px';
			el.style.padding = '5px';
		}
	}
						// Encode for spaces - also ampersand in company name
	dta = dta.replace('&', "%26");
	var str = encodeURI("AJContacts.php?co=" + dta);
	hAjax.open("GET",str,true);
	hAjax.send();
}

