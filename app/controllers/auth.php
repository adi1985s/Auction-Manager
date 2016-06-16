<?php
namespace App\Controllers;
use \System\Controller as Controller;
use \System\View as View;
use \System\Request as Request;
use \App\Helpers\Password as Password;
use \App\Models\Users as Users;

class Auth
{
	public $data; // dane użytkownika

	private $user; // model użytkowników
	private $salt; // sól użytkownika
	private $passwordInstance; // instancja klasy Password

	// dane użytkownika
	private $username;
	private $password;


	/**
	 * Inicjacja
	 * @param tablica z danymi POST
	 */
	public function __construct(Request $request){
		$this->username = $request->post['username'];
		$this->password = $request->post['password'];

		$this->passwordInstance = new Password;
		$this->user = new Users;
	}


	/**
	 * Weryfikuje użytkownika za pomocą wprowadzonych danych
	 */
	public function verify(){
		// Informacje o użytkowniku
		$data = $this->user->getUserByName([
			':name' => $this->username
		]);

		// Jeśli istnieje
		if(count($data)){
			// Porównuje hashe
			if($this->passwordInstance->verify($this->password, $data[0]->salt, $data[0]->password)){
				$this->data = $_SESSION['user'] = $data[0];
				return $data[0];
			}

			return 0;
		}

		return -1;
	}


	/**
	 * Pobiera dane o użytkowniku
	 */
	public static function getUser(){
		return isset($_SESSION['user']) ? $_SESSION['user'] : null;
	}


	/**
	 * Sprawdza czy użytkownik jest administratorem
	 */
	public static function isAdmin(){
		return isset($_SESSION['user']) ? $_SESSION['user']->level : null;
	}


	/**
	 * Sprawdza czy użytkownik jest zalogowany
	 */
	public static function isLogged(){
		return isset($_SESSION['user']);
	}


	/**
	 * Sprawdza czy użytkownik NIE jest zalogowany
	 */
	public static function notLogged(){
		return !self::isLogged();
	}


	/**
	 * Sprawdza czy użytkownik jest zalogowany
	 */
	public static function loggedAs(){
		return isset($_SESSION['user']) ? $_SESSION['user']->username : null;
	}


	/**
	 * Niszczy sesję i usuwa dane użytkownika
	 */
	public static function logout(){
		unset($_SESSION['user']);
		session_destroy();
	}
}