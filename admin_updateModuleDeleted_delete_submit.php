<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];

$sql = "DELETE FROM `DELETED_MODULES` WHERE `id` = $id";

if(mysqli_query($con, $sql)){
	echo "Löschen erfolgreich.";
}else{
	echo "Löschen nicht erfolgreich.";
}
?>