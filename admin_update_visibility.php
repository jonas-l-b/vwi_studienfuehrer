<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$name = $_POST['name'];
$value = $_POST['value'];

$sql = "UPDATE `admin_update_settings` SET `value`='$value' WHERE name = '$name'";

mysqli_query($con, $sql);
?>