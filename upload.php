<?php

if (move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name'])) {
	echo "Die Datei '".$_FILES['file']['name']."' wurde erfolgreich hochgeladen.";
} else {
	echo "Beim Hochladen ist ein Problem aufgetreten.";
}

?>