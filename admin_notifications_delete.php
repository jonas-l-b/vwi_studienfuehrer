<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$admin_id = strip_tags($_POST['admin_id']);

$sql = "
	DELETE FROM admin_notifications
	WHERE admin_id = $admin_id
";

if(mysqli_query($con,$sql)){
	echo "erfolg";
}
?>