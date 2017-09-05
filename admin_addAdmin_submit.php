<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$user_id = strip_tags($_POST['user_id']);

$sql = "
	UPDATE users
	SET admin = 1
	WHERE user_ID = $user_id
";

if(mysqli_query($con,$sql)){
	$db_logger->info("Nutzer ".$userRow['username']." hat gerade den Nutzer mit user_ID ".$user_id." Admin-Rechte zugeschrieben.");
}else{
	echo "Beim Eintragen ist ein Problem aufgetreten.";
}

?>