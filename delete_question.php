<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php

$q_id = $_POST['q_id'];
$user_id = $userRow['user_ID'];

$sql = "
    SELECT * FROM questions
    WHERE ID = $q_id AND user_ID = $user_id
";
$result = mysqli_query($con, $sql);
if(mysqli_num_rows($result) != 1){
    echo "error";
}else{
    $sql ="DELETE FROM `questions` WHERE ID = $q_id AND user_ID = $user_id";
    
    if(mysqli_query($con, $sql)){
        echo "erfolg";
    }
}

?>