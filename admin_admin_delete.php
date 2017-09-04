<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$user_id = strip_tags($_POST['user_id']);

$sql = "
	UPDATE users
	SET admin = 0
	WHERE user_ID = $user_id
";

if(mysqli_query($con,$sql)){
	echo "erfolg";
}

?>