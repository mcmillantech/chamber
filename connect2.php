<?php
// ------------------------------------------------------
//  Project	Sudbury Chamber
//  File	Connect2.php
//  		Actually common database code
//
//  Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------

const USER_EMAIL = "john@mcmillantech.co.uk";
const SEC_EMAIL = "secretary@sudbury.org.uk";
const BRIEF_LAST = "'2018-06-01'";
const TEST_PAY_RECEIVED = false;				// For Braintree testing
const ADDRESS = "Unit 4, Byford Road Sudbury CO10 2YG";
const PHONE = "07850 667901 ";

// Can't use a const for the following
$msgMailFail = "Please try again or contact the Chamber secretary on "
	. "<a href='MailTo:" . SEC_EMAIL . "'>" . SEC_EMAIL . "</a> or "
	. PHONE . "to book manually.";

const ERR_COMMON_CONFIG = 11;
const ERR_CONNECT = 12;
const ERR_MB_CL_PREP1 = 21;
const ERR_MB_CL_BIND1 = 22;
const ERR_MB_CL_EX1 = 23;
const ERR_MB_CL_PREP2 = 24;
const ERR_MB_CL_BIND2 = 25;
const ERR_MB_CL_EX2 = 26;
const ERR_MB_BR_EX1 = 31;
const ERR_BM2_PREP1 = 41;
const ERR_BM2_BIND1 = 42;
const ERR_BM2_EX1 = 43;
const ERR_BM2_PREP2 = 44;
const ERR_BM2_BIND2 = 45;
const ERR_BM2_EX2 = 46;
const ERR_PA_CONF = 51;


    $config = setConfig();				// Get DB connection parameters

    $mysqli = mysqli_connect 
            ($config['dbhost'], $config['dbuser'], $config['dbpw'], $config['dbname'])
        or die("Could not connect : " . $mysqli -> error);

// ----------------------------------------------
//	Read the database access parameters from
//	the config file
//
//	Returns array of parameters
// ----------------------------------------------
function setConfig()
{
    $hfile = fopen('config.txt', 'r');
    if (!$hfile)
            myError(ERR_COMMON_CONFIG, "Could not open config file");
    $config = array();
    while (!feof($hfile)) {
        $str = fgets($hfile);
        sscanf($str, '%s %s', $ky, $val);
        $config[$ky] = $val;
    }
    fclose ($hfile);
    $_SESSION['config'] = $config;
    return $config;
}

// ---------------------------------------
//	Report error for this site
//
//	Parameter message id
// ---------------------------------------
function myError($errno, $msg)
{
	echo "<br><br>We are sorry, an error has occurred. Please send a "
		. "message to " . USER_EMAIL . " quoting error $errno and "
		. "the following message:";
	echo "<br><br>$msg<br>";
	die();
}

?>
