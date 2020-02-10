<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];
$identifier = $_POST['identifier'];
$subject_name = $_POST['subject_name'];


$sql1 = "
	UPDATE `subjects` SET `active`= 0
	WHERE `identifier` = '$identifier'
";

$sql2 = "DELETE FROM `DELETED_SUBJECTS` WHERE `id` = $id";

if(mysqli_query($con, $sql1)){
	echo "Erfolgreich aus Studi gelöscht.";
	if(mysqli_query($con, $sql2)){
		echo " Erfolgreich aus dieser Tabelle gelöscht.";
	}else{
		echo " Löschen aus dieser Tabelle nicht erfolgreich.";
	}
}else{
	echo "Löschen aus Studi nicht erfolgreich. Das kann verschiedene Gründe haben.";
}
?>