<?php

include "connect.php";


?>


<?php
$user_id = $_GET['user_id'];

//This creates the table containing the requested subjects after search
//Creation in 4 steps:
//1. Create query to get all subjects
//2. Get rating averages for each subject using this query
//3. Put all in one array
//4. Sort array

/*1. Create query to get all subjects*/
$modulType = $_GET['modulType'];
$module = $_GET['module'];
$lecturer = $_GET['lecturer'];
$institute = $_GET['institute'];
$level = $_GET['level'];
$semester = $_GET['semester'];
$language = $_GET['language'];

$sortArea = $_GET['sortArea'];

if(isset($_GET['sortOverall'])) $sortOverall = $_GET['sortOverall'];
if(isset($_GET['sortLecture'])) $sortLecture = $_GET['sortLecture'];
if(isset($_GET['sortExamType'])) $sortExamType = $_GET['sortExamType'];
if(isset($_GET['sortExamItem'])) $sortExamItem = $_GET['sortExamItem'];
if(isset($_GET['sortExamOther'])) $sortExamOther = $_GET['sortExamOther'];

$orderDirection = $_GET['orderDirection'];


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
	SELECT DISTINCT subjects.ID as ID, subject_name, subjects.ECTS AS subject_ECTS, semester, language
	".$sqlBody."
	WHERE ".$query."
	ORDER BY subjects.subject_name
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
		$relevance[$subjects['ID']] = 0;
		$interest[$subjects['ID']] = 0;
		$quality[$subjects['ID']] = 0;
		$reproductionTransfer[$subjects['ID']] = 0;
		$calculative[$subjects['ID']] = 0;
		$effort[$subjects['ID']] = 0;
		$training[$subjects['ID']] = 0;
		$reproductionTransfer[$subjects['ID']] = 0;
		$qualitativeQuantitative[$subjects['ID']] = 0;
		$amountRatings[$subjects['ID']] = "-";
	}
	else{ //Falls Bewertungen vorhanden
		$db = array("general0", "lecture0", "lecture1", "lecture2", "exam0", "exam1", "exam2", "exam3");
		$name = array("overallRating", "relevance", "interest", "quality", "reproductionTransfer", "calculative", "effort", "training");

		for ($i = 0; $i < sizeof($db); $i++) {
			$row = mysqli_fetch_array(mysqli_query($con, "SELECT AVG(".$db[$i].") FROM ratings WHERE subject_ID = '".$subjects['ID']."'"));
			${$name[$i]}[$subjects['ID']] = $row[0];
		}

		$row = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(recommendation) FROM ratings WHERE subject_ID = '".$subjects['ID']."' AND recommendation = 1"));
		$row2 = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(recommendation) FROM ratings WHERE subject_ID = '".$subjects['ID']."'"));
		$recoms[$subjects['ID']] = round(($row[0]/$row2[0])*100,0);

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
		SELECT DISTINCT lecturers.lecturer_ID, lecturers.last_name, lecturers.first_name
		".$sqlBody."
		WHERE subjects.ID = ".$subjects['ID']."
		ORDER BY abbr, lecturers.last_name
	";
	$result = mysqli_query($con,$sql);
	$lecturers = "";
	while($row = mysqli_fetch_assoc($result)){
		$sql_abbr = mysqli_query($con,"
						SELECT *
						FROM institutes
						JOIN lecturers_institutes ON institutes.institute_ID=lecturers_institutes.institute_ID
						WHERE lecturer_ID = '".$row['lecturer_ID']."'
			");
		$abbr = "";
		while($abbr_row = mysqli_fetch_assoc($sql_abbr)){
			$abbr .= "<a href=\"institute.php?institute_id=".$abbr_row['institute_ID']."\">".$abbr_row['abbr']."</a>, ";
		}
		$abbr = substr($abbr, 0, -2);



		$lecturers .= "<a href=\"lecturer.php?lecturer_id=".$row['lecturer_ID']."\">".substr($row['first_name'],0,1).". ".$row['last_name']."</a> (".$abbr.")<br>";
	}
	$lecturers = substr($lecturers, 0, -4);

	$data[] = array(
		'subject_name' => $subjects['subject_name'],
		'subject_id' => $subjects['ID'],
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

		'relevance' => $relevance[$subjects['ID']],
		'interest' => $interest[$subjects['ID']],
		'quality' => $quality[$subjects['ID']],
		
		'reproductionTransfer' => $reproductionTransfer[$subjects['ID']],
		'calculative' => $calculative[$subjects['ID']],
		'effort' => $effort[$subjects['ID']],
		'training' => $training[$subjects['ID']],
		
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
				$orderByHeader = "Anzahl Empfehlungen (in %)";
				$displayFromMax = "display:none";
				$displayNote = "";
				break;
		}
	}elseif($sortArea == "lecture"){
		switch ($sortLecture){
			case "relevance":
				$orderBy = "relevance";
				$orderByHeader = "Relevanz";
				$displayFromMax = "display:none";
				$displayNote = "";
				break;
			case "interest":
				$orderBy = "interest";
				$orderByHeader = "Interessantheit";
				$displayFromMax = "display:none";
				$displayNote = "";
				break;
			case "quality":
				$orderBy = "quality";
				$orderByHeader = "Qualität Materialien";
				$displayFromMax = "display:none";
				$displayNote = "";
				break;
		}
	}elseif($sortArea == "exam"){
		if($sortExamType == "written_oral"){
		switch ($sortExamItem){
				case "reproductionTransfer":
				$orderBy = "reproductionTransfer";
				$orderByHeader = "Reprod./Transfer";
				$displayFromMax = "display:none";
				$displayNote = "";
				break;
			case "calculative":
				$orderBy = "calculative";
				$orderByHeader = "Rechenlastigkeit";
				$displayFromMax = "display:none";
				$displayNote = "";
				break;
			case "effort":
				$orderBy = "effort";
				$orderByHeader = "Aufwand";
				$displayFromMax = "display:none";
				$displayNote = "";
				break;
			case "training":
				$orderBy = "training";
				$orderByHeader = "Pr.vorbereitung";
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
	$data = array_slice($data,0,50);

	//Ausgabe vorbereiten
	$content = "";
	foreach($data as $item){
		$content .= "
			<tr>
				<td><div><a href=\"index.php?subject=".$item['subject_id']."\" target=\"blank\">".$item['subject_name']."</a></div></td>
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
				
				<td>
				<!--
				<div style=\"width:40%; text-align:right\"><span class=\"glyphicon glyphicon-arrow-down\" style=\"color:orange\"></span></div>
				<div style=\"border-top:solid 2px;\"></div>
				<div class=\"contenedor\">
					<div class=\"contenido\">3</div>
					<div class=\"contenido\">0</div>
					<div class=\"contenido\">-3</div>
				</div>
				-->
				</td>
				
			</tr>
		";
	}

	$table="

		<span style=\"$displayNote\"><br><i>Hinweis:</i> Die Skala der Bewertungskriterien von Vorlesung und Prüfung reicht von -3 bis 3 (\"#Anmerkungen Prüfung\" ausgenommen).</span>
		<table class=\"table table-striped table-condensed searchresulttable\">
			<thead>
				<tr>
					<th>Veranstaltung</th>
					<th>Typ</th>
					<th>Beinhaltet in</th>
					<th>Level</th>
					<th>ECTS</th>
					<th>Dozent(en)</th>
					<th>Semester</th>
					<th>Sprache</th>
					<th class=\"nowrap\">".$orderByHeader."</th>
				</tr>
			</thead>
			<tbody>
				".$content."
			</tbody>
		</table>
		<script>
			//Startet Pagination
			$(document).ready(function() {
				$(\".searchresulttable\").simplePagination('onInit', function(){
					$('#tabelleLaden').hide();
				});
				$( \"td\" ).children().niceScroll();
			});
		</script>
	";
} else{
	$table = "<br><h4>Für die gewählten Einschränkungen befinden sich keine Veranstaltungen in unserer Datenbank.</h4>";
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

//Planer badge
mysqli_query($con, "INSERT INTO `users_search`(`user_id`, `time_stamp`) VALUES ($user_id, now())");
$result = mysqli_query($con, "SELECT * FROM users_search");
if(mysqli_num_rows($result) >= 15){
	$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '$user_id' AND badge_id = 97");
	if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
		$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES ('$user_id',97)";
		if ($con->query($sql2) == TRUE) {
			echo "Du hast eine neue Errungenschaft freigeschaltet! Schau gleich nach unter Profil > Errungenschaften.";
		}
	}	
}


echo $table;
?>

<?php
/*
ANHANG - Benutze Sortierfunktion kommt von hier: http://php.net/manual/de/function.ksort.php
Falls Link kaputt, hier das wichtigste:

---------------

Here is a function to sort an array by the key of his sub-array.

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

?>

Example
<?php
$info = array("peter" => array("age" => 21,
                                           "gender" => "male"
                                           ),
                   "john"  => array("age" => 19,
                                           "gender" => "male"
                                           ),
                   "mary" => array("age" => 20,
                                           "gender" => "female"
                                          )
                  );

sksort($info, "age");
var_dump($info);

sksort($info, "age", true);
var_dump($ifno);
?>

This will be the output of the example:

//DESCENDING SORT
array(3) {
  ["peter"]=>
  array(2) {
    ["age"]=>
    int(21)
    ["gender"]=>
    string(4) "male"
  }
  ["mary"]=>
  array(2) {
    ["age"]=>
    int(20)
    ["gender"]=>
    string(6) "female"
  }
  ["john"]=>
  array(2) {
    ["age"]=>
    int(19)
    ["gender"]=>
    string(4) "male"
  }
}

//ASCENDING SORT
array(3) {
  ["john"]=>
  array(2) {
    ["age"]=>
    int(19)
    ["gender"]=>
    string(4) "male"
  }
  ["mary"]=>
  array(2) {
    ["age"]=>
    int(20)
    ["gender"]=>
    string(6) "female"
  }
  ["peter"]=>
  array(2) {
    ["age"]=>
    int(21)
    ["gender"]=>
    string(4) "male"
  }
}
*/
?>
