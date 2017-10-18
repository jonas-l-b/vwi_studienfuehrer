<?php

include "connect.php";

?>

<?php
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'Jahr',
        'm' => 'Monat',
        'w' => 'Woche',
        'd' => 'Tag',
        'h' => 'Stunde',
        'i' => 'Minute',
        's' => 'Sekunde',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
			if($v == 'Jahr' || $v == 'Monat' || $v == 'Tag'){
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 'en' : '');
			}
			else {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 'n' : '');
			}
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? 'vor ' . implode(', ', $string) : 'gerade eben';
}
?>

<?php
$order = "rating_bestFirst";
if(isset($_POST['order'])) $order = $_POST['order'];

if(isset($subjectData['ID'])){
	$subject_id = $subjectData['ID'];
}else{
	$subject_id = $_POST['subject_id'];
}

if(isset($userRow['user_ID'])){
	$user_id = $userRow['user_ID'];
}else{
	$user_id = $_POST['user_id'];
}

switch($order){
	case "date_newFirst":
		$orderBy = "time_stamp";
		$orderDirection = "DESC";
		break;
	case "date_newLast":
		$orderBy = "time_stamp";
		$orderDirection = "ASC";
		break;
	case "rating_bestFirst":
		$orderBy = "comment_rating";
		$orderDirection = "DESC";
		break;
	case "rating_worstFirst":
		$orderBy = "comment_rating";
		$orderDirection = "ASC";
		break;
}

$sql = "
	SELECT * FROM ratings
	WHERE subject_ID = '".$subject_id."'
	ORDER BY ".$orderBy." ".$orderDirection."";

$result = mysqli_query($con,$sql);

if (mysqli_num_rows($result) == 0){
	echo "Noch keine Kommentare vorhanden.";
}

while($comments = mysqli_fetch_assoc($result)){
	
	$recommend = "
		<div>
			<img src=\"pictures/greentick.png\" style=\"width:12px;height:12px;vertical-align:middle; margin-bottom:1.5px;\">
			<span style=\"font-weight:bold; font-size:12px\">Der Kommentator würde diese Veranstaltung empfehlen.</span>
		</div>";
	if ($comments['recommendation'] == 0) $recommend = "";
	
	$sql2 = "
		SELECT *
		FROM ratings
		JOIN users ON ratings.user_ID = users.user_ID
		WHERE ID = '".$comments['ID']."';
	";
	$join = mysqli_query($con,$sql2);
	$rows = mysqli_fetch_assoc($join);
	
	//Erstellt Variable, um Bearbeiten-Button nur für Ersteller anzuzeigen
	$displayEdit = "display:none;";
	$editClassIdentifier = "";
	$displayReport ="";
	
	//displayEdit auskommentiert, da noch diskutiert werden muss!
	//Falls Funktion nicht behalten werden soll, alles löschen, was damit in Zusammenhang steht!
	
	if($comments['user_ID'] == $user_id){
		$displayEdit = "";
		$editClassIdentifier = "editButtonIdentificationClass";
		$displayReport = "display:none;";
	}

	echo "
		<div class=\"well\" style=\"background-color:white; border-radius:none\">
			<div id=\"bewertungMitID".$comments['ID']."\" class=\"media einzelKommentar\">
				<div class=\"media-left\">
					<p style=\"white-space: nowrap; padding-right:10px;\"><span style=\"font-weight:bold; cursor: pointer; cursor: hand;\" onclick=\"colorChange(this.id)\" id=\"".$comments['ID']."do\"> &minus; </span><span style=\"padding-right:3px;\" id=\"".$comments['ID']."\">".$comments['comment_rating']."</span><span style=\"font-weight:bold; cursor: pointer; cursor: hand;\" onclick=\"colorChange(this.id)\" id=\"".$comments['ID']."up\">+</span></p>
					<p class=\"nowrap confirmation\" id=\"".$comments['ID']."confirmation\"></p>
				</div>
				<div class=\"media-body\">
					<p><span id=\"ausrufezeichen".$comments['ID']."\" class=\"ausrufezeichen glyphicon glyphicon-exclamation-sign pull-right\"></span> ".$comments['comment']." </p>
					".$recommend."
					<hr style=\"margin:10px\">
					<div style=\"font-size:10px\">
						".$rows['username']." &#124; ". time_elapsed_string($comments['time_stamp'])."
						<span style=\"float:right;\">
							<button type=\"button\" id=\"bewertungAendernButton\" style=\"".$displayEdit."\" role=\"button\" class=\"editTrashButton $editClassIdentifier\"  title=\"Kommentar bearbeiten\"> <span class=\"glyphicon glyphicon-pencil\"></span></button>
							<button type=\"button\" style=\"".$displayEdit."\" href=\"#deleteModal\" role=\"button\" class=\"editTrashButton\" data-toggle=\"modal\" title=\"Kommentar löschen\"> <span class=\"glyphicon glyphicon-trash\"></span></button>
							<button onclick=\"showStats(this.id)\" id=\"commentstats".$comments['ID']."\" type=\"button\" href=\"#\" role=\"button\" class=\"editTrashButton\"> <span class=\"glyphicon glyphicon-stats\" title=\"Einzelbewertung anzeigen\" ></span></button>
						</span>
						<span style=\"float:right; ".$displayReport."\">
							<button type=\"button\" role=\"button\" data-toggle=\"modal\" data-id=\"".$comments['ID']."\" class=\"editTrashButton reportButton\" title=\"Kommentar melden\"> <span class=\"glyphicon glyphicon-exclamation-sign\"></span></button>
						</span>
					</div>
				</div>
			</div>
		</div>
	";	
}

?>