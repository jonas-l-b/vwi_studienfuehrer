<?php

include "header.php";

include "sessionsStart.php";

include "connect.php";

include "saveSubjectToVariable.php";

include "loadSubjectData.php";

?>

<?php
$nameID = $userRow['user_ID'];

$sql="
	DELETE FROM ratings
	WHERE subject_ID = '$subjectData[ID]' AND user_ID = '$nameID';
";

if ($con->query($sql) == TRUE) {
	//echo 'erfolgreich';
}
	
echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='index.php?subject=$subject';</SCRIPT>");



?>