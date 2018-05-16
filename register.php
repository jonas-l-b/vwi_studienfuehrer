<?php
session_start();
if (isset($_SESSION['userSession'])!="") {
	header("Location: home.php");
}
require_once 'connect.php';

$msg1 = "";
$success = false;

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);

    return $length === 0 ||
    (substr($haystack, -$length) === $needle);
}

if(isset($_POST['btn-signup'])) {
	$firstName = strip_tags($_POST['first_name']);
	$lastName = strip_tags($_POST['last_name']);
	$username = strip_tags($_POST['username']);
	$email = strip_tags($_POST['email']);
	if(!startsWith($email,'u') || !endsWith($email,'student.kit.edu')){
		exit;
	}
	$upass = strip_tags($_POST['password']);
	$degree = strip_tags($_POST['degree']);
	$advance = strip_tags($_POST['advance']);
	$semester = strip_tags($_POST['semester']);
	if(isset($_POST['info'])){
		$info = strip_tags($_POST['info']);
	}else{
		$info = "no";
	}

	$firstName = $con->real_escape_string($firstName);
	$lastName = $con->real_escape_string($lastName);
	$username = $con->real_escape_string($username);
	$email = $con->real_escape_string($email);
	$upass = $con->real_escape_string($upass);
	$degree = $con->real_escape_string($degree);
	$advance = $con->real_escape_string($advance);
	$semester = $con->real_escape_string($semester);
	$info = $con->real_escape_string($info);
	$hash = md5(rand(0,1000));

	$hashed_password = password_hash($upass, PASSWORD_DEFAULT); // this function works only in PHP 5.5 or latest version

	$check_email = $con->query("SELECT email FROM users WHERE email='$email'");
	$count=$check_email->num_rows;

	$check_username = $con->query("SELECT username FROM users WHERE username='$username'");
	$count2=$check_username->num_rows;

	if ($count==0 && $count2==0 && strtolower($username) != strtolower(explode("@", $email, 2)[0])) {
		$query = "INSERT INTO users(admin,first_name,last_name,username,email,password,active,degree,advance,semester,info,hash) VALUES(0,'$firstName','$lastName','$username','$email','$hashed_password',0,'$degree','$advance','$semester','$info','$hash')";
		if ($con->query($query)) {
			$subject = 'Aktivierung deines Studienführer-Accounts'; // Give the email a subject
			$message="
			<p>vielen Dank für deine Registrierung!</p>
			<p>Dein Account wurde erstellt. Um ihn zu aktivieren, klicke bitte auf diesen Link:<br>
			<a href=\"http://studienführer.vwi-karlsruhe.de/verify.php?email=".$email."&hash=".$hash."\">http://studienführer.vwi-karlsruhe.de/verify.php?email=".$email."&hash=".$hash."</a></p>
			";
			$mailService = EmailService::getService();
			if($mailService->sendEmail($email, $firstName, $subject, $message)){
				$success = true;
			}

			$msg = "<div class='alert alert-success'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Erfolgreich registiert! Wir haben einen Aktivierungslink an die angegebene E-Mail-Adresse gesendet.<br>
			Überprüfe auch deinen <strong>SPAM-Ordner</strong> und füge <strong>noreply@studienführer.vwi-karlsruhe.de</strong> zu deinen Ausnahmen hinzu!
			</div>";
		}else {
			$msg = "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Beim Registieren ist ein Fehler aufgetreten! Bitte wende dich an VWI-ESTIEM Karlsruhe.
			</div>";
		}
	}else {
		if(strtolower($username) == strtolower(explode("@", $email, 2)[0])){
			$msg1 .= "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Verwende nicht dein U-Kürzel als deinen Nutzernamen!
			</div>";
			$memorey_firstName = $firstName;
			$memorey_lastName = $lastName;
			//$memorey_username = $username;
			$memorey_email = $email;
			$memorey_degree = $degree;
			$memorey_advance = $advance;
			$memorey_semester = $semester;
			$memorey_info = $info;

			$highlight_username = "style=\"background-color:rgb(242, 222, 222)\"";
			//$highlight_email = "style=\"background-color:rgb(242, 222, 222)\"";
			$hightlight_upass = "style=\"background-color:rgb(242, 222, 222)\"";
		}
		if($count>0 AND $count2==0){
			$msg = "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Diese E-Mail-Adresse wird bereits verwendet! Bitte korrigiere die hervorgehobenen Felder - das Passwort muss aus Sicherheitsgründen erneut eingegeben werden.
			</div>";

			$memorey_firstName = $firstName;
			$memorey_lastName = $lastName;
			$memorey_username = $username;
			//$memorey_email = $email;
			$memorey_degree = $degree;
			$memorey_advance = $advance;
			$memorey_semester = $semester;
			$memorey_info = $info;

			//$highlight_username = "style=\"background-color:rgb(242, 222, 222)\"";
			$highlight_email = "style=\"background-color:rgb(242, 222, 222)\"";
			$hightlight_upass = "style=\"background-color:rgb(242, 222, 222)\"";

		}
		if($count2>0 AND $count==0){
			$msg = "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Dieser Benutzername wird bereits verwendet! Bitte korrigiere die hervorgehobenen Felder - das Passwort muss aus Sicherheitsgründen erneut eingegeben werden.
			</div>";

			$memorey_firstName = $firstName;
			$memorey_lastName = $lastName;
			//$memorey_username = $username;
			$memorey_email = $email;
			$memorey_degree = $degree;
			$memorey_advance = $advance;
			$memorey_semester = $semester;
			$memorey_info = $info;

			$highlight_username = "style=\"background-color:rgb(242, 222, 222)\"";
			//$highlight_email = "style=\"background-color:rgb(242, 222, 222)\"";
			$hightlight_upass = "style=\"background-color:rgb(242, 222, 222)\"";
		}
		if($count2>0 AND $count>0){
			$msg = "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Dieser Benutzername und diese E-Mail-Adresse werden bereits verwendet! Bitte korrigiere die hervorgehobenen Felder - das Passwort muss aus Sicherheitsgründen erneut eingegeben werden.
			</div>";

			$memorey_firstName = $firstName;
			$memorey_lastName = $lastName;
			//$memorey_username = $username;
			//$memorey_email = $email;
			$memorey_degree = $degree;
			$memorey_advance = $advance;
			$memorey_semester = $semester;
			$memorey_info = $info;

			$highlight_username = "style=\"background-color:rgb(242, 222, 222)\"";
			$highlight_email = "style=\"background-color:rgb(242, 222, 222)\"";
			$hightlight_upass = "style=\"background-color:rgb(242, 222, 222)\"";
		}
	}
	$con->close();
}
?>
<?php

