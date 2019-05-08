<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

//$sql = "SELECT first_name, email FROM `users` WHERE info = 'yes'";
$sql = "SELECT first_name, email FROM `users` WHERE user_ID IN (2,3,49)";
$result = mysqli_query($con, $sql);

while($row = mysqli_fetch_assoc($result)){
	echo $row['first_name'] . "<br>";
}
/*
$mails = array(
	"julian.germek@estiem.org",
	"jonas.bakker@estiem.org",
	"felix.bock@estiem.org"

);

$subject = "[Studienführer] Nur ein zweiter Test";


$body = "
	<p>Wir hatten beim Studienführer das Problem, dass beim Senden von E-Mails an eine Liste von Empfängern jeder mehrere Mails empfangen hat - unter anderem welche, die nicht für ihn bestimmt waren.</p>
	<p>Das Problem sollte behoben sein. Schreib doch bitte trotzdem eine kurze Nachricht mit der Anzahl an Mails, die du empfangen hast, an
	<a href='mailto:julian.germek@estiem.org?subject=Rückmeldung%20Studienführermailtest&amp;body=Hallo%20Julian,%0D%0A%0D%0Aso%20viele%20Mails%20habe%20ich%20empfangen:%20%0D%0A%0D%0AViele%20Grüße!'>julian.germek@estiem.org</a>
	.</p>
	<p>Danke</p>
";

$body = "Das ist Test #2!";

foreach ($mails as &$mail) {
    EmailService::getService()->sendEmail($mail, 'du', $subject, $body);
}
*/
?>