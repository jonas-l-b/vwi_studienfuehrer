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

if (isset($_GET['query'])){
	$lectures = mysqli_query($con,"SELECT subject_name,code FROM subjects WHERE subject_name LIKE '%".$_GET['query']."%'");
	while($row = mysqli_fetch_assoc($lectures)){
		if($count < 3){
			$result .= '{ "value" :"'.$row['subject_name'].'", "data": {"category" : "Veranstaltung", "dest":"index.php?subject='.$row['code'].'" } },';
			$anything = true;
			$count++;
		}else{
			break;
		}		
	}
	$counttotal = $count;
	$count = 0;
	
	$modules = mysqli_query($con,"SELECT name, module_ID FROM modules WHERE name LIKE '%".$_GET['query']."%'");
	while($row = mysqli_fetch_assoc($modules)){
		if($count < 3){
			$result .= '{ "value" :"'.$row['name'].'", "data": {"category" : "Module", "dest":"module.php?module_id='.$row['module_ID'].'"} },';
			$anything = true;
			$count++;
		}else{
			break;
		}	
	}
	$counttotal += $count;
	$count = 0;
	
	$institutes = mysqli_query($con,"SELECT name, abbr, institute_ID FROM institutes WHERE abbr LIKE '%".$_GET['query']."%' OR name LIKE '%".$_GET['query']."%'");
	while(($row = mysqli_fetch_assoc($institutes)) && $count<2){
		$result .= '{ "value" :"('.$row['abbr'].') '.$row['name'].'", "data": {"category" : "Institute", "dest":"institute.php?institute_id='.$row['institute_ID'].'" } },';
		$anything = true;
		$count++;
	}
	$counttotal += $count;
	$count = 0;
	
	$lecturers = mysqli_query($con,"SELECT first_name, last_name, lecturer_ID FROM lecturers WHERE CONCAT(first_name,' ',last_name) LIKE '%".$_GET['query']."%'");
	while(($row = mysqli_fetch_assoc($lecturers) )&& $count<2 && $counttotal<8){
		$result .= '{ "value" :"'.$row['last_name'].', '.$row['first_name'].'", "data": {"category" : "Dozenten", "dest":"lecturer.php?lecturer_id='.$row['lecturer_ID'].'" } },';
		$anything = true;
		$counttotal++;
		$count++;
	}
	$count = 0;
	while(($row = mysqli_fetch_assoc($lectures)) && $counttotal<8 && $count<2){
		$result .= '{ "value" :"'.$row['subject_name'].'", "data": {"category" : "Veranstaltung", "dest":"index.php?subject='.$row['code'].'" } },';
		$counttotal++;
		$count++;
	}
	$count = 0;
	while(($row = mysqli_fetch_assoc($modules)) && $count<1 && $counttotal<8){
		$result .= '{ "value" :"'.$row['name'].'", "data": {"category" : "Module", "dest":"module.php?module_id='.$row['module_ID'].'"} },';
		$count++;
		$counttotal++;
	}
	$count = 0;
	while(($row = mysqli_fetch_assoc($institutes)) && $count<2 && $counttotal<8){
		$result .= '{ "value" :"('.$row['abbr'].') '.$row['name'].'", "data": {"category" : "Institute", "dest":"institute.php?institute_id='.$row['institute_ID'].'" } },';
		$count++;
		$counttotal++;
	}
	if($anything){
		$result = rtrim($result, ',');
	}else{
		$result .= '{ "value" :"Keine Ergebnisse", "data": {"dest":""} }';
	}	
}else{
	echo '{
    "query": "Unit",
    "suggestions": []
	}'; 
}

$result .= "]}";
echo $result;
?>
