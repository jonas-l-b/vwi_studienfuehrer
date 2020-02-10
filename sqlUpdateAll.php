<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$type = $_POST['type'];
$files = glob("uploads/*");

if($type == "entities"){
	$validNames = array(
		"ADDED_SUBJECTS.txt", "CHANGED_SUBJECTS.txt", "DELETED_SUBJECTS.txt",
		"ADDED_MODULES.txt", "CHANGED_MODULES.txt", "DELETED_MODULES.txt",
		"ADDED_LECTURERS.txt", "CHANGED_LECTURERS.txt", "DELETED_LECTURERS.txt",
		"ADDED_INSTITUTES.txt", "CHANGED_INSTITUTES.txt", "DELETED_INSTITUTES.txt",
	);
	$validNames = array(
		"ADDED_LECTURERS.txt",
		"ADDED_INSTITUTES.txt"
	);
}elseif($type == "matchings"){
	$validNames = array(
		"LECTURERS_INSTITUTES.txt", "MODULES_LEVELS.txt", "SUBJECTS_LECTURES.txt", "SUBJECTS_MODULES.txt"
	);
}else{
	echo "Es ist ein Fehler aufgetreten.";
}

if(isset($validNames)){
	foreach ($files as $file) {
		if(in_array(basename($file), $validNames)){
			$myfile = fopen($file, "r") or die("Unable to open file!");

			$sql = fread($myfile,filesize($file));
			//$sql = utf8_encode($sql); //Encode äöü, das ist sau wichtig!
			echo $sql."\n\n";
			if(mysqli_multi_query($con, $sql)){
				echo "SQL-Befehl aus ".basename($file)." erfolgreich ausgeführt.\n";
			}else{
				echo "Beim Ausführen des SQL-Befehls aus ".basename($file)." ist ein Fehler aufgetreten.\n";
			}
			fclose($myfile);
		}
	}
}

?>