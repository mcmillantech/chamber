<?php
// --------------------------------------
//	Braintree bootstrap file.
// --------------------------------------

//require_once '../braintree-php-3.34.0/lib/Braintree.php';
//require_once 'braintree-php-3.34.0/lib/Braintree.php';
	$bt = $config['braintree'];
	require_once $bt;
define ("PP_TEST", 1);
//define ("PP_LIVE", 1);

// Sandbox keys
if(defined("PP_TEST"))
{
	echo "Test mode<br>";
	echo "$bt<br>";
	$merchantId = "w43wzttxmvmyrz8z";
	$publicKey = "mnzg56sz7b36f7r4";
	$privateKey = "555af850f9d9d277c5173c9feeaff227";
    $environment = 'sandbox';
}
// Production keys
else
{
	$merchantId = "rcy8fxhfbjfngsw4";
	$publicKey = "zdpvqj6w7hkzs8wf";
	$privateKey = "f4071f4f11f175c7194ec311e48f41dd";
    $environment = 'production';
}

?>

