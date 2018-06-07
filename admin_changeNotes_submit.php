<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$noteLeftInput = trim($_POST['noteLeftInput']);
$noteMiddleInput = trim($_POST['noteMiddleInput']);
$noteRightInput = trim($_POST['noteRightInput']);

$a = mysqli_query($con, "UPDATE notes SET content = '$noteLeftInput' WHERE name = 'noteLeft';");
$b = mysqli_query($con, "UPDATE notes SET content = '$noteMiddleInput' WHERE name = 'noteMiddle';");
$c = mysqli_query($con, "UPDATE notes SET content = '$noteRightInput' WHERE name = 'noteRight';");

if($a && $b && $c){
	echo "Die Änderungen wurden erfolgreich gespeichert!";
}


?>