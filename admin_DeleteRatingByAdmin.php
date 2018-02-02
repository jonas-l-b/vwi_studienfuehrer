<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php

$id = $_POST['user_id'];

$sql = "DELETE FROM ratings WHERE ID = ".$id;

if(mysqli_query($con,$sql)){
	echo "Das Löschen war erfolgreich!";
}

?>