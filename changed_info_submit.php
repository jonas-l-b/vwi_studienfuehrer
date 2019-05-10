<?php
include "sessionsStart.php";
include "connect.php";
include "processInput.php";
?>

<?php

$k_id = process_input($_POST['k_id']);
$category = process_input($_POST['category']);
$title = process_input($_POST['title']);
$content = process_input($_POST['content']);

$user_id = $userRow['user_ID'];

$sql = "
    SELECT * FROM info_content
    WHERE id = $k_id AND user_id = $user_id
";
$result = mysqli_query($con, $sql);
if(mysqli_num_rows($result) != 1){
    echo "error";
}else{
    $sql = "
        UPDATE `info_content`
        SET `category`='$category',`title`='$title',`content`='$content',`last_changed_time_stamp`=now()
        WHERE id = $k_id
    ";

    if(mysqli_query($con, $sql)){
        echo "erfolg";
    }
}
?>