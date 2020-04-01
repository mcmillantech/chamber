<!doctype html>
 
<html>
 
<head>
<title>Sudbury Chamber of Commerce News</TITLE>
<meta name=viewport content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<link type="text/css" rel="stylesheet" href="Chamber.css">
<link type="text/css" rel="stylesheet" href="Menus.css">
<script src="Chamber.js"></script> 
</head>

<body onLoad="checkMobile()" >

<?php
    require "Header.html";
    require "connect2.php";

    $query = "SELECT * FROM news WHERE date > '2018' ORDER BY date DESC";
    $result = mysqli_query($mysqli, $query)
        or die ("There has been an error. We apologise for the inconvenience " 
            . mysqli_error($mysqli));

    echo "<h1>Chamber News</h1>";
    echo "<div style='padding-left:40px; padding-right:40px'>";

    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC))
        showLine ($line);

    echo "</div>";
    require "Footer.html";

function showLine($line)
{
    $Dt = $line['date'];
    $Dt = substr($Dt, 8, 2) . '/' . substr($Dt, 5, 2) . '/' . substr($Dt, 0, 4);

    echo "<h2>" . $line["title"] . "</h2>";
    echo "$Dt<br>";
    echo $line["item"];
}
?>

</BODY>
</HTML>