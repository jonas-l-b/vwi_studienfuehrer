<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$messageID = $_POST['messageID'];

$result = mysqli_query($con, "DELETE FROM messages WHERE message_id = $messageID");

if($result){
	echo "Die Nachricht wurde erfolgreich gelöscht.";
}else{
	echo "Beim Löschen der Nachricht ist ein Fehler aufgetreten.";
}

?>