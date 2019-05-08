<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php
if($userRow['super_admin'] == 1){

	$sql = "SELECT first_name, email FROM `users` WHERE info = 'yes'";
	//$sql = "SELECT first_name, email FROM `users` WHERE user_ID IN (2,3,49,46)";
	$result = mysqli_query($con, $sql);

	$subject = "[Studienführer] Infoabend VWI-ESTIEM Hochschulgruppe";
	$body = "
		<p>wir als VWI-ESTIEM Hochschulgruppe möchten ein bisschen Werbung in eigener Sache machen.</p>
		<p>Eines unserer Projekte kennst du bereits - wir sind diejenigen, die hinter dem Studienführer stehen. Wenn du davon genauso begeistert bist wie wir, hast du bei uns die Chance, selbst daran mitwirken. Daneben kannst du in unserer Hochschulgruppe allerdings auch viele andere Dinge machen. Vielleicht hast du auch so eine gute Idee wie den Studienführer, konntest sie aber bisher noch nicht umsetzen? Oder du hast Lust, Veranstaltungen mit Firmen zu organisieren und dabei ganz leicht wertvolle Kontakte zu knüpfen? Bist du gerne unterwegs? Egal ob du Deutschland entdecken möchtest oder neue Orte in Europa kennenlernen willst, wir verbinden zwei große Netzwerke, die dir beides bieten. Nebenbei kannst du dabei Wirtschaftsingenieure aus ganz Deutschland und Europa kennenlernen.</p>
		<p>Wenn dich die ein oder andere Sache davon interessiert, dann komm zu unserem Infoabend! Er findet <strong>heute Abend, den 07.05. um 19:30 Uhr im Allianzgebäude, Raum 1C-02</strong> statt. Für deine Verpflegung haben wir ausreichend gesorgt.</p>
		<p>Wir freuen uns, dich zu sehen!</p>
	";
	
	echo "Number of emails in mailing list: " . mysqli_num_rows($result) . "<br><br>";
	while($row = mysqli_fetch_assoc($result)){
		/*
		if ((EmailService::getService()->sendEmail($row["email"], $row["first_name"], $subject, $body)) == 1 ){
			echo "Sent to: " . $row['email'] . "<br>";
		}
		*/
		echo "Uncomment EmailService in script!";
	}

}else{
	echo "Access denied.";
}
?>