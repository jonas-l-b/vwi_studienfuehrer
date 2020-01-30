<?php
include "sessionsStart.php";
if($userRow['admin']==0){
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_admin';</SCRIPT>");
}else{
	
require("connect.php");

if (isset($_GET['table_name'])){
	$table_name = $_GET['table_name'];
}
else{
	echo "<p>No table name in url.</p>";
}

$query = "SELECT * FROM $table_name";
if (!$result = mysqli_query($con, $query)) {
	exit(mysqli_error($con));
}

$data = array();
if (mysqli_num_rows($result) > 0) {
	while ($row = mysqli_fetch_assoc($result)) {
		$data[] = $row;
	}
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$table_name.'.csv');
$output = fopen('php://output', 'w');

$sql = "SHOW COLUMNS FROM $table_name";
$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result)){
	$columns[] = $row['Field'];
}

fputcsv($output, $columns);

if (count($data) > 0) {
	foreach ($data as $row) {
		fputcsv($output, $row);
	}
}

}
?>