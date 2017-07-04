<?php

include 'connect.php';

?>

<?php

if (isset($_GET['commentID'])){
	$commentID = strval ($_GET['commentID']);
}

if (isset($_GET['userID'])){
	$userID = strval ($_GET['userID']);
}

$result = mysqli_query($con,"SELECT * FROM commentratings WHERE comment_ID = '$commentID' AND user_ID = '$userID'");
$num = mysqli_num_rows($result);

if ($num >= 1 ) { // Check, ob Datensatz existiert
	$exists = true;
} else{
	$exists = false;
}

echo $exists;

?>