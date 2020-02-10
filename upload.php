<?php

$validNames = array(
	"ADDED_LECTURERS.txt", "CHANGED_LECTURERS.txt", "DELETED_LECTURERS.txt",
	"ADDED_MODULES.txt", "CHANGED_MODULES.txt", "DELETED_MODULES.txt",
	"ADDED_SUBJECTS.txt", "CHANGED_SUBJECTS.txt", "DELETED_SUBJECTS.txt",
	"ADDED_INSTITUTES.txt", "CHANGED_INSTITUTES.txt", "DELETED_INSTITUTES.txt",
	"LECTURERS_INSTITUTES.txt", "MODULES_LEVELS.txt", "SUBJECTS_LECTURERS.txt", "SUBJECTS_MODULES.txt"
);

if(in_array($_FILES['file']['name'], $validNames)){
	if (move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name'])) {
		echo "Die Datei '".$_FILES['file']['name']."' wurde erfolgreich hochgeladen.";
	} else {
		echo "Beim Hochladen ist ein Problem aufgetreten.";
	}
}else{
	echo "Der Dateiname ist nicht zulässig.";
}
?>