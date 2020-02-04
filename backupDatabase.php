<?php
/*
include "sessionsStart.php";
include "header.php";
*/
include "connect.php";
?>

<?php
/*
if($userRow['admin']==0){
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_admin';</SCRIPT>");
}
*/
?>

<?php
$configs = ConfigService::getService()->getConfigs();
$user = $configs['db_user'];
$pass = $configs['db_password'];
$db = $configs['db_database'];

//Tragen Sie hier Ihre Datenbankinformationen ein und den Namen der Backup-Datei
$mysqlDatabaseName =$configs['db_database'];
$mysqlUserName =$configs['db_user'];
$mysqlPassword = $configs['db_password'];
$mysqlHostName ='db680704532.db.1and1.com';
$mysqlExportPath ='database_backup/databaseBackup-'.date("Y-m-d") . '_' . date("H:i:s").'.sql';

//Bei den folgenden Punkten bitte keine Änderung durchführen
//Export der Datenbank und Ausgabe des Status
$command='mysqldump --opt -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' > ' .$mysqlExportPath;
exec($command,$output=array(),$worked);

switch($worked){
case 0:
echo 'Backup wurde erfolgreich im folgenden Pfad abgelegt: '.getcwd().'/' .$mysqlExportPath;
break;
case 1:
echo 'Es ist ein Fehler aufgetreten beim Exportieren von <b>' .$mysqlDatabaseName .'</b> zu '.getcwd().'/' .$mysqlExportPath .'</b>';
break;
case 2:
echo 'Es ist ein Fehler beim Exportieren aufgetreten, bitte prüfen Sie die folgenden Angaben: <br/><br/><table><tr><td>MySQL Database Name:</td><td><b>' .$mysqlDatabaseName .'</b></td></tr><tr><td>MySQL User Name:</td><td><b>' .$mysqlUserName .'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>' .$mysqlHostName .'</b></td></tr></table>';
break;
}

?>