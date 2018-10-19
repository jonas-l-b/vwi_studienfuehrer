<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$assign_to_id = $_POST['assign_to_id'];
$message_id = substr($_POST['message_id'],11);

$sql = "
	UPDATE messages
	SET assigned_to_id = $assign_to_id, assigned_to_time_stamp = now()
	WHERE message_id = '".$message_id."'
";

//Get assigned name
$sql2 = "
	SELECT *
	FROM users
	WHERE user_ID = '".$assign_to_id."'
";
$result = mysqli_query($con, $sql2);
$assigned_to = mysqli_fetch_assoc($result);

if(mysqli_query($con, $sql)){
	echo $assigned_to['username'];
	
	if($assign_to_id != $userRow['user_ID']){ //Nur E-Mail wenn nicht sich selbst zugewiesen
		$subject = "[Studienführer: Benachrichtigung] Dir wurde eine Nachricht zugewiesen";
		
		$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM messages WHERE message_id = '".$message_id."'"));
		switch($row['message_type']){
			case "bug":
				$type = "Bug";
				break;
			case "mistake":
				$type = "Fehler";
				break;
			case "question":
				$type = "Frage";
				break;
			case "feedback":
				$type = "Feedback";
				break;
		}
	
		$body = "nosig
			<span>ein Administrator hat dir eine Nachricht zugewiesen:</span>
			<hr>
			<p>Betreff: <strong>".$type."</strong></p>
			<span>".$row['comment']."</span>
			<hr>
			<span class='foo'><a href=\"studienführer.vwi-karlsruhe.de/admin.php#messages\">Hier</a> kannst du die Nachricht online anschauen.</span>
		";
		
		EmailService::getService()->sendEmail($assigned_to['email'], $assigned_to['username'], $subject, $body);
	}
}

?>