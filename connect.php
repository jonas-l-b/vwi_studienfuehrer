<?php

//Steht hier nur zum Testen:
	require_once  __DIR__.'/Autoloader.php';
	require __DIR__ . '/vendor/autoload.php';
//TEST ENDE

$configs = ConfigService::getService()->getConfigs();
$user = $configs['db_user'];
$pass = $configs['db_password'];
$db = $configs['db_database'];
$con = mysqli_connect($configs['db_url'], $user, $pass, $db) or die("Kann keine Verbindung mit der Datenbank herstellen.");
$con->set_charset("utf8");
?>
