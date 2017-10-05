<?php

/*
*		Dieses Skript liefert das Bewertungsform unbefüllt oder befüllt
*/


include "sessionsStart.php";
include "connect.php";



if (isset($_GET['subject'])){
	
	$subject = $_GET['subject'];
	$userID = $userRow['user_ID'];
	if(isset($_GET['filled'])){
		
		
		/*Datenbankabfrage, um bestehende Werte ins Modal einzutragen*/
		$statement1 = $con->prepare("SELECT * FROM ratings
			WHERE ratings.user_ID = ? AND ratings.subject_ID = (SELECT ID FROM subjects WHERE subjects.code = ?);");
		$statement1->bind_param('ss', $userID, $subject);
		$statement1->execute();
		$result = $statement1->get_result();
		$ratingData = mysqli_fetch_assoc($result);
		//Rating
		for ($i = 1; $i <= 5; $i++) {
			for ($j = 1; $j <= 7; $j++) {
				if($ratingData['crit'.$i] == $j){
					$crit[$i][$j] = "checked";
				} else{
					$crit[$i][$j] = "";
				}
			}
		}
		//Recommendation
		if($ratingData['recommendation'] == 1){
			$recom1 = "checked";
			$recom0 = "";
		} else{
			$recom1 = "";
			$recom0 = "checked";
		}
		echo $twig->render('bewerten.template.html', 
							array(	'subject' => $subject,
									'form_target' => 'rating_change.php',
									'button_text' => 'Bewertung ändern',
									'isChecked' => $crit,
									'weiterempfehlen_ja' => $recom1,
									'weiterempfehlen_nein' => $recom0,
									'kommentar' => $ratingData['comment'],
								));
	}else{
		for ($i = 1; $i <= 5; $i++) {
			for ($j = 1; $j <= 7; $j++) {
				$crit[$i][$j] = "";
			}
		}
		
		$lectureItems = array(
			"Wie bewertest du die Vorlesung ingesamt?",
			"Wie relevant war die Vorlesung für die Klausur?",
			"Wie interessant fadest du die Vorlesung?",
			"Wie war die Qualität der Vorlesungsmaterialien?",
		);
		
		$examItems = array(
			"Wie bewertest du die Klausur ingesamt?",
			"Wie aufwändig fandest du die Klausurvorbereitung?",
			"Wie fair war die Klausur gestellt?",
			"Wie groß war der Zeitdruck während der Klausur?",
		);
		
		$examItems2 = array(
			"Ging es eher um die Reproduktion von Auswendigelerntem oder den Transfer von Wissen?",
			"Handelte es sich eher um quantitative oder um qualitative Aufgaben?",
		);
		$examItems2Labels = array(
			array("Reproduktion", "Transfer"),
			array("Quantitativ", "Qualitativ")
		);
		
		$generalItems = array(
			"Wie bewertest du die Veranstaltung ingesamt?",
		);
		
		echo $twig->render('bewerten.template.html', 
							array(	'subject' => $subject,
									'form_target' => 'rating_submit.php',
									'button_text' => 'Bewertung abschicken',
									'isChecked' => $crit,
									'lectureItems' => $lectureItems,
									'examItems' => $examItems,
									'examItems2' => $examItems2,
									'examItems2Labels' => $examItems2Labels,
									'generalItems' => $generalItems,
								));
	}
}else{
	exit;
}	
	
	/*$statement1 = $con->prepare("SELECT * FROM ratings WHERE ID = ?");
	$statement1->bind_param('s', $kommentarID);
	$statement1->execute();
	$users = $statement1->get_result();
	if($row = mysqli_fetch_assoc($users)){*/
		
	/*}else{
		echo "<script> alert('Ein Fehler ist aufgetreten.');</script>";
	}
	$statement1->close();*/


?>