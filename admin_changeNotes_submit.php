<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$noteLeftInput = trim($_POST['noteLeftInput']);
$noteMiddleInput = trim($_POST['noteMiddleInput']);
$noteRightInput = trim($_POST['noteRightInput']);

$noteLeftColor = trim($_POST['noteLeftColor']);
$noteMiddleColor = trim($_POST['noteMiddleColor']);
$noteRightColor = trim($_POST['noteRightColor']);

$a = mysqli_query($con, "UPDATE notes SET content = '$noteLeftInput' WHERE name = 'noteLeft';");
$b = mysqli_query($con, "UPDATE notes SET content = '$noteMiddleInput' WHERE name = 'noteMiddle';");
$c = mysqli_query($con, "UPDATE notes SET content = '$noteRightInput' WHERE name = 'noteRight';");

$d = mysqli_query($con, "UPDATE notes SET color = '$noteLeftColor' WHERE name = 'noteLeft';");
$e = mysqli_query($con, "UPDATE notes SET color = '$noteMiddleColor' WHERE name = 'noteMiddle';");
$f = mysqli_query($con, "UPDATE notes SET color = '$noteRightColor' WHERE name = 'noteRight';");

if($a && $b && $c && $d && $e && $f){
	echo "Die Änderungen wurden erfolgreich gespeichert!";
}


?>