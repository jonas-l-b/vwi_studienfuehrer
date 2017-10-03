<?php

include "connect.php";

?>

<?php

$series = $_POST['series'];
$token = $_POST['token'];
$user_id = $_POST['user_id'];

$sql = "
	INSERT INTO remember_me(user_id, series, token)
	VALUES('$user_id', '$series', '$token')
";


if(mysqli_query($con, $sql)){
	echo "erfolg";
}



?>