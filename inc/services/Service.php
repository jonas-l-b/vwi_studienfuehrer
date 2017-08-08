<?php

/*
*	Abstrakte Service Klasse erzwingt Teile der Singleton Struktur
*
*
*/

abstract class Service{
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
	   protected function __clone() {};
}