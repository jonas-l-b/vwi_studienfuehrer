<?php

	/*Verschickt Emails*/
	use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

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
		   //new PHPMailerAutoload();
		   $this->mail = new PHPMailer();

		   $configs = ConfigService::getService()->getConfigs();
		   $this->mail->SMTPDebug = 2;			//Gibt starke Debugging Ausgaben aus - für Realease Deaktivieren (später auf 2 vgl. Homepage2016)
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
		   $this->mail->ClearAllRecipients();  
		   $this->mail->AddAddress($toEmail, $userName);
		   $this->mail->Subject = $subject;

			 $this->mail->AddEmbeddedImage(__DIR__ . '/../../pictures/logo_studi.png', 'studilogo.png', 'studilogo.png');
			 $this->mail->AddEmbeddedImage(__DIR__ . '/../../pictures/email/facebook.png', 'facebook.png', 'facebook.png');
			 $this->mail->AddEmbeddedImage(__DIR__ . '/../../pictures/email/instagram.png', 'instagram.png', 'instagram.png');

		   $htmlWithoutCSS = '
		    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>Studienführer — VWI-ESTIEM Karlsruhe</title>
					<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
				</head>
				'.'
				<body>
					<table class="outerTable" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<table class="middleTable" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
									<tr>
										<td class="contentblock">
										'."
											<p class=\"anrede\">Hallo $userName,</p>
											
											$body
											
											<p>Viele Grüße!<br>
											Dein Studienführer-Team der VWI-ESTIEM Hochschulgruppe Karlsruhe<br>
											<a href=\"mailto:studienfuehrer@vwi-karlsruhe.de\"/>studienfuehrer@vwi-karlsruhe.de</a></p>
										".'

											<span>
										</td>
									</tr>
									<tr>
										<td class="footerblock">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td width="75%" class="signature">
														<strong class="signaturetitle">VWI-ESTIEM Hochschulgruppe Karlsruhe e.V.</strong><br />
														Büro: <a href="tel:0049721375987">0721 - 37 59 87</a><br />
														<a class="geolink" href="https://www.google.com/maps/place/?q=place_id:ChIJie5q7zcGl0cR_kDs4XeChQ8">Waldhornstraße 27<br />
														76131 Karlsruhe</a><br />
														<a href="https://www.vwi-karlsruhe.de" target="_blank">www.vwi-karlsruhe.de</a>
													</td>
													<td class="socialmedia">
														<table border="0" cellpadding="0" cellspacing="0">
														 <tr>
															<td>
															 <a href="https://www.facebook.com/VWIESTIEM.KARLSRUHE">
																<img src="cid:facebook.png" alt="Facebook" width="38" height="38" style="display: block;" border="0" />
															 </a>
															</td>
															<td class="socialmediabuffer" width="20">&nbsp;</td>
															<td>
															 <a href="https://www.instagram.com/vwi.estiem_karlsruhe/">
																<img src="cid:instagram.png" alt="Instagram" width="38" height="38" style="display: block;" border="0" />
															 </a>
															</td>
														 </tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</body>
			</html>
			';
		   $cssToInlineStyles = new CssToInlineStyles();
		   $this->mail->Body = $cssToInlineStyles->convert(
				$htmlWithoutCSS,
				file_get_contents(__DIR__ . '/../../res/css/emails.css')
			);
		   if(!$this->mail->Send()){
			   return false;
		   }else{
			   return true;
		   }
	   }
	}

?>
