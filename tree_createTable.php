<?php

include "connect.php";

?>


<?php

//This creates the table containing the requested subjects after search
//Creation in 3 steps:
//1. Create query to get all subjects
//2. Get rating averages for each subject using this query
//3. Put all in one array
//4. Sort array

/*1. Create query to get all subjects*/
$modulType = $_POST['modulType'];
$module = $_POST['module'];
$lecturer = $_POST['lecturer'];
$institute = $_POST['institute'];
$level = $_POST['level'];
$semester = $_POST['semester'];
$language = $_POST['language'];

$sortArea = $_POST['sortArea'];

if(isset($_POST['sortOverall'])) $sortOverall = $_POST['sortOverall'];
if(isset($_POST['sortLecture'])) $sortLecture = $_POST['sortLecture'];
if(isset($_POST['sortExamType'])) $sortExamType = $_POST['sortExamType'];
if(isset($_POST['sortExamItem'])) $sortExamItem = $_POST['sortExamItem'];
if(isset($_POST['sortExamOther'])) $sortExamOther = $_POST['sortExamOther'];

$orderDirection = $_POST['orderDirection'];

				
/*Daten gemäß Auswahl abfragen*/
$query = "";

//modultypes
foreach($modulType as $check) {
	if($check!="") $query .= "modules.type = '".$check."' OR ";
}
$query = substr($query, 0, -4); //Überflüssiges OR abschneiden
$query = "(".$query.")";

//Rest
if($module!="none") $query .= " AND modules.module_ID = '".$module."'";
if($lecturer!="none") $query .= " AND lecturers.lecturer_ID = '".$lecturer."'";
if($institute!="none") $query .= " AND institutes.institute_ID = '".$institute."'";
if($level!="none") $query .= " AND levels.name = '".$level."'";
if($semester!="none") $query .= " AND subjects.semester = '".$semester."'";
if($language!="none") $query .= " AND subjects.language = '".$language."'";

/*Alle Veranstaltungen gemäßg Abfrage aus Dankenbank abfragen*/
$sqlBody = "
	FROM subjects
	JOIN subjects_lecturers ON subjects.ID = subjects_lecturers.subject_ID
	JOIN lecturers ON subjects_lecturers.lecturer_ID = lecturers.lecturer_ID
	JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
	JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
	JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
	JOIN modules ON subjects_modules.module_ID = modules.module_ID
	JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
	JOIN levels ON modules_levels.level_ID = levels.level_ID
";
$sql1 = "
	SELECT DISTINCT subjects.ID as ID, subject_name, subjects.code AS subject_code, subjects.ECTS AS subject_ECTS, semester, language
	".$sqlBody."
	WHERE ".$query."
";
//echo $query;
//echo "<br><br>";
//echo $sql1;

/*2. Get rating averages for each subject using this query*/
$allSubjects = mysqli_query($con,$sql1);
	
