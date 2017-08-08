<?php

/*
*	Service Interface erzwingt Teile der Singleton Struktur
*
*
*/

interface Service{
	protected function __clone();
	protected function __construct();
	public static function getService();
}