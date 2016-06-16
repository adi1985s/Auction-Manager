<?php
/**
 * Nazwa:	Główna klasa uruchomieniowa
 */

namespace System;

class Application {
	public $request;

	/**
	 * Domyślne ustawienia
	 * @param controller - domyślna nazwa kontrolera
	 * @param action - domyślna nazwa akcji (metody)
	 */
	public static $defaults = [
		'controller' => 'index',
		'action' => 'index'
	];

	public function __construct(){
		$this->request = $_REQUEST;

		Controller::$config = include SYSPATH.'config.php';
		View::$dir = APPPATH.VIEWS; // ustawienie katalogu bazowego dla plikow z szablonami widoku
		Model::$dsn = Controller::$config['_db']['dsn']; // konfiguracja bazy danych dla modelu
		Model::$user = Controller::$config['_db']['user'];
		Model::$password = Controller::$config['_db']['password'];
	}

	public function init($controller, $action=null){
		self::$defaults['controller'] = $controller;

		if($action) {
			self::$defaults['action'] = $action;
		}

		return $this;
	}

	public function run(){
		if (empty($_GET['controller'])) $_GET['controller'] = self::$defaults['controller'];
		if (empty($_GET['action'])) $_GET['action'] = self::$defaults['action'];

		$controller = '\\'.substr(APPPATH, 0, -1).'\\Controllers\\' . $_GET['controller'];
		if(class_exists($controller)){
			$controller = new $controller;
			
			if(method_exists($controller, $_GET['action'])){
				$action = (string)$_GET['action'];
				echo $controller->$action();
			}
		}
	}
}