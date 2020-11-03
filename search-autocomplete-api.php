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
	
	$CachedString = $InstanceCache->getItem('autocompletesearch-'.filter_var($_GET['query'], FILTER_SANITIZE_STRING));
 	if (is_null($CachedString->get())) {
		$statement1 = $con->prepare("SELECT subject_name,ID FROM subjects WHERE subject_name LIKE ? ORDER BY subject_name LIMIT 5");
		$statement1->bind_param('s', $query);
		$statement1->execute();
		$lectures = $statement1->get_result();
		while($row = mysqli_fetch_assoc($lectures)){
			if($count < 3){
				$lecture_result .= '{ "value" :"'.$row['subject_name'].'", "data": {"category" : "Veranstaltung", "dest":"index.php?subject='.$row['ID'].'" } },';
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
		
		$statement4 = $con->prepare("SELECT name, lecturer_ID FROM lecturers WHERE name LIKE ? ORDER BY name LIMIT 4");
		$statement4->bind_param('s', $query);
		$statement4->execute();
		$lecturers = $statement4->get_result();
		while(($row = mysqli_fetch_assoc($lecturers) )&& $count<2 && $counttotal<8){
			$lecturer_result .= '{ "value" :"'.$row['name'].'", "data": {"category" : "Dozenten", "dest":"lecturer.php?lecturer_id='.$row['lecturer_ID'].'" } },';
			$anything = true;
			$counttotal++;
			$count++;
		}
		$count = 0;
		while(($row = mysqli_fetch_assoc($lectures)) && $counttotal<8 && $count<2){
			$lecture_result .= '{ "value" :"'.$row['subject_name'].'", "data": {"category" : "Veranstaltung", "dest":"index.php?subject='.$row['ID'].'" } },';
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
			$lecturer_result .= '{ "value" :"'.$row['name'].'", "data": {"category" : "Dozenten", "dest":"lecturer.php?lecturer_id='.$row['lecturer_ID'].'" } },';
			$anything = true;
			$counttotal++;
			$count++;
		}
		$result = $result . $lecture_result . $module_result . $institute_result . $lecturer_result;
		if($anything){
			$result = rtrim($result, ',');
			$result .= "]}";
			$CachedString->set($result)->expiresAfter(3000);//in seconds, also accepts Datetime
			$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities
		}else{
			if($_GET['query'] == "Übersicht Startseite")
				$result .= '{ "value" :"Wechseln zu Startseite...", "data": {"dest":""} }';
			else
				$result .= '{ "value" :"Keine Ergebnisse", "data": {"dest":""} }';
			$result .= "]}";
		}	
	}else{
		$result = $CachedString->get();
	}
}else{
	$result .= '{
    "query": "Unit",
    "suggestions": []
	}'; 
}

echo $result;
?>
