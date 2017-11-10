<?php
// Subject aus URL speichern
if (isset($_GET['subject'])){
	$subject = strval ($_GET['subject']);
}
else{
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='tree.php';</SCRIPT>");
}
?>
