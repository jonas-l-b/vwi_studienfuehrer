<?php

/*
*		Dieses Skript dient als API für das autocomplete-Feature unserer Suchfunktion. 
*
*		
*
*/
header('Content-type: application/json');
include "connect.php";

$anything = false;
$count = 0;
$counttotal = 0;
$result =  '{
    "query": "Unit",
    "suggestions": [';
$lecture_result ="";
$lecturer_result ="";
$module_result ="";
$institute_result ="";

if (isset($_GET['query'])){
	$query = '%'.$_GET['query'].'%';
	
	$statement1 = $con->prepare("SELECT subject_name,code FROM subjects WHERE subject_name LIKE ? ORDER BY subject_name LIMIT 5");
	$statement1->bind_param('s', $query);
	$statement1->execute();
	$lectures = $statement1->get_result();
	while($row = mysqli_fetch_assoc($lectures)){
		if($count < 3){
			$lecture_result .= '{ "value" :"'.$row['subject_name'].'", "data": {"category" : "Veranstaltung", "dest":"index.php?subject='.$row['code'].'" } },';
			$anything = true;
			$count++;
		}else{
			break;
		}		
	}
	$counttotal = $count;
	$count = 0;
	
	$statement2 = $con->prepare("SELECT name, module_ID FROM modules WHERE name LIKE ? ORDER BY name LIMIT 5");
	$statement2->bind_param('s', $query);
	$statement2->execute();
	$modules = $statement2->get_result();
	while($row = mysqli_fetch_assoc($modules)){
		if($count < 3){
			$module_result .= '{ "value" :"'.$row['name'].'", "data": {"category" : "Module", "dest":"module.php?module_id='.$row['module_ID'].'"} },';
			$anything = true;
			$count++;
		}else{
			break;
		}	
	}
	$counttotal += $count;
	$count = 0;
	
	$statement3 = $con->prepare("SELECT name, abbr, institute_ID FROM institutes WHERE abbr LIKE ? OR name LIKE ? ORDER BY abbr LIMIT 5");
	$statement3->bind_param('ss', $query, $query);
	$statement3->execute();
	$institutes = $statement3->get_result();
	while(($row = mysqli_fetch_assoc($institutes)) && $count<2){
		$institute_result .= '{ "value" :"('.$row['abbr'].') '.$row['name'].'", "data": {"category" : "Institute", "dest":"institute.php?institute_id='.$row['institute_ID'].'" } },';
		$anything = true;
		$count++;
	}
	$counttotal += $count;
	$count = 0;
	
	$statement4 = $con->prepare("SELECT first_name, last_name, lecturer_ID FROM lecturers WHERE CONCAT(first_name,' ',last_name) LIKE ? ORDER BY last_name LIMIT 4");
	$statement4->bind_param('s', $query);
	$statement4->execute();
	$lecturers = $statement4->get_result();
	while(($row = mysqli_fetch_assoc($lecturers) )&& $count<2 && $counttotal<8){
		$lecturer_result .= '{ "value" :"'.$row['last_name'].', '.$row['first_name'].'", "data": {"category" : "Dozenten", "dest":"lecturer.php?lecturer_id='.$row['lecturer_ID'].'" } },';
		$anything = true;
		$counttotal++;
		$count++;
	}
	$count = 0;
	while(($row = mysqli_fetch_assoc($lectures)) && $counttotal<8 && $count<2){
		$lecture_result .= '{ "value" :"'.$row['subject_name'].'", "data": {"category" : "Veranstaltung", "dest":"index.php?subject='.$row['code'].'" } },';
		$counttotal++;
		$count++;
	}
	$count = 0;
	while(($row = mysqli_fetch_assoc($modules)) && $count<1 && $counttotal<8){
		$module_result .= '{ "value" :"'.$row['name'].'", "data": {"category" : "Module", "dest":"module.php?module_id='.$row['module_ID'].'"} },';
		$count++;
		$counttotal++;
	}
	$count = 0;
	while(($row = mysqli_fetch_assoc($institutes)) && $count<4 && $counttotal<8){
		$institute_result .= '{ "value" :"('.$row['abbr'].') '.$row['name'].'", "data": {"category" : "Institute", "dest":"institute.php?institute_id='.$row['institute_ID'].'" } },';
		$count++;
		$counttotal++;
	}
	$count = 0;
	while(($row = mysqli_fetch_assoc($lecturers) )&& $count<2 && $counttotal<8){
		$lecturer_result .= '{ "value" :"'.$row['last_name'].', '.$row['first_name'].'", "data": {"category" : "Dozenten", "dest":"lecturer.php?lecturer_id='.$row['lecturer_ID'].'" } },';
		$anything = true;
		$counttotal++;
		$count++;
	}
	$result = $result . $lecture_result . $module_result . $institute_result . $lecturer_result;
	if($anything){
		$result = rtrim($result, ',');
	}else{
		$result .= '{ "value" :"Keine Ergebnisse", "data": {"dest":""} }';
	}	
	
	$result .= "]}";
}else{
	$result .= '{
    "query": "Unit",
    "suggestions": []
	}'; 
}

echo $result;
$statement1->close();
$statement2->close();
$statement3->close();
$statement4->close();
?>