include "header.php";

?>
<body>
<div class="container">
	<h1>Willkommen beim Studienführer!</h1>
	<p>Um dich zu registieren, musst du lediglich die Felder unten ausfüllen und auf den Button klicken. Sofern nicht explizit von dir erlaubt, werden wir deine Daten lediglich für den Studienführer nutzen.</p>
	<p style="font-weight:bold">Der Studienführer ist und bleibt kostenlos.</p>
	<br>
	<div class="alert alert-warning" role="alert">
		Die Registrierung mit Safari scheint nicht zu funktionieren. Bitte einen anderen Browser verwenden, bis wir das Problem behoben haben!
	</div>
</div>

<div class="signin-form">
	<div class="container">
		<form class="form-signin" method="post" id="register-form" action="register.php">
			<h3 class="form-signin-heading">Hier registrieren:</h3><hr />

			<?php


			if (isset($msg) && $success){
				echo $msg;
				echo '<a href="login.php" class="btn btn-default" style="float:center;">Zum Login</a>';
			}
			if (!isset($msg)||!$success):

			if (isset($msg) && !$success) {
			  echo $msg . $msg1;
			}
		?>

			<div class="form-group has-feedback">
				<input value="<?php if(isset($memorey_firstName)) echo $memorey_firstName ?>" type="text" class="form-control" placeholder="Vorname" name="first_name" id="bad1" data-error="Gib deinen Vornamen ein." required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>

			<div class="form-group has-feedback">
				<input value="<?php if(isset($memorey_lastName)) echo $memorey_lastName ?>" type="text" class="form-control" placeholder="Nachname" name="last_name" id="bad2" data-error="Gib deinen Nachnamen ein." required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>

			<div class="form-group has-feedback <?php if(isset($highlight_username)) echo 'has-error' ?>">
				<input value="<?php if(isset($memorey_username)) echo $memorey_username ?>"
					type="text" pattern="^[a-zA-Z0-9_äöüÄÖÜßẞ][a-zA-Z0-9_äöüÄÖÜßẞ][a-zA-Z0-9_äöüÄÖÜßẞ][a-zA-Z0-9_äöüÄÖÜßẞ][a-zA-Z0-9_äöüÄÖÜßẞ]+$"
					maxlength="30" class="form-control" placeholder="Benutzername" name="username" aria-describedby="helpBlock" data-username="username" data-username-error="Der Benutzername ist leider schon vergeben."
					data-error="Dein Benutzername muss zwischen 5 und 30 Zeichen lang sein. Erlaubt sind Ziffern 0-9 und Buchstaben a-Z, Umlaute und das kleine und (jetzt auch) das große ẞ." id="bad3" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block">Benutze <strong>nicht</strong> dein U-Kürzel.
					<a href="#" data-trigger="focus" data-toggle="popoverUKUERZEL" title="Benutze kein U-Kürzel als Nutzernamen." data-content="U-Kürzel sind (entgegen der häufigen Annahme) nicht anonym. Zum Beispiel kann in ILIAS jeder Administrator einer Gruppe mit den geeigneten Rechten ein U-Kürzel einem Namen und einer Matrikelnummer zuordnen. Wir möchten, dass du den Studienführer pseudonymisiert nutzen kannst, wähle daher einen Nutzernamen, indem dein U-Kürzel am besten nicht vorkommt. Andere nutzer können deinen Nutzernamen sehen!">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
				</div>
				<div class="help-block"></div>
				<div class="help-block with-errors"></div>
			</div>

			<div class="form-group has-feedback <?php if(isset($highlight_email)) echo 'has-error' ?>">
				<input value="<?php if(isset($memorey_email)) echo $memorey_email ?>" type="email" pattern="^u[a-z][a-z][a-z][a-z]@student.kit.edu$" class="form-control" placeholder="E-Mail-Adresse" name="email" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block">Gib eine deine U-Kürzel E-Mail-Adresse ein. Zum Beispiel: uxxxx@student.kit.edu
					<a href="#" data-toggle="popoverEMAIL" data-trigger="focus" title="Benutze deine Studierendenemailadresse." data-content="Der Studienführer soll nur für Studierende des KIT zur Verfügung stehen. Wir können diesen Status am leichtesten über deine u-Email-Adresse verifizieren.">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
				</div>
				<div class="help-block with-errors"></div>
			</div>

			<div class="form-group has-feedback <?php if(isset($hightlight_upass)) echo 'has-error' ?>">
				<input id="userpassword" type="password" class="form-control" placeholder="Passwort" name="password" data-pw="pw" data-pw-error="Deine Passwortstärke muss mindestens 'Mittelmäßig' sein!" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>

			<div class="progress PWprogress">
				<div id="StrengthProgressBar" class="progress-bar"></div>
			</div>

			<input type="button" class="btn btn-info" value="Mehr herausfinden" data-toggle="collapse" data-target="#demo"></input>


			<div id="demo" class="collapse">
				<br />
				<p>Deine Passworteingabe ist: <b><span id="userpasswordinputforshow"><i>Nichts eingegeben.</i></span></b></p>
				<p>Dieses Passwort kann man vermutlich mit einem sehr schnellen Server in folgender Zeit knacken: <b><span id="knackzeit">0 Sekunden</span></b> </p>
				<p>Dein Passwort konnten wir wie folgt zerlegen:</p>
				<div id="zerlegung" style="margin-left:100px;"></div>
			</div>

			<br /> <br />

			<div class="form-group has-feedback <?php if(isset($hightlight_upass)) echo 'has-error' ?>">
				<input id="userpassword2" type="password" class="form-control" placeholder="Passwort erneut eingeben" data-match="#userpassword" name="password2" required data-error="Die Eingaben stimmen nicht überein." />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>

<br />

			<div class="form-group has-feedback">
				<div class="ui dropdown">
				  <input data-error="Gib deinen Studiengang ein!" required class="form-control" type="hidden" name="degree">
				  <i class="dropdown icon"></i>
				  <div class="default text">Studiengang</div>
				  <div class="menu">
				    <div class="item" data-value="Wirtschaftsingenieurwesen">Wirtschaftsingenieurwesen</div>
				    <div class="item" data-value="Technische Volkswirtschaftslehre">Technische Volkswirtschaftslehre</div>
						<div class="item" data-value="Informationswirtschaft">Informationswirtschaft</div>
						<div class="item" data-value="Wirtschaftsmathematik">Wirtschaftsmathematik</div>
						<div class="item" data-value="Sonstige">Sonstige</div>
				  </div>
				</div>
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>

			<script>
			$('.ui.dropdown')
				.dropdown(
					<?php if(isset($memorey_degree)) echo "'set selected', '$memorey_degree'" ?>
				)
				;
			</script>

<br />

			<div class="form-group has-feedback">
				<select class="form-control" name="advance" required style="-moz-appearance: none;-webkit-appearance: none;appearance: none;">
					<option value="bachelor">Bachelor</option>
					<option value="master" <?php if(isset($memorey_advance))if($memorey_advance == "master") echo "selected" ?> >Master</option>
				</select>
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
			</div>

			<div class="form-group has-feedback">
				<input value="<?php if(isset($memorey_semester)) echo $memorey_semester ?>" type="number" max="18" min="1" step="1" class="form-control" placeholder="Fachsemester" name="semester" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>

			<div class="checkbox has-feedback">
				<label><input type="checkbox" name="info" value="yes" <?php if(isset($memorey_info))if($memorey_info == "yes") echo "checked" ?> >Ich möchte über speziell für mich interessante Events informiert werden. Das können beispielsweise Einladungen zu (kostenlosen) Events wie Workshops, Vorträgen oder Fallstudien sein, die die Hochschulgruppe VWI-ESTIEM Karlsruhe zusammen mit Unternehmen veranstaltet.</label>
			</div>

			<div class="checkbox has-feedback">
				<label><input type="checkbox" name="nutzungsbedingungen" id="bedingungen"
				value="yes">Hiermit bestätigst du, dass du unsere <a href="#" data-toggle="modal" data-target="#bedingungenModal">Datenschutzerklärung, Nutzungsbedingungen und Gemeinschaftsstandards</a> gelesen hast und diese akzeptierst.</label>
			</div>

			<hr>
			<?php /*Hier wäre es sinnvoll noch ein ReCAPTCHA von Google einzubauen */ ?>
			<div class="form-group">
				<button id="submitbutton" class="btn btn-primary" name="btn-signup">
					<span class="glyphicon glyphicon-log-in"></span> &nbsp; Account erstellen
				</button>
				<a href="login.php" class="btn btn-default" style="float:right;">Zum Login</a>
			</div>
		</form>
		<?php
			endif;
		?>
    </div>
</div>

<!-- Bedingungen Modal -->
<div id="bedingungenModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Datenschutzerklärung, Nutzungsbedingungen und Gemeinschaftsstandards</h4>
      </div>
      <div class="modal-body">
				<h4>Datenschutzerklärung</h4>
				<p><strong>Allgemeine Datenschutzerklärung</strong></p>
				<p>Durch die Nutzung unserer Website erklären Sie sich mit der Erhebung, Verarbeitung und Nutzung von Daten gemäß der nachfolgenden Beschreibung einverstanden. Der Studienführer des VWI-ESTIEM Hochschulgruppe Karlsruhe e.V. kann grundsätzlich nicht ohne Registrierung besucht werden. Personenbezogene Daten, insbesondere Name und E-Mail-Adresse, aber auch andere personenbezogene Daten, werden bei der Registrierung von uns erhoben. Die Registrierung auf dieser Seite erfolgt auf freiwilliger Basis durch Sie. Ohne Ihre ausdrückliche Einwilligung erfolgt keine Weitergabe der Daten an Dritte. Der VWI-ESTIEM Hochschulgruppe Karlsruhe e.V. verwendet Ihre Daten ausschließlich für den Studienführer und die Bereitstellung eines Newsletters. (Dazu mehr unter "Newsletter".)</p>
				<p><strong>Datenschutzerklärung für Cookies</strong></p>
				<p>Unsere Website verwendet Cookies. Das sind kleine Textdateien, die es möglich machen, auf dem Endgerät des Nutzers spezifische, auf den Nutzer bezogene Informationen zu speichern, während er die Website nutzt. Cookies ermöglichen es, insbesondere Nutzungshäufigkeit und Nutzeranzahl der Seiten zu ermitteln, Verhaltensweisen der Seitennutzung zu analysieren, aber auch unser Angebot kundenfreundlicher zu gestalten. Cookies bleiben über das Ende einer Browser-Sitzung gespeichert und können bei einem erneuten Seitenbesuch wieder aufgerufen werden. Wenn Sie das nicht wünschen, sollten Sie Ihren Internetbrowser so einstellen, dass er die Annahme von Cookies verweigert. Dies kann unter Umständen negative Auswirkungen auf den Nutzungskomfort unserer Webseite haben, da diese die Nutzung von Cookies für die technische Umsetzung vorrausgesetzt hat.</p>
				<p><strong>Datenschutzerklärung für Google Analytics</strong></p>
				<p>Unsere Website verwendet Google Analytics, einen Webanalysedienst von Google Inc., 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA. Zur Deaktivierung von Google Analytiscs stellt Google unter http://tools.google.com/dlpage/gaoptout?hl=de ein Browser-Plug-In zur Verfügung. Google Analytics verwendet Cookies. Das sind kleine Textdateien, die es möglich machen, auf dem Endgerät des Nutzers spezifische, auf den Nutzer bezogene Informationen zu speichern. Diese ermöglichen eine Analyse der Nutzung unseres Websiteangebotes durch Google. Die durch den Cookie erfassten Informationen über die Nutzung unserer Seiten (einschließlich Ihrer IP-Adresse) werden in der Regel an einen Server von Google in den USA übertragen und dort gespeichert. Wir weisen darauf hin, dass auf dieser Website Google Analytics um den Code „gat._anonymizeIp();“ erweitert wurde, um eine anonymisierte Erfassung von IP-Adressen (sog. IP-Masking) zu gewährleisten. Ist die Anonymisierung aktiv, kürzt Google IP-Adressen innerhalb von Mitgliedstaaten der Europäischen Union oder in anderen Vertragsstaaten des Abkommens über den Europäischen Wirtschaftsraum, weswegen keine Rückschlüsse auf Ihre Identität möglich sind. Nur in Ausnahmefällen wird die volle IP-Adresse an einen Server von Google in den USA übertragen und dort gekürzt. Google beachtet die Datenschutzbestimmungen des „Privacy Shield“-Abkommens und ist beim „Privacy Shield“-Programm des US-Handelsministeriums registriert und nutzt die gesammelten Informationen, um die Nutzung unserer Websites auszuwerten, Berichte für uns diesbezüglich zu verfassen und andere diesbezügliche Dienstleistungen an uns zu erbringen. Mehr erfahren Sie unter<a href="http://www.google.com/intl/de/analytics/privacyoverview.html"> http://www.google.com/intl/de/analytics/privacyoverview.html</a>.</p>

				<p>Nach einer Vorlage <a href="https://www.anwalt.de/vorlage/muster-datenschutzerklaerung.php">von anwalt.de</a> mit Ergänzungen des VWI-ESTIEM Hochschulgruppe Karlsruhe e.V.</p>

        <p>&nbsp;</p><h4>Besondere Nutzungsbedingungen für die Registrierung im Studienführer des VWI-ESTIEM Hochschulgruppe Karlsruhe e.V.</h4><p>&nbsp;</p><p><strong>§ 1 Geltungsbereich</strong></p><p>(1)&nbsp;Die nachfolgenden Bedingungen gelten für die Nutzung des Studienführer des VWI-ESTIEM Hochschulgruppe Karlsruhe e.V. - im folgenden "Unsere Webseite" genannt - Forums.&nbsp;Für die Nutzung des Forums ist wichtig, dass Sie als Nutzer/Nutzerin die nachfolgenden Forenregeln bzw. –bedingungen akzeptieren. Die Registrierung und Benutzung unseres Forums ist kostenlos.</p><p>(2)&nbsp;Mit der Registrierung sind Sie mit den Nutzungsbedingungen unserer Webseite einverstanden. Durch Ihr Einverständnis garantieren Sie uns, dass Sie keine Beiträge erstellen werden, die gegen die Nutzungsbedingung verstoßen.</p><p>(3) Durch das Benutzen von unserer Webseite kommt kein Vertrag zwischen den Nutzern/Nutzerinnen und uns zustande.</p><p><strong>&nbsp;</strong></p><p><strong>§ 2 Pflichten als Forum-Nutzer/Forum-Nutzerinnen</strong></p><p><sup>&nbsp;</sup>(1) Einer Ihrer Pflichten als Nutzer/Nutzerin ist es, dass Sie keine Beiträge veröffentlichen, die gegen diese Forenregeln, gegen die guten Sitten oder sonst gegen geltendes deutsches Recht verstoßen.</p><p><span style="text-decoration: underline;">Folgende Punkte sind Ihnen nicht genehmigt:</span></p><p>1. Inhalte zu veröffentlichen, die unwahr sind und deren Veröffentlichung einen Straftatbestand oder eine Ordnungswidrigkeit erfüllt,<br />2. Versendung von Spam über das Forum an andere Forum-Nutzer/Form-Nutzerin,<br />3. Verwendung von gesetzlich durch Urheber- und Markenrecht geschützte Inhalte ohne rechtmäßige Berechtigung (z.B. Pressemitteilungen etc.),<br />4. Handlungen, die wettbewerbswidrig sind,<br />5. mehrfache Einstellung von Themen ins Forum (sogenannte Doppelpostings),<br />6. eigene Werbung, folglich Schleichwerbung, zu betreiben und<br />7. Inhalte zu veröffentlichen, die beleidigend, rassistisch, diskriminierend oder pornographische Elemente aufweisen gegenüber anderen Nutzern/Nutzerinnen und der Allgemeinheit.</p><p>Ihre Pflicht als Forum-Nutzer/ Forum-Nutzerin ist es, auf § 2 Abs. 1 Nr. 1-7 dieser Nutzungsbedingung, vor&nbsp; der Veröffentlichung Ihres Beitrages im Forum zu beachten und zu überprüfen, ob Sie sich an diese Punkte gehalten haben.</p><p>(2)&nbsp;&nbsp;&nbsp;Sollten Sie gegen § 2 Abs. 1 Nr. 1-7 dieser Nutzungsbedingung verstoßen, behalten wir uns das Recht vor, gegen Sie folgende Schritte vorzunehmen:</p><p>1. Ihre eingestellten Beiträge zu löschen und diese abzuändern,</p><p>2. Verbot weiterhin im Forum Beiträge zu verfassen und</p><p>3. Sperrung des Zugangs als Nutzer/Nutzerin.</p><p>(3)&nbsp;&nbsp;Haben Sie als Forum-Nutzer/Forum-Nutzerin nicht die Forenregeln beachtet und sind hierdurch mögliche Rechtsverstöße entstanden, die durch Ihre eingestellten Inhalte in unserem Forum entstanden sind (Pflichtverletzung), verpflichten Sie sich als Nutzer/Nutzerin, <em>uns </em>von jeglichen Ansprüchen, auch den Schadenersatzansprüchen, freizustellen und diesen die Kosten zu ersetzen.</p><p>Zudem ist der Nutzer/die Nutzerin verpflichtet uns, bei Schadenersatzansprüchen hinsichtlich der Abwehr des durch ihn entstandenen Rechtsverstoßes (Pflichtverletzung s.o.), zu unterstützen und die Kosten einer angemessenen Rechtsverteidigung für uns zu tragen.</p><p>(4)&nbsp;&nbsp; Durch Ihr Einverständnis garantieren Sie uns, dass Sie keine Beträge erstellen werden, die gegen die Nutzungsbedingung verstoßen. Entsprechendes gilt auch für das Setzen von externen Links und die Signaturen.</p><p>&nbsp;</p><p><strong>§ 3 Übertragung von Nutzungsrechten</strong></p><p>Sie, als Forum-Nutzer/Forum-Nutzerin&nbsp; haben die alleinige Verantwortung des Urheberrechts i.S.d. Urhebergesetzes bei Veröffentlichung von Beiträgen und Themen im Forum zu beachten. <sup>2</sup>Als Nutzer/Nutzerin räumen Sie lediglich uns mit Veröffentlichung Ihres Beitrages auf deren Homepage das Recht ein, den Beitrag dauerhaft zum Abruf bereitzustellen. <sup>3</sup>Ferner hat unser Team das Recht, Ihre Themen und Beiträge zu löschen, zu bearbeiten und innerhalb seiner Homepage zu verschieben, um diese mit anderen Inhalten zu verknüpfen oder zu schließen.</p><p>&nbsp;</p><p><strong>§ 4 Haftungsbeschränkung</strong></p><p>(1) Wir übernimmen keinerlei Gewähr für die im Forum veröffentlichten und eingestellten Beiträge, Themen, externen Links und die daraus resultierenden Inhalte, insbesondere nicht für deren Richtigkeit, Vollständigkeit und Aktualität. Wir sind auch nicht verpflichtet, permanent die übermittelten und gespeicherten Beiträge der Nutzer/Nutzerinnen zu überwachen oder nach den Umständen zu erforschen, ob sie auf einen rechtswidrigen Inhalt hinweisen. Wir haften grundsätzlich nur im Falle einer vorsätzlichen oder grob fahrlässigen Pflichtverletzung.</p><p>(2) WIr weisen ausdrücklich darauf hin, dass die juristischen Beiträge und Diskussionen im Forum vollkommen unverbindlich sind. <sup>2</sup>Die Nutzung der Beiträge und deren Verwertung erfolgt auf Ihre eigene Gefahr.</p><p>(3)&nbsp;&nbsp; Bei Werbeschaltungen übernimmen wir keine Haftung für den Inhalt und die Richtigkeit. <sup>2 </sup>Für den Inhalt der Werbeanzeigen ist der jeweilige Autor einzig und allein verantwortlich; gleiches gilt für den Inhalt der beworbenen Webseite. <sup>3</sup>Bei Darstellung der Werbeanzeige auf unserer Webseite , sind wir nicht gleichzeitig mit dem rechtswidrigen Inhalt einverstanden. <sup>4</sup>Daher liegt die Haftung ausschließlich bei dem Werbekunden.</p><p>(4)&nbsp;&nbsp; Es ist wird nicht für einen ständigen unterbrechungsfreien Abruf der Webseite garantiert. <sup>2</sup>Einer Haftung diesbezüglich wird hiermit ausdrücklich widersprochen. <sup>3</sup>Auch bei großer Sorgfalt können Ausfallzeiten leider nicht ausgeschlossen werden.</p><p>&nbsp;</p><p><strong>§ 5 Urheberrecht</strong></p><p>Sämtliche Texte, Bilder und andere auf unserer Webseite veröffentlichten Informationen und Daten unterliegen - sofern nicht anders gekennzeichnet - dem Copyright unserer Seite. Jede Form von Wiedergabe und/oder Modifikation darf nur mit der schriftlichen Genehmigung durch uns erfolgen. Andersfalls behalten wir uns das Recht vor gerichtlich gegen diese Rechtsverletzung vorzugehen. <sup>4</sup>Alle Kosten, die durch eine Rechtsverletzung seitens eines Benutzers verursacht werden, werden diesem in Rechnung gestellt.</p><p><strong>&nbsp;</strong></p><p><strong>§ 6 Änderungsvorbehalt</strong></p><p>Wir haben jederzeit das Recht die Nutzungsbedingungen zu ändern. Die Änderung wird dann per E-Mail-Benachrichtigung veröffentlicht.</p><p>&nbsp;</p><p><strong>§ 7 Kündigung und Laufzeit der Mitgliedschaft bei unserer Webseite <br /></strong></p><p>Die Laufzeit der Mitgliedschaft beginnt mit der Registrierung und mit dem Einverständnis unseren Nutzungsbedingungen und besteht auf eine unbestimmte Zeit. Die Mitgliedschaft kann jederzeit ohne Angabe von Gründen fristlos gekündigt werden.</p><p>&nbsp;</p><p><strong>§ 8 Salvatorische Klausel</strong></p><p>Diese Forum-Nutzungsbedingung ist als Teil unserer Webseite zu betrachten, von dem aus auf diese Seite verwiesen wird. Sind einzelne Formulierungen dieser Forum-Nutzungsbedingung nicht mehr ganz oder nicht mehr vollständig konform mit der geltenden Rechtslage, so ist davon auszugehen, dass die übrigen Regelungen der Forum-Nutzungsbedingungen bestehen bleiben.</p><p>Diese <a href="http://www.jurarat.de/muster-nutzungsbedingungen">Nutzungsbedingungen</a> wurden freundlicherweise von www.jurarat.de zur Verfügung
				<p></p>
				<br/>
				<h4>Gemeinschaftsstandards</h4>
        <p>An die folgenden Gemeinschaftsstandards hat sich der Nutzer zu halten. Ein Verstoß gegen die Gemeinschaftsstandards kann zur dauerhaften Sperrung des Kontos und Nutzers führen. Wir bitte die Gemeinschaftsstandards möglichst sorgfältig zu beachten, damit wir eine weitere Verfügbarkeit des Studienführer des VWI-ESTIEM Hochschulgruppe Karlsruhe e.V. gewährleisten können:</p>
				<ul>
					<li>Der Nutzer defamiert, beleidigt oder greift keine Person namentlich oder anderweitig an.</li>
					<li>Der Nutzer gibt seine Bewertungen im Studienführer nach bestem Wissen und Gewissen ab. Höchstes Ziel bei der Bereitstellung und dem Teilen von Wissen besteht darin, anderen Nutzern einen Mehrwert bei der Veranstaltungswahl zu schaffen.</li>
					<li>Der Nutzer ist freundlich und sachlich in seiner ausdrucksweise.</li>
					<li>Der Nutzer weißt bei Kenntlichwerden einer groben Verletzung der Gemeinschaftsstandards durch einen anderen Nutzer die Betreiber des Studienführer des VWI-ESTIEM Hochschulgruppe Karlsruhe e.V. unverzüglich über die entsprechenden Kontaktformulare darauf hin. Wir bedanken uns hierbei ausdrücklich für die Mithilfe.</li>
				</ul>
				<p>Wir freunen uns über das Interesse des Nutzers.!<p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
      </div>
    </div>

  </div>
