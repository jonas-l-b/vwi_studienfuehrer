<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$message_id = substr($_POST['message_id'],11);

//Get assigned_to's name
$sql = "
	SELECT *
	FROM messages
	WHERE message_id = '".$message_id."'
";
$result= mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
if($row['assigned_to_id']=="0"){
	$glyphicon2Line = "question-sign";
} else{
	$glyphicon2Line = "user";
}

echo "$glyphicon2Line";

?>