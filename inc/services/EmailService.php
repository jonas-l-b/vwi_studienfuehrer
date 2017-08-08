<?php

	/*Verschickt Emails*/
	
	class EmailService{
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
		   $this->mail = new PHPMailer();
		   
		   $configs = ConfigService::getService()->getConfigs();
		   $this->mail->SMTPDebug = 3;			//Gibt starke Debugging Ausgaben aus - für Realease Deaktivieren (später auf 2 vgl. Homepage2016)
		   $this->mail->setLanguage('de');
		   $this->mail->IsSendmail();
           $this->mail->Host = $configs['email_host'];
           $this->mail->Port = $configs['email_port'];
           $this->mail->SMTPSecure = "ssl";
           $this->mail->SMTPAuth = true;
           $this->mail->Username = $configs['email_username'];
           $this->mail->Password = $configs['email_password'];
           $this->mail->From       = $configs['email_username'];
           $this->mail->FromName   = "Studienführer - VWI-ESTIEM-Karlsruhe e.V.";
           $this->mail->CharSet =  'UTF-8';  
		   $this->mail->isHTML(true);
	   }
	   
	   
	   /**
		* sendet Email
		*
		* Sendet eine Email an den Nutzer. Gibt ein gewisses Format vor
		*/
	   public function sendEmail($toEmail, $userName, $subject, $body){
		   $this->mail->AddAddress($toEmail, $userName);
		   $this->mail->Subject = $subject;
		   $this->mail->Body = "
			<html>
				<header>
					<style>
					
					</style>
				</header>
				<body>
					<div style=\"font-family:calibri\">
						<h2>$subject</h2>
						<p>Hallo $userName,</p>
						$body
						<p>Viel Spaß mit dem Studienführer,<br>
						Deine VWI-ESTIEM Hochschulgruppe Karlsruhe</p>
						".'
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
		   if(!$this->mail->Send()){
			   return false;
		   }else{
			   return true;
		   }
	   }
	}

?>