while($subjects = mysqli_fetch_assoc($allSubjects)){

	$result = mysqli_query($con,"SELECT * FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
	if (mysqli_num_rows($result) == 0){ //Falls noch keine Bewertungen vorhanden
		$overallRating[$subjects['ID']] = "-";
		$recoms[$subjects['ID']] = "-";
		$overallLecture[$subjects['ID']] = "-";
		$relevance[$subjects['ID']] = "-";
		$interest[$subjects['ID']] = "-";
		$quality[$subjects['ID']] = "-";
		$overallExam[$subjects['ID']] = "-";
		$effort[$subjects['ID']] = "-";
		$fairness[$subjects['ID']] = "-";
		$timePressure[$subjects['ID']] = "-";
		$reproductionTransfer[$subjects['ID']] = 0;
		$qualitativeQuantitative[$subjects['ID']] = 0;
		$amountRatings[$subjects['ID']] = "-";
	}
	else{ //Falls Bewertungen vorhanden
		$db = array("general0", "lecture0", "lecture1", "lecture2", "lecture3", "exam0", "exam1", "exam2", "exam3", "exam4", "exam5");
		$name = array("overallRating", "overallLecture", "relevance", "interest", "quality", "overallExam", "effort", "fairness", "timePressure", "reproductionTransfer", "qualitativeQuantitative");
		
		for ($i = 0; $i < 11; $i++) {
			$row = mysqli_fetch_array(mysqli_query($con, "SELECT AVG(".$db[$i].") FROM ratings WHERE subject_ID = '".$subjects['ID']."'"));
			${$name[$i]}[$subjects['ID']] = $row[0];
		}
		
		$row = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(recommendation) FROM ratings WHERE subject_ID = '".$subjects['ID']."' AND recommendation = 1"));
		$recoms[$subjects['ID']] = $row[0];
		
		$row = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(examType) FROM ratings WHERE subject_ID = '".$subjects['ID']."' AND examType = 'other'"));
		$amountRatings[$subjects['ID']] = $row[0];
	}
	
	/*3. Put all in one array*/
	//module_types
	$sql = "
		SELECT DISTINCT type
		".$sqlBody."
		WHERE subjects.ID = ".$subjects['ID']."
		ORDER BY type
	";
	$result = mysqli_query($con,$sql);
	$module_types = "";
	while($row = mysqli_fetch_assoc($result)){
		$module_types .= $row['type']."<br>";
	}
	$module_types = substr($module_types, 0, -4);

	//part_of_modules
	$sql = "
		SELECT DISTINCT modules.name, modules.module_id 
		".$sqlBody."
		WHERE subjects.ID = ".$subjects['ID']."
		ORDER BY modules.name
	";
	$result = mysqli_query($con,$sql);
	$part_of_modules = "";
	while($row = mysqli_fetch_assoc($result)){
		$part_of_modules .= "<a href=\"module.php?module_id=".$row['module_id']."\">".$row['name']."</a><br>";
	}
	$part_of_modules = substr($part_of_modules, 0, -4);

	//levels
	$sql = "
		SELECT DISTINCT levels.name
		".$sqlBody."
		WHERE subjects.ID = ".$subjects['ID']."
		ORDER BY CASE
			when levels.name = 'bachelor_basic' then 1
			when levels.name = 'bachelor' then 2
			when levels.name = 'master' then 3
		END
	";
	$result = mysqli_query($con,$sql);
	$levels = "";
	while($row = mysqli_fetch_assoc($result)){
		switch($row['name']){
			case "bachelor_basic":
				$levels .= "Kernprog."."<br>";
				break;
			case "bachelor":
				$levels .= "Vertiefung"."<br>";
				break;
			case "master":
				$levels .= "Master"."<br>";
				break;
		}
	}
	$levels = substr($levels, 0, -4);

	//lecturers
	$sql = "
		SELECT DISTINCT lecturers.lecturer_ID, lecturers.last_name, lecturers.first_name, institutes.institute_ID, abbr
		".$sqlBody."
		WHERE subjects.ID = ".$subjects['ID']."
		ORDER BY abbr, lecturers.last_name
	";
	$result = mysqli_query($con,$sql);
	$lecturers = "";
	while($row = mysqli_fetch_assoc($result)){
		$lecturers .= "<a href=\"lecturer.php?lecturer_id=".$row['lecturer_ID']."\">".substr($row['first_name'],0,1).". ".$row['last_name']."</a> (<a href=\"institute.php?institute_id=".$row['institute_ID']."\">".$row['abbr']."</a>)<br>";
	}
	$lecturers = substr($lecturers, 0, -4);

	$data[] = array(
		'subject_name' => $subjects['subject_name'],
		'subject_code' => $subjects['subject_code'],
		'modul_types' => $module_types,
		'part_of_modules' => $part_of_modules,
		'levels' => $levels,
		'ECTS' => $subjects['subject_ECTS'],
		'lecturers' => $lecturers,
		'semester' => $subjects['semester'],
		'language' => $subjects['language'],
		'ID' => $subjects['ID'],
		
		'overallRating' => $overallRating[$subjects['ID']],
		'recoms' => $recoms[$subjects['ID']],
		'overallLecture' => $overallLecture[$subjects['ID']],
		'relevance' => $relevance[$subjects['ID']],
		'interest' => $interest[$subjects['ID']],
		'quality' => $quality[$subjects['ID']],
		'overallExam' => $overallExam[$subjects['ID']],
		'effort' => $effort[$subjects['ID']],
		'fairness' => $fairness[$subjects['ID']],
		'timePressure' => $timePressure[$subjects['ID']],
		'reproductionTransfer' => $reproductionTransfer[$subjects['ID']],
		'qualitativeQuantitative' => $qualitativeQuantitative[$subjects['ID']],
		'amountRatings' => $amountRatings[$subjects['ID']],
	);
}

/*4. Sort array*/
if(mysqli_num_rows($allSubjects)!=0){ //Nur ausführen, wenn ganz am Anfang Fächer zurückgegeben wurden; führt sonst zu Fehlern
	//For "/10" and note
	$displayFromMax = "";
	$displayNote = "display:none";
	
	//Get by what it is sorted
	if($sortArea == "overall"){
		switch ($sortOverall){
			case "overallRating":
				$orderBy = "overallRating";
				$orderByHeader = "Gesamtbewertung";
				break;
			case "recoms":
				$orderBy = "recoms";
				$orderByHeader = "Anzahl Empfehlungen";
				break;
		}
	}elseif($sortArea == "lecture"){
		switch ($sortLecture){
			case "overallLecture":
				$orderBy = "overallLecture";
				$orderByHeader = "Overall Vorlesung";
				break;
			case "relevance":
				$orderBy = "relevance";
				$orderByHeader = "Relevanz";
				break;
			case "interest":
				$orderBy = "interest";
				$orderByHeader = "Interessantheit";
				break;
			case "quality":
				$orderBy = "quality";
				$orderByHeader = "Qualität Materialien";
				break;
		}
	}elseif($sortArea == "exam"){
		if($sortExamType == "written" OR $sortExamType == "oral"){
		switch ($sortExamItem){
			case "overallExam":
				$orderBy = "overallExam";
				$orderByHeader = "Overall Prüfung";
				break;
			case "effort":
				$orderBy = "effort";
				$orderByHeader = "Aufwand";
				break;
			case "fairness":
				$orderBy = "fairness";
				$orderByHeader = "Fairness";
				break;
			case "timePressure":
				$orderBy = "timePressure";
				$orderByHeader = "Zeitdruck";
				break;
			case "reproductionTransfer":
				$orderBy = "reproductionTransfer";
				$orderByHeader = "Reprod./Transfer";
				$displayFromMax = "display:none";
				$displayNote = "";
				break;
			case "qualitativeQuantitative":
				$orderBy = "qualitativeQuantitative";
				$orderByHeader = "Qualit./Quantit.";
				$displayFromMax = "display:none";
				$displayNote = "";
				break;
		}	
		}else{
			$orderBy = "amountRatings";
			$orderByHeader = "#Anmerkungen Prüfung";
			$displayFromMax = "display:none";
		}
	}

	$bool = true;
	if($orderDirection=="ASC") $bool = false;
	
	//Array sortieren
	sksort($data, "$orderBy", $bool);

	//Ausgabe vorbereiten
	$content = "";
	foreach($data as $item){
		$content .= "
			<tr>
				<td><div><a href=\"index.php?subject=".$item['subject_code']."\" target=\"blank\">".$item['subject_name']."</a></div></td>
				<td><div>".$item['modul_types']."</div></td>
				<td><div>".$item['part_of_modules']."</div></td>
				<td><div>".$item['levels']."</div></td>
				<td><div>".$item['ECTS']."</div></td>
				<td><div><p style=\"white-space: nowrap;\">".$item['lecturers']."<p></div></td>
				<td><div>".$item['semester']."</div></td>
				<td><div>".$item['language']."</div></td>
				<td><div>".round($item[$orderBy],1)."
					<span style=\"$displayFromMax\"> /10</span>
				</div></td>
			</tr>
		";
	}
	
	$table="
		
		<span style=\"$displayNote\"><br><i>Hinweis:</i> Der Maximalwert für Reproduktion und Qualitativ ist -10; der Maximalwert für Transfer und Quantitativ ist 10.</span>
		<table class=\"table table-striped table-condensed searchresulttable\">
			<thead>
				<tr>
					<th>Veranstaltung</th>
					<th>Typ</th>
					<th>Beinhaltet in</th>
					<th>Level</th>
					<th>ECTS</th>
					<th>Dozent</th>
					<th>Semester</th>
					<th>Sprache</th>
					<th class=\"nowrap\">".$orderByHeader."</th>
				</tr>
			</thead>
			<tbody>
				".$content."
			</tbody>
		</table>
	";
} else{
	$table = "<h4>Für die gewählten Einschränkungen befinden sich keine Veranstaltungen in unserer Datenbank.</h4>";
}

//Funktion für Array-Sortierung
function sksort(&$array, $subkey="id", $sort_ascending=false) {
	if (count($array))
		$temp_array[key($array)] = array_shift($array);

	foreach($array as $key => $val){
		$offset = 0;
		$found = false;
		foreach($temp_array as $tmp_key => $tmp_val)
		{
			if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
			{
				$temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
											array($key => $val),
											array_slice($temp_array,$offset)
										  );
				$found = true;
			}
			$offset++;
		}
		if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
	}

	if ($sort_ascending) $array = array_reverse($temp_array);

	else $array = $temp_array;
}

echo $table;
?>