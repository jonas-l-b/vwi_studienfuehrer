<?php

	/*Diese Klasse wird der Config-Service. 
	
	Händelt alle festen Konstanten, die nicht auf Github öffentlich gemacht werden dürfen und andere Variable Attribute der Software*/
	
	class ConfigService{
		
		private $configs;
		
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
			$configs = require_once __DIR__.'/../../config.php';		//PATH TO CONFIG FILE
			$this->configs = $configs;
	   }
	   
	   public function getConfigs(){
		   return $this->configs;
	   }
	   
	   public function getConfig($key){
		   return $this->configs[$key];
	   }
	   
	}

?>