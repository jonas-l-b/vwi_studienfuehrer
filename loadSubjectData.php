<?php 

$result = mysqli_query($con,"SELECT * FROM subjects WHERE code = '$subject'");
$num = mysqli_num_rows($result);

// Check, ob Datensatz existiert (ist der Fall, wenn mindestens ein Ergebnis zurückgegeben wird)
if ($num >= 1 ) {
	$subjectData = mysqli_fetch_assoc($result);
} else {
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_subject_in_db';</SCRIPT>");
}

?>