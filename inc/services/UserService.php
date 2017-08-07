<?php

	/*Diese Klasse wird der User-Service. 
	
	Händelt alles von Benutzergruppenmanagement bis persönliche Information*/
	
	class UserService{
		/**
		* instance
		*
		* Statische Variable, um die aktuelle (einzige!) Instanz dieser Klasse zu halten
		*
		* @var Singleton
		*/
	   protected static $_instance = null;
		
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
		   
	   }
	}

?>