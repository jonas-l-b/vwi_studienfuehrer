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
		   $mail = new PHPMailer();
	   }
	   
	   
	   /**
		* sendet Email
		*
		* Sendet eine Email an den Nutzer. Gibt ein gewisses Format vor
		*/
	   public function sendEmail(){
		   
		   
		   if(mail($to, $subject, $message, $headers)){
			   return true;
		   }else{
			   return false;
		   }
	   }
	}

?>