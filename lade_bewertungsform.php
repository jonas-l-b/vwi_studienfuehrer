<?php

/*
*		Dieses Skript liefert das Bewertungsform unbefüllt oder befüllt
*/

include "sessionsStart.php";
include "connect.php";
?>

<?php
if (isset($_GET['subject'])){
	$subject = $_GET['subject'];
	$userID = $userRow['user_ID'];

	$lectureItems = array(
		"Für wie relevant bewertest du den Vorlesungsbesuch (Folien selbsterklärend? Vorlesung behandelt zusätzlichen Stoff?)?",
		"Wie interessant war die Vorlesung gestaltet?",
		"Wie bewertest du die Veranstaltungsmaterialien?"
	);

	$lectureItemsLabels = array(
		array("Nicht relevant", "Sehr relevant"),
		array("Uninteressant", "Sehr interessant"),
		array("Unstrukturiert, Unvollständig", "Strukturiert, Selbsterklärend, Vollständig")
	);
	
	$examItems = array(
		"Lag der Schwerpunkt auf Reproduktion oder auf Transfer?",
		"Wie rechenlastig war die Prüfung?",
		"War der Aufwand zur Prüfungsvorbereitung dem Leistungsumfang (ECTS) der Veranstaltung gegenüber angemessen?",
		"Wie hat dich die Gesamtheit der Lernmöglichkeiten (inkl. Tutorium, Übung, Forum, Buch, ...) auf die Prüfung vorbereitet?",
	);
	$examItemsLabels = array(
		array("Reproduktion", "Transfer"),
		array("Nicht rechenlastig", "Sehr rechenlastig"),
		array("Aufwand deutlich geringer", "Aufwand deutlich höher"),
		array("Schlecht", "Gut")
	);

	$generalItems = array(
		"Wie bewertest du die Veranstaltung ingesamt? (1 = schlecht, 10 = gut)",
	);

	//Values if already filleds
	if(isset($_GET['filled'])){
		$statement1 = $con->prepare("SELECT * FROM ratings
			WHERE ratings.user_ID = ? AND ratings.subject_ID = (SELECT ID FROM subjects WHERE subjects.ID = ?);");
		$statement1->bind_param('ss', $userID, $subject);
		$statement1->execute();
		$result = $statement1->get_result();
		$ratingData = mysqli_fetch_assoc($result);

		$lectureValues = array($ratingData['lecture0'], $ratingData['lecture1'], $ratingData['lecture2'], $ratingData['lecture3']);
		$examValues = array($ratingData['exam0'], $ratingData['exam1'], $ratingData['exam2'], $ratingData['exam3']);
		$examValues2 = array($ratingData['exam4'], $ratingData['exam5']);
		//examType
		switch($ratingData['examType']){
			case "written":
				$written = "selected";
				$oral = "";
				$other = "";
				break;
			case "oral":
				$written = "";
				$oral = "selected";
				$other = "";
				break;
			case "other":
				$written = "";
				$oral = "";
				$other = "selected";
				break;
		}
		//examText
		$examText = $ratingData['examText'];
		//examSemester
		$examSemester = $ratingData['examSemester'];
		//General
		$general0 = $ratingData['general0'];
		//Recommendation
		if($ratingData['recommendation'] == 1){
			$recom1 = "checked";
			$recom0 = "";
		} else{
			$recom1 = "";
			$recom0 = "checked";
		}
		//comment
		$comment = $ratingData['comment'];
	}else{
		$lectureValues = array(5,5,5,5);
		$examValues = array(5,5,5,5);
		$examValues2 = array(0,0);
		$written = "";
		$oral = "";
		$other = "";
		$examText = "";
		$examSemester = "";
		$general0 = "";
		$recom1 = "";
		$recom0 = "";
		$comment = "";
	}

	echo $twig->render('bewerten.template.html',
						array(	'subject' => $subject,
								'form_target' => 'rating_submit.php',
								'button_text' => 'Bewertung abschicken',
								'lectureItems' => $lectureItems,
								'lectureItemsLabels' => $lectureItemsLabels,
								'examItems' => $examItems,
								'examItemsLabels' => $examItemsLabels,
								'generalItems' => $generalItems,

								'lectureValues' => $lectureValues,
								'typeWritten' => $written,
								'typeOral' => $oral,
								'typeOther' => $other,
								'examValues' => $examValues,
								'examValues2' => $examValues2,
								'examText' => $examText,
								'examSemester' => $examSemester,
								'general0' => $general0,
								'weiterempfehlen_ja' => $recom1,
								'weiterempfehlen_nein' => $recom0,
								'comment' => $comment,
							));
}else{
	exit;
}
?>
