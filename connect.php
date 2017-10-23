<?php

//Steht hier nur zum Testen:
	require_once  __DIR__.'/Autoloader.php';
	require __DIR__ . '/vendor/autoload.php';
	$loader = new Twig_Loader_Filesystem('templates');
	$twig = new Twig_Environment($loader, array(
		//'cache' => 'templates/cache',
		'cache' => false,
	));
	require_once 'cacheIni.php';
	use Monolog\Logger;
	use Monolog\Handler\StreamHandler;
	
	$logger = new Logger('Generell'); //Beschreibt alle generellen Logs
	//$db_logger = new Logger('Datenbank'); //Beschreibt allen Datenbank ÄNDERUNGEN
	$u_logger = new Logger('Nutzung'); //Beschreibt alle Logs zum Nutzungsverhalten
	$stream = new StreamHandler(__DIR__.'/logs/studienfuehrer.log', Logger::DEBUG);
	$logger->pushHandler($stream);
	//$db_logger->pushHandler($stream);
	$u_logger->pushHandler($stream);
//TEST ENDE

$configs = ConfigService::getService()->getConfigs();
$user = $configs['db_user'];
$pass = $configs['db_password'];
$db = $configs['db_database'];
if(!$con = mysqli_connect($configs['db_url'], $user, $pass, $db)){
  $logger->info('Kann keine Verbindung mit der Datenbank herstellen.');
  die("Kann keine Verbindung mit der Datenbank herstellen.");
}
$con->set_charset("utf8");
?>
