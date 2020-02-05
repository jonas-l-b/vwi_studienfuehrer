<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];
$name = $_POST['name'];

$sql1 = "
	UPDATE `institutes` SET `active`= 0
	WHERE `name` = '$name'
";

$sql2 = "DELETE FROM `DELETED_INSTITUTES` WHERE `id` = $id";

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