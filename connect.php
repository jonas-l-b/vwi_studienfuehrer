<?php

/*
$user = 'root';
$pass = '';
$db = 'studienfuehrer';
$con = mysqli_connect('localhost', $user, $pass, $db) or die("Unable to connect to database!");
$con->set_charset("utf8");
*/

//Steht hier nur zum Testen:
	require_once  __DIR__.'/Autoloader.php';
//TEST ENDE

$configs = ConfigService::getService()->getConfigs();

$user = $configs['db_user'];
$pass = $configs['db_password'];
$db = $configs['db_database'];
$con = mysqli_connect($configs['db_url'], $user, $pass, $db) or die("Unable to connect to database!");
$con->set_charset("utf8");


//if($con) echo ("<br> Sucessfully connected to database $db."); //CHECK

?>