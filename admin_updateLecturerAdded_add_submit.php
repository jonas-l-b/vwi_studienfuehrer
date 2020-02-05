<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];
$name = $_POST['name'];

$sql1 = "
	INSERT INTO `lecturers`(`name`, `user_ID`, `time_stamp`, `active`)
	VALUES ('$name', ".$userRow['user_ID'].", now(), 1)
";
$sql2 = "DELETE FROM `ADDED_LECTURERS` WHERE `ID` = $id";

if(mysqli_query($con, $sql1)){
	echo "Hinzufügen erfolgreich.";
	if(mysqli_query($con, $sql2)){
		echo " Löschen aus dieser Tabelle erfolgreich.";
	}else{
		echo " Löschen aus dieser Tabelle nicht erfolgreich.";
	}
}else{
	echo "Hinzufügen nicht erfolgreich. Das kann verschiedene Gründe haben - bspw. könnte der Veranstaltungsname bereits existieren.";
}
?>