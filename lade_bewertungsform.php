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
		$sql="
			SELECT * FROM ratings
			WHERE ratings.user_ID = '$userID' AND ratings.subject_ID = (SELECT ID FROM subjects WHERE subjects.code = '$subject');
		";
		$result = mysqli_query($con,$sql);
		$ratingData = mysqli_fetch_assoc($result);
		var_dump($subject);
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
		echo $twig->render('bewerten.template.html', 
							array(	'subject' => $subject,
									'form_target' => 'rating_submit.php',
									'button_text' => 'Bewertung abschicken',
									'isChecked' => $crit,
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