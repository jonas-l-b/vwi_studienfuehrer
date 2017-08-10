<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="treeWelcome">
	<h3>Willkommen zum Studienführer</h3>
</div>

<div class="container" style="margin-top:20px">
	<?php
	/*Vorbereitung*/
	//Hide all
	$displayTree = "style=\"display:none\"";
	$displaySearch = "style=\"display:none\"";
	
	//Enable all buttons
	$displayButtonTree = "";
	$displayButtonSearch = "";
	
	
	if (isset($_POST['btn-toTree'])){ //Wenn Baum-Button geklickt
		$displayTree = "";
		$displaySearch = "style=\"display:none\"";
		
		$displayButtonTree = "disabled";
	}
	
	if (isset($_POST['btn-toSearch'])){ //Wenn Suche-Button geklickt
		$displayTree = "style=\"display:none\"";
		$displaySearch = "";
		
		$displayButtonSearch = "disabled";
	}
	?>
	
	<?php /*FÜR ERGEBNISTABELLE*/
	$displayTable = "style=\"display:none\"";
	
	/*Suchmasken-Einträge vorbereiten (für erstes Aufrufen der Seite; werden nach Button-Betätigung wieder modifiziert)*/
	//checkboxes
	$q = mysqli_query($con,"SELECT * FROM moduletypes");
	$array_types = array();
	while($row = mysqli_fetch_assoc($q)){
		$array_types[] .= $row['name'];
	}

	foreach($array_types as $type){
		$checkbox[$type] = "checked";		
	}
	
	//module
	$result = mysqli_query($con,"SELECT * FROM modules");
	while($row = mysqli_fetch_assoc($result)){
		$moduleSelection[$row['module_ID']] = "";
	}
	$moduleSelection['none'] = "selected";
	
	//lecturer
	$result = mysqli_query($con,"SELECT * FROM lecturers");
	while($row = mysqli_fetch_assoc($result)){
		$lecturerSelection[$row['lecturer_ID']] = "";
	}
	$lecturerSelection['none'] = "selected";
	
	//institute
	$result = mysqli_query($con,"SELECT * FROM institutes");
	while($row = mysqli_fetch_assoc($result)){
		$instituteSelection[$row['institute_ID']] = "";
	}
	$instituteSelection['none'] = "selected";
	
	
	if (isset($_POST['btn-filterSort'])){ //Wenn Button geklickt
		//Tabelle Anzeigen und auch sicherstellen, dass der restliche Search-Bereich angezeigt wird
		$displayTable = "";
		$displaySearch = "";
		$displayButtonSearch = "disabled";
		
		//Get POST
		
		$modulType = $_POST['modulType'];
		$module = filter_var($_POST['module'], FILTER_SANITIZE_STRING);
		$lecturer = filter_var($_POST['lecturer'], FILTER_SANITIZE_STRING);
		$institute = filter_var($_POST['institute'], FILTER_SANITIZE_STRING);
		$level = filter_var($_POST['level'], FILTER_SANITIZE_STRING);
		$semester = filter_var($_POST['semester'], FILTER_SANITIZE_STRING);
		$language = filter_var($_POST['language'], FILTER_SANITIZE_STRING);
		$orderBy = filter_var($_POST['orderBy'], FILTER_SANITIZE_STRING);
		$orderDirection = filter_var($_POST['orderDirection'], FILTER_SANITIZE_STRING);
		
		
		/*Suchmasken-Einträge wieder einfügen*/
		//Checkboxes
		foreach($array_types as $type){
			$checkbox[$type] = "";		
		}
		foreach($modulType as $check) {
			if($check!="") $checkbox[$check] = "checked";
		};
		
		//module
		$moduleSelection['none'] = "";
		$moduleSelection[$module] = "selected";
		
		//lecturer
		$lecturerSelection['none'] = "";
		$lecturerSelection[$lecturer] = "selected";
		
		//institute
		$instituteSelection['none'] = "";
		$instituteSelection[$institute] = "selected";
		
		//level
		$levelSelection[$level] = "selected";
		
		//semester
		$semesterSelection[$semester] = "selected";
		
		//language
		$languageSelection[$language] = "selected";
		
		//orderBy
		$sortSelection[$orderBy] = "selected";
		
		//orderDirection
		$directionSelection[$orderDirection] = "selected";
		
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
				
		/*Bewertungen abfragen und alles zusammen in ein Feld packen*/
		$allSubjects = mysqli_query($con,$sql1);
			
		while($subjects = mysqli_fetch_assoc($allSubjects)){

			$result = mysqli_query($con,"SELECT * FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
			if (mysqli_num_rows($result) == 0){ //Falls noch keine Bewertungen vorhanden
				$crit1_percent[$subjects['ID']] = "-";
				$crit2_percent[$subjects['ID']] = "-";
				$crit3_percent[$subjects['ID']] = "-";
				$crit4_percent[$subjects['ID']] = "-";
				$crit5_percent[$subjects['ID']] = "-";
				$overall_percent[$subjects['ID']] = "-";
				$recommendations[$subjects['ID']] = "-";
				$commentCount[$subjects['ID']] = "-";
			}
			else{ //Falls Bewertungen vorhanden
				//Crit1
				$result2 = mysqli_query($con,"SELECT SUM(crit1) AS value_sum FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit1Sum = $row['value_sum'];

				$result2 = mysqli_query($con,"SELECT COUNT(crit1) AS value_count FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit1Count = $row['value_count'];

				$crit1_percent[$subjects['ID']] = round(($crit1Sum / ($crit1Count*7) ) * 100);

				//Crit2
				$result2 = mysqli_query($con,"SELECT SUM(crit2) AS value_sum FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit2Sum = $row['value_sum'];

				$result2 = mysqli_query($con,"SELECT COUNT(crit2) AS value_count FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit2Count = $row['value_count'];

				$crit2_percent[$subjects['ID']] = round(($crit2Sum / ($crit2Count*7) ) * 100);

				//Crit3
				$result2 = mysqli_query($con,"SELECT SUM(crit3) AS value_sum FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit3Sum = $row['value_sum'];

				$result2 = mysqli_query($con,"SELECT COUNT(crit3) AS value_count FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit3Count = $row['value_count'];

				$crit3_percent[$subjects['ID']] = round(($crit3Sum / ($crit3Count*7) ) * 100);

				//Crit4
				$result2 = mysqli_query($con,"SELECT SUM(crit4) AS value_sum FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit4Sum = $row['value_sum'];

				$result2 = mysqli_query($con,"SELECT COUNT(crit4) AS value_count FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit4Count = $row['value_count'];

				$crit4_percent[$subjects['ID']] = round(($crit4Sum / ($crit4Count*7) ) * 100);

				//Crit5
				$result2 = mysqli_query($con,"SELECT SUM(crit5) AS value_sum FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit5Sum = $row['value_sum'];

				$result2 = mysqli_query($con,"SELECT COUNT(crit5) AS value_count FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$crit5Count = $row['value_count'];

				$crit5_percent[$subjects['ID']] = round(($crit5Sum / ($crit5Count*7) ) * 100);

				//Overall
				$overall_percent[$subjects['ID']] = round(($crit1_percent[$subjects['ID']]+$crit2_percent[$subjects['ID']]+$crit3_percent[$subjects['ID']]+$crit4_percent[$subjects['ID']]+$crit5_percent[$subjects['ID']])/5);
				
				//Recommendations
				$result2 = mysqli_query($con,"SELECT SUM(recommendation) AS value_sum FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$recommendations[$subjects['ID']] = $row['value_sum'];
				
				//commentCount
				$result2 = mysqli_query($con,"SELECT COUNT(ID) AS value_count FROM ratings WHERE subject_ID = '".$subjects['ID']."'");
				$row = mysqli_fetch_assoc($result2);
				$commentCount[$subjects['ID']] = $row['value_count'];
			}
			/*Alles in ein Feld*/
			/*Vorbereitung*/
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
				'crit1' => $crit1_percent[$subjects['ID']],
				'crit2' => $crit2_percent[$subjects['ID']],
				'crit3' => $crit3_percent[$subjects['ID']],
				'crit4' => $crit4_percent[$subjects['ID']],
				'crit5' => $crit5_percent[$subjects['ID']],
				'overall' => $overall_percent[$subjects['ID']],
				'recommendations' => $recommendations[$subjects['ID']],
				'commentCount' => $commentCount[$subjects['ID']]
			);
		}
		if(mysqli_num_rows($allSubjects)!=0){ //Nur ausführen, wenn ganz am Anfang Fächer zurückgegeben wurden; führt sonst zu Fehlern
			$orderBy = filter_var($_POST['orderBy'], FILTER_SANITIZE_STRING);
			$orderDirection = filter_var($_POST['orderDirection'], FILTER_SANITIZE_STRING);

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
						<td><div>".$item[$orderBy]." %</div></td>
					</tr>
				";
			}
			
			$orderByHeader = "";
			switch($orderBy){
				case "overall":
					$orderByHeader = "Gesamtwertung";
					break;
				case "crit1":
					$orderByHeader = "Kriterium 1";
					break;
				case "crit2":
					$orderByHeader = "Kriterium 2";
					break;
				case "crit3":
					$orderByHeader = "Kriterium 3";
					break;
				case "crit4":
					$orderByHeader = "Kriterium 4";
					break;
				case "crit5":
					$orderByHeader = "Kriterium 5";
					break;
				case "recommendations":
					$orderByHeader = "#Empfehlungen";
					break;
				case "commentCount":
					$orderByHeader = "#Kommentare";
					break;
			}
			
			$table="
				<hr>
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
	?>

	<h4 align="center">Wie möchtest du deine Veranstaltung finden?</h4>
	<div align="center">
		<form method="post">
			<button style="width:330px" type="submit" class="btn btn-primary" name="btn-toTree" <?php echo $displayButtonTree ?>>Veranstaltung aus Verzeichnis wählen</button>
			<button style="width:330px" type="submit" class="btn btn-primary" name="btn-toSearch" <?php echo $displayButtonSearch ?>>Veranstaltungen nach Kriterien durchsuchen</button>
		</form>
	</div>
	
	<div <?php echo $displayTree ?>>
		<?php
		//Erstellt das Verzeichnis
		$content = "";
		
		$array = array(array("Bachelor - Kernprogramm", "bachelor_basic"), array("Bachelor - Vertiefungsprogramm", "bachelor"), array("Master", "master"));
		for($x = 0; $x <= 2; $x++) {
			$content .= "<li><label class=\"tree-toggler nav-header treetop\" style=\"color:rgb(0, 51, 153)\"><strong>".$array[$x][0]."</strong></label>";
			
				$content .= "<ul class=\"nav nav-list tree\">";
				$result = mysqli_query($con,"SELECT * FROM moduletypes");
				while($modulTypes = mysqli_fetch_assoc($result)){ //Modultyp
					$content .= "<li><label class=\"tree-toggler nav-header\">".$modulTypes['name']."</label>";
					
					$content .= "<ul class=\"nav nav-list tree\">";
					$result2 = mysqli_query($con,"
						SELECT modules.name AS module_name, levels.name AS level_name, type
						FROM modules
						JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
						JOIN levels ON modules_levels.level_ID = levels.level_ID
						WHERE levels.name = '".$array[$x][1]."' AND type = '".$modulTypes['name']."';
					");
					while($modules = mysqli_fetch_assoc($result2)){ //Modulname
						$content .= "<li><label class=\"tree-toggler nav-header\">".$modules['module_name']."</label>";
						
						$content .= "<ul class=\"nav nav-list tree\">";
						$result3 = mysqli_query($con,"
							SELECT subject_name, subjects.code AS subject_code, modules.name AS module_name
							FROM subjects
							JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
							JOIN modules ON subjects_modules.module_ID = modules.module_ID
							WHERE modules.name = '".$modules['module_name']."'
						");
						while($subjects = mysqli_fetch_assoc($result3)){ //Veranstaltungsname
							$content .= "<li><a target=\"_blank\" href=\"index.php?subject=".$subjects['subject_code']."\">".$subjects['subject_name']."</a></li>";
						}
						$content .= "</ul>";
						$content .= "</li>";
					}
					$content .= "</ul>";
					$content .= "</li>";
				}
				$content .= "</ul>";
			$content .= "</li>";
		}
		?>
		
		<hr>
		<h2>Veranstaltungsverzeichnis</h2>
		<div class="well" style="width:500px; padding: 8px 0;">
			<div>
				<ul class="nav nav-list">
					<?php echo $content ?>
				</ul>
			</div>
		</div>

		<script> //Schließt das Verzeichnis beim Laden der Seite
		$(document).ready(function () {
			$('label.tree-toggler').click(function () {
				$(this).parent().children('ul.tree').toggle(300);
			});
		});
		$(document).ready(function () {
		$('label.tree-toggler').parent().children('ul.tree').toggle(300);
		});
		</script>
	</div>
	
	
	<div <?php echo $displaySearch ?>>
		<hr>
		<h2>Veranstaltungssuche</h2>
		<p><i>Vorsicht beim Filtern: Wird beim Modul-Typ "BWL" angegeben, beim Modul aber "Informatik", kann es natürlich keine Ergebnisse geben. Ebenso können sich beispielsweise Dozent und Institut schnell gegenseitig ausschließen.</i></p>

		<form id="filtersort" class="form-horizontal" method="post">	
			<div class="row">
				<div class="col-md-4">
					
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4">Modul-Typ:</label>
							<div class="col-md-8">
								<div class="row">	
						<!--	
									<div class="col-md-6">
										<div class="checkbox"><label><input type="checkbox" name="modulType[]" value="BWL" <?php if(isset($checkbox['BWL'])) echo $checkbox['BWL']?> >BWL</label></div>
										<div class="checkbox"><label><input type="checkbox" name="modulType[]" value="VWL" <?php if(isset($checkbox['VWL'])) echo $checkbox['VWL']?>>VWL</label></div>
										<div class="checkbox"><label><input type="checkbox" name="modulType[]" value="INFO" <?php if(isset($checkbox['INFO'])) echo $checkbox['INFO']?>>INFO</label></div>
									</div>
									<div class="col-md-6">
										<div class="checkbox"><label><input type="checkbox" name="modulType[]" value="OR" <?php if(isset($checkbox['OR'])) echo $checkbox['OR']?>>OR</label></div>
										<div class="checkbox"><label><input type="checkbox" name="modulType[]" value="ING" <?php if(isset($checkbox['ING'])) echo $checkbox['ING']?>>ING</label></div>
										<div class="checkbox"><label><input type="checkbox" name="modulType[]" value="Sonstige" <?php if(isset($checkbox['Sonstige'])) echo $checkbox['Sonstige']?>>Sonstige</label></div>
									</div>
						-->
						
									<!--
									VORSICHT!
									Folgender Code erstellt Checkboxen dynamisch. Funktioniert allerdings (kontrolliert) nur
									bis zu 12 Checkboxen wegen der Spaltenaufteilung (col-md-x), siehe switch unten.
									Sollte aber eigentlich genügen.
									-->
									
									<?php
									$columnSize = "";
									switch(count($array_types)){
										case 1:
										case 2:
										case 3:
										$columnSize = 12;
										break;
										case 4:
										case 5:
										case 6:
										$columnSize = 6;
										break;
										case 7:
										case 8:
										case 9:
										$columnSize = 4;
										break;
										case 10:
										case 11:
										case 12:
										$columnSize = 3;
										break;
									}
									?>
									
									<div class="col-md-<?php echo($columnSize)?>">
										<?php
										for ($j = 1; $j <= count($array_types); $j++) {
											$i = $j-1; //Nicht einfach "$j = 0; $j < count($array_types)" damit Modulus-Operation unten funktioniert
											
											if(isset($checkbox[$array_types[$i]])){ //Bereitet Checkboxfüllen vor
												$checked = $checkbox[$array_types[$i]];
											} else{
												$checked = "";
											}
											
											echo("
												<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"modulType[]\" value=\"".$array_types[$i]."\"".$checked." >".$array_types[$i]."</label></div>
											");
											
											if(($j%3)==0){ //Fängt eine neue Spalte nach drei Einträgen an
												echo("</div><div class=\"col-md-".$columnSize."\">");
											}
										}	
										?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<?php
					$mod_selection = "<option value=\"none\">(Keine Einschränkung)</option>";
					$result = mysqli_query($con,"SELECT * FROM modules ORDER BY name");
					while($mod_row = mysqli_fetch_assoc($result)){
						$mod_selection .= "<option value=".$mod_row['module_ID']." ".$moduleSelection[$mod_row['module_ID']].">".$mod_row['name']." [".$mod_row['code']."]</option>";
					}
					?>
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Modul:</label>
							<div class="col-md-8">          
								<select class="form-control" name="module">
									<?php echo $mod_selection ?>
								</select>
							</div>
						</div>
					</div>
				
				</div>

				<div class="col-md-4">
					
					<?php
					$lec_selection = "<option value=\"none\">(Keine Einschränkung)</option>";
					$sql = "
						SELECT *
						FROM lecturers
						JOIN lecturers_institutes ON lecturers.lecturer_ID=lecturers_institutes.lecturer_ID
						JOIN institutes ON lecturers_institutes.institute_ID=institutes.institute_ID
						ORDER BY name, last_name
					";
					$result = mysqli_query($con,$sql);
					while($row = mysqli_fetch_assoc($result)){
						$lec_selection .= "<option value=".$row['lecturer_ID']." ".$lecturerSelection[$row['lecturer_ID']].">".$row['last_name'].", ".$row['first_name']." (".$row['abbr'].")</option>";
					}
					?>		
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Dozent:</label>
							<div class="col-md-8">          
								<select class="form-control" name="lecturer">
									<?php echo $lec_selection ?>
								</select>
							</div>
						</div>
					</div>
					
					<?php
					$insti_selection = "<option value=\"none\">(Keine Einschränkung)</option>";
					$result = mysqli_query($con, "SELECT * FROM institutes ORDER BY name");
					while($row = mysqli_fetch_assoc($result)){
						$insti_selection .= "<option value=".$row['institute_ID']." ".$instituteSelection[$row['institute_ID']].">".$row['name']." (".$row['abbr'].")</option>";
					}
					?>
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Institut:</label>
							<div class="col-md-8">          
								<select class="form-control" name="institute">
									<?php echo $insti_selection ?>
								</select>
							</div>
						</div>
					</div>
					
				</div>
				
				<div class="col-md-4">
					
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Level:</label>
							<div class="col-md-8">          
								<select name="level" class="form-control" required>
									<option value="none" <?php if(isset($levelSelection['none'])) echo $levelSelection['none']?>>(Keine Einschränkung)</option>
									<option value="bachelor_basic" <?php if(isset($levelSelection['bachelor_basic'])) echo $levelSelection['bachelor_basic']?>>Bachelor: Kernprogramm</option>
									<option value="bachelor" <?php if(isset($levelSelection['bachelor'])) echo $levelSelection['bachelor']?>>Bachelor: Vertiefungsprogramm</option>
									<option value="master" <?php if(isset($levelSelection['master'])) echo $levelSelection['master']?>>Master</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4">Semester:</label>
							<div class="col-md-8">          
								<select class="form-control" name="semester">
									<option value="none" <?php if(isset($semesterSelection['none'])) echo $semesterSelection['none']?>>(Keine Einschränkung)</option>
									<option value="Winter" <?php if(isset($semesterSelection['Winter'])) echo $semesterSelection['Winter']?>>Winter</option>
									<option value="Sommer" <?php if(isset($semesterSelection['Sommer'])) echo $semesterSelection['Sommer']?>>Sommer</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Sprache:</label>
							<div class="col-md-8">          
								<select class="form-control" name="language">
									<option value="none" <?php if(isset($languageSelection['none'])) echo $languageSelection['none']?>>(Keine Einschränkung)</option>
									<option value="Deutsch" <?php if(isset($languageSelection['Deutsch'])) echo $languageSelection['Deutsch']?>>Deutsch</option>
									<option value="Englisch" <?php if(isset($languageSelection['Englisch'])) echo $languageSelection['Englisch']?>>Englisch</option>
								</select>
							</div>
						</div>
					</div>
					
				</div>
			</div>

			<br><br>
			
			<div class="row"><div class="control-group"> <div class="controls form-inline">
				<span><strong>Sortieren nach</strong></span>
				
				<select class="form-control" name="orderBy">
					<option value="overall" <?php if(isset($sortSelection['overall'])) echo $sortSelection['overall']?>>Gesamtwertung</option>
					<option value="crit1" <?php if(isset($sortSelection['crit1'])) echo $sortSelection['crit1']?>>Bewertung Veranstaltung</option>
					<option value="crit2" <?php if(isset($sortSelection['crit2'])) echo $sortSelection['crit2']?>>Bewertung Aufwand</option>
					<option value="crit3" <?php if(isset($sortSelection['crit3'])) echo $sortSelection['crit3']?>>Bewertung Kriterium 3</option>
					<option value="crit4" <?php if(isset($sortSelection['crit4'])) echo $sortSelection['crit4']?>>Bewertung Kriterium 4</option>
					<option value="crit5" <?php if(isset($sortSelection['crit5'])) echo $sortSelection['crit5']?>>Bewertung Kriterium 5</option>
					<option value="recommendations" <?php if(isset($sortSelection['recommendations'])) echo $sortSelection['recommendations']?>>Anzahl Veranstaltungsempfehlungen</option>
					<option value="commentCount" <?php if(isset($sortSelection['commentCount'])) echo $sortSelection['commentCount']?>>Anzahl abgegebene Kommentare</option>
				</select>
				
				<select class="form-control" name="orderDirection">
					<option value="ASC" <?php if(isset($directionSelection['ASC'])) echo $directionSelection['ASC']?>>Absteigend</option>
					<option value="DESC" <?php if(isset($directionSelection['DESC'])) echo $directionSelection['DESC']?>>Aufsteigend</option>
				</select>
				
				<button type="submit" class="btn btn-primary" name="btn-filterSort">Filtern & Sortieren</button>
			</div></div></div>
		</form>

		<script>
			//Verhindert, dass kein Modultyp ausgewählt wird
			$(document).ready(function() {
				$("form#filtersort").submit (function() {
					if($('input[type="checkbox"]:checked').length) {
						return true;
					} else {
						alert("Wähle mindestens einen Modultyp aus - andernfalls kann es keine Ergebnisse geben.");
						return false;
					}           
				});
			});
		</script>

		<!--Start Ergebnistabelle-->
		
		<div <?php echo $displayTable ?>>
			<?php echo $table ?>
		</div>
		
	</div>
	
</div>
<script src="res/lib/jquery.simplePagination.js"></script>
<script src="res/lib/jquery.nicescroll-master/jquery.nicescroll.js"></script>
<script>
	//Startet Pagination
	$(document).ready(function() {
		$(".searchresulttable").simplePagination();
		$( "td" ).children().niceScroll();
	});
</script>
</body>
</html>

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