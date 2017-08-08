<?php

	/*Verschickt Emails*/
	
	class EmailService implements Service{
		/**
		* instance
		*
		* Statische Variable, um die aktuelle (einzige!) Instanz dieser Klasse zu halten
		*
		* @var Singleton
		*/
	   protected static $_instance = null;
	   protected $mail;
		
	   /**
		* get service
		*
		* Falls die einzige Service-Instanz noch nicht existiert, erstelle sie
		* Gebe die einzige Service-Instanz dann zurück
		*
		* @return   Singleton
		*/
	   public static function getService()
	   {
		   if (null === self::$_instance)
		   {
			   self::$_instance = new self;
		   }
		   return self::$_instance;
	   }
	 
	   /**
		* clone
		*
		* Kopieren der Service-Instanz von aussen ebenfalls verbieten
		*/
	   protected function __clone() {}
	 
	   /**
		* constructor
		*
		* externe Instanzierung verbieten
		*/
	   protected function __construct() {
		   new PHPMailerAutoload();
		   $mail = new PHPMailer();
		   
		   $mail->SMTPDebug = 3;			//Gibt starke Debugging Ausgaben aus - für Realease Deaktivieren (später auf 2 vgl. Homepage2016)
		   $mail->setLanguage('de');
		   $mail->IsSendmail();
           $mail->Host = "REPLACE WITH HOST";
           $mail->Port = "PEPLACE WITH PORT";
           $mail->SMTPSecure = "ssl";
           $mail->SMTPAuth = true;
           $mail->Username = "REPLACE WITH USERNAME";
           $mail->Password = "REPLACE WiTH PASSWORD";
           $mail->From       = "REPLACE WITH EMAIL-ADDRESS";
           $mail->FromName   = "Studienführer - VWI-ESTIEM-Karlsruhe e.V.";
           $mail->CharSet =  'UTF-8';  
		   $mail->isHTML(true);
	   }
	   
	   
	   /**
		* sendet Email
		*
		* Sendet eine Email an den Nutzer. Gibt ein gewisses Format vor
		*/
	   public function sendEmail($toEmail, $userName, $subject, $body){
		   $mail->AddAddress($toEmail, $userName);
		   $mail->Subject = $subject;
		   $mail->Body = "
			<html>
				<header>
					<style>
					
					</style>
				</header>
				<body>
					<div style=\"font-family:calibri\">
						<h1>$subject</h1>
						<p>Hallo $userName,</p>
						$body
						<p>Viel Spaß mit dem Studienführer,<br>
						Deine VWI-ESTIEM Hochschulgruppe Karlsruhe</p>
						<br><br>".'
						<p style="font-size:.8em;">
							____________________________________<br />
							<strong>Studienführer VWI-ESTIEM Karlsruhe</strong><br />
							<a href="mailto:studienführer@vwi-karlsruhe.de"/>studienführer@vwi-karlsruhe</a><br />
							<br />
							<strong>VWI-ESTIEM Hochschulgruppe Karlsruhe e.V.</strong><br />
							Büro: 123456789 / Fax: 123456789 <br />
							Waldhornstraße 27<br />
							76131 Karlsruhe<br />
							<a href="https://www.vwi-karlsruhe.de" target="_blank">www.vwi-karlsruhe.de</a>
						</p>
					</div>
				</body>
			</html>
			';
		   if(!$mail->Send()){
			   return false;
		   }else{
			   return true;
		   }
	   }
	}

?>