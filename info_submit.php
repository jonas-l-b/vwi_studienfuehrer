<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php

$category = $_POST['category'];
$title = $_POST['title'];
$content = $_POST['content'];

$user_id = $userRow['user_ID'];

$sql = "
    INSERT INTO `info_content`(`category`, `title`, `content`, `user_id`, `time_stamp`)
    VALUES ('$category', '$title', '$content', '$user_id', now())
";

if(mysqli_query($con, $sql)){
	echo "erfolg";
}
?>