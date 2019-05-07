<?php
include "sessionsStart.php";
include "connect.php";
include "processInput.php";
?>

<?php

$category = process_input($_POST['category']);
$title = process_input($_POST['title']);
$content = process_input($_POST['content']);

$user_id = $userRow['user_ID'];

$sql = "
    INSERT INTO `info_content`(`category`, `title`, `content`, `user_id`, `time_stamp`)
    VALUES ('$category', '$title', '$content', '$user_id', now())
";

if(mysqli_query($con, $sql)){
	echo "erfolg";
}
?>