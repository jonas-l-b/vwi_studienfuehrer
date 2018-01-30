<?php

session_start();
include 'connect.php';

function endsWith($haystack, $needle)
{
    $length = strlen($needle);

    return $length === 0 ||
    (substr($haystack, -$length) === $needle);
}

if (!isset($_SESSION['userSession'])) {
	if(endsWith($url, 'vwi-karlsruhe.de') || endsWith($url, 'vwi-karlsruhe.de/')){
		echo ("<SCRIPT LANGUAGE='JavaScript'>alert('hi1');window.location.href='login.php';</SCRIPT>");
	}else{
		$url =  urlencode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
		echo ("<SCRIPT LANGUAGE='JavaScript'>alert('hi2');window.location.href='login.php?url=$url';</SCRIPT>");
	}

}

$query = $con->query("SELECT * FROM users WHERE user_ID=".$_SESSION['userSession']);
$userRow=$query->fetch_array();
$con->close();


?>
