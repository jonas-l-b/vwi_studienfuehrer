<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$table = $_POST['table'];

$myfile = fopen("uploads/$table.txt", "r") or die("Unable to open file!");

$sql = fread($myfile,filesize("uploads/$table.txt"));
if(mysqli_multi_query($con, $sql)){
	echo "SQL-Befehl erfolgreich ausgeführt.";
}else{
	echo "Beim Ausführen des SQL-Befehls ist ein Fehler aufgetreten.";
}

fclose($myfile);

?>