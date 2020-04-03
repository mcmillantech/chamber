<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber
//  File	index.php
//
//  Author	John McMillan, McMillan Technology
// ------------------------------------------------------

?>
<?php
    require "view.php";

    $dta = array();				// Data for the view
    showView("indexHead.html", $dta);

    require "Header.html";
    require "connect2.php";
    
    $config = setConfig();

    $query = "SELECT * FROM news ORDER BY date DESC LIMIT 1";
    $result = mysqli_query($mysqli, $query)
        or die ("There has been an error. We apologise for the inconvenience " 
            . mysqli_error($mysqli));
    
    $line = mysqli_fetch_array($result, MYSQL_ASSOC);
    $dta['news'] = $line['item'];
    
    
    showView("index.html", $dta);

?>

