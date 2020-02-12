<script src="Ajax.js"></script> 

<?php
// -------------------------------------------------
//	File	autosearch.php
//
//	Look up combo class
//	The show method provides an event handler
//	for onKeyUp in the entry element
//		
// -------------------------------------------------
/*  Typical use in the calling page:
	echo '<span style="margin-left:20px">New Layer: Product</span>';
	$as = new AutoSearch('newrow', 'ProductCodes', 'Description', 'newRow');
	$as->spanStyle('position:absolute; left:214px');
	$as->show($left);

The handler makes an AJAX call with Typical SQL:
	$sql = "SELECT DISTINCT $columns FROM $table WHERE $column LIKE '$srch%' ORDER BY $column"
		. " LIMIT 0,15";

That populates a list with typical rows:
	$dta = $record[$column];
	$call = $handler . '("' . ($dta) . '")';
	$list .= "<span class='pcl' onMouseOver='ajaxMouseOver(this)' "
		. "onMouseOut='ajaxMouseOut(this)' onClick='$call'>"
		. $dta
		. "</span><br>";

and the second handler fetches and processes the selected record.
*/
//session_start();

class AutoSearch
{
	private	$dbTable;
	private	$dbColumn;
	private	$listHandler;
	private	$inputId;
	private	$listId;
	private $listStyle;
	private $spanStyle;

// -------------------------------------------------
//	Parameters
//		Suffix of id for the html elements
//		Database table and column
//		JS event handler
// -------------------------------------------------
	function __construct ($id, $dbTable, $dbColumn, $handler)
	{
		$this->listStyle = "position: absolute; "
		. "top: 22px;"
		. "border-style: solid;"
		. "border-width: 1px;"
		. "padding-left: 5px;"
		. "padding-right: 5px;"
		. "visibility: hidden;"
		. "background-color: white;"
		. "width: 150%;"
		. "color: black;"
		. "z-index: 1;";
		$this->inputId = $id . 'Input';
		$this->listId = $id . 'List';
		$this->dbTable = $dbTable;
		$this->dbColumn = $dbColumn;
		$this->listHandler = $handler;
		$this->handler = "searchComboKeyUp";
		
		$vars = array(
			'table' => $dbTable,
			'column' => $dbColumn,
			'id' => $id
		);
	}

	function spanStyle($style)
	{
		$this->spanStyle = $style;
	}
	
// -------------------------------------------------
//	Show the input box and and position the list
//
//	The input box has a handler to a JavaScript
//	function ASEditKeyUp
//
//	Parameter	Left position in px
// -------------------------------------------------
	public function show($left)
	{
		$handler = 'ASEditKeyUp("mbr", "members", "Company", "makeASList")';

						// Wrap the construction in a span element
		echo "<span style='$this->spanStyle'>";
		echo "<input type='text' name='$this->inputId' id='$this->inputId' onKeyUp='$handler'>";
		echo "\n";
		echo "<div class='$this->listId' id='$this->listId' style='$this->listStyle'>Hello</div>";
		echo '</span>';
	}
}

?>