</div>




<div id="snackbar">Du musst unsere Bedingungen akzeptieren bevor du dich registrieren kannst!</div>


<?php /*Die folgenden Skripte implementieren die Strength-Meter Bar des Password Inputs. Basis für die Berechnung der Stärke ist die zxcvbn library.
		Außerdem berechnen wir, ob das Input Form als ganzes abgeschickt werden darf.*/ ?>
<script type="text/javascript" src="res/lib/zxcvbn.js"></script>
<script type="text/javascript" src="res/lib/zxcvbn-bootstrap-strength-meter.js"></script>
<script type="text/javascript" src="res/lib/bootstrap-validator/validator.js"></script>
<script type="text/javascript">
	$(document).ready(function () {

		var userInputs = ["studienführer", "vwi", "estiem", "wiwi", "wing", "hochschulgruppe", "hsg"];
		$( '#bad1' ).blur(function() {
		  userInputs.push($('#bad1').val());
		});
		$( '#bad2' ).blur(function() {
		  userInputs.push($('#bad2').val());
		});
		$( '#bad3' ).blur(function() {
		  userInputs.push($('#bad3').val());
		});
		jQuery('body').on('keyup','input#userpassword',function(){
		  var quickanswer = zxcvbn($('#userpassword').val(), userInputs);
			console.log(quickanswer);
			$('#userpasswordinputforshow').html($('#userpassword').val());
			$('#knackzeit').html(quickanswer.crack_times_display.offline_fast_hashing_1e10_per_second);
			let zerlegungstext = '';
		  quickanswer.sequence.forEach(function (item) {
			  zerlegungstext = zerlegungstext + '<li><i>';
					zerlegungstext = zerlegungstext + item.token + '</i> ist vom Mustertyp <i><b>' + item.pattern;
					if(item.pattern === 'dictionary'){
							zerlegungstext = zerlegungstext  + '</b></i> und aus unserem Wörterbuch <i>' + item.dictionary_name + '</i>.';
					}else{
							zerlegungstext = zerlegungstext  + '</b></i>.';
					}
				zerlegungstext = zerlegungstext + ' Wir brauchten dafür <b>' + item.guesses_log10.toFixed(2) + 'e Versuche.</b></li>';
			});
			$('#zerlegung').html('<ul class="dl-horizontal"><li><ul>' + zerlegungstext + '</ul></li></ul>');

		});
		$("#StrengthProgressBar").zxcvbnProgressBar({
			  passwordInput: "#userpassword",
			  ratings: ["Weitertippen", "Immer noch recht schwach", "Mittelmäßig", "Stark!", "Unfassbar stark"],
			  userInputs: userInputs });
		$('#register-form').validator({
			custom: {
				'pw': function($el) {
					var result = zxcvbn($el.val(), userInputs);
				    if(result.score>=2){
					  return false;
				    }else{
					  return true;
				    }
				},
				'username': function($el) {
					$.ajax({
						type: "GET",
						url: "username-validation-api.php",
						dataType: "text",
						data: { username: $el.val() }
					}).done(function (res) {
						if(res==="{ok: true}"){
							$el.next().next().next().text('Der Benutzername ist leider schon vergeben.');
							$el.parent().addClass('has-error');
							return false;
						}else{
							$el.next().next().next().text('');
							$el.parent().removeClass('has-error');
							return true;
						}
					});
				}
			},
			errors: {
				pw: 'Deine Passwortstärke muss mindestens "Mittelmäßig" sein!',
				username: 'Der Benutzername ist leider schon vergeben.'
			}
		});

		$( "#submitbutton" ).click(function() {
		  if($( "#submitbutton" ).hasClass('disabled')==false){
			if($( "#bedingungen:checked").length > 0){
				$( "#register-form" ).submit();
			}else{
				var x = document.getElementById("snackbar")
				// Add the "show" class to DIV
				x.className = "show";
				// After 3 seconds, remove the show class from DIV
				setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
			}
		  }
		});


		$('[data-toggle="popoverUKUERZEL"]').popover();
		$('[data-toggle="popoverEMAIL"]').popover();
	});
</script>
</body>
</html>
