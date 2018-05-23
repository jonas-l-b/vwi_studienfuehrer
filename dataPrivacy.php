<!--DATA PRIVACY-->
<!--Generate Message on 1st of January-->
<?php
$sql="
	SELECT value FROM data_privacy
	WHERE item = 'delete_prof_last_sent'
";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
echo $row['value'];

if($row['value'] != date("Y")){
	echo "ungleich!!";
	$mes="
		Geltende Datenschutzgesetze verbieten, nicht mehr genutzte personenbezogene Daten weiterhin zu speichern. Darum ist es nötig, 1x jährlich zu prüfen, ob im Studienführer eingetragene Dozenten noch genutzt werden. <br>
		Um zu sehen, welche Dozenten nicht mit Veranstaltungen im Studienführer verbunden sind, klicke bitte <a href=\"admin_deleteLecturerInstituteModule.php\">hier</a> (Alternative: Admin-Bereich: \"Daten bearbeiten\" > \"Dozenten/Institute/Module löschen\").<br><br>
		Bitte lösche Dozenten nur, wenn du dir sicher bist, dass sie tatsächlich nicht mehr benötigt werden.
		<br><br>
		Diese Nachricht wurde automatisch generiert.
	";
	
	$sql="
		INSERT INTO messages (sender_id, receiver_id, message_type, area, object_id, answer_required, comment, time_stamp)
		VALUES (-1, -1, 'mistake', 'lecturer', 0, 0, '$mes', now());
	";
	
	mysqli_query($con, $sql);
	
	$sql="
		UPDATE data_privacy
		SET value = ".date("Y")."
		WHERE item = 'delete_prof_last_sent'
	";
	
	mysqli_query($con, $sql);
}
?>