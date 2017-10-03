<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$message_id = substr($_POST['message_id'],11);

//Get assigned_to's name
$sql = "
	SELECT username
	FROM messages
	JOIN users ON messages.assigned_to_id = users.user_ID
	WHERE message_id = '".$message_id."'
";
$result= mysqli_query($con, $sql);
$assigned_to = mysqli_fetch_assoc($result);
if(mysqli_num_rows($result) == 0){
	$assigned_to_name = "<i>nicht zugewiesen</i>";
} else{
	$assigned_to_name = $assigned_to['username'];
}

echo "$assigned_to_name";

?>