<?php
namespace App\Controllers;
use \System\View as View;
use \System\Utility as Utility;
use \System\Request as Request;
use \App\Models\Users as Users;
use \App\Helpers\Password as Password;

/**
 * Kontroler zajmujący się użytkownikami
 */
class User extends BaseController
{
	private $title = 'Zarządzaj użytkownikami';
	private $request;

	public function __construct(){
		parent::__construct();

		$this->users = new Users;
		$this->request = new Request;
	}


	/**
	 * Domyślna akcja.
	 * Zarządzanie wszystkimi użytkownikami.
	 */
	public function index(){
		$this->setTitle($this->title);
		$body = new View('user/manage.phtml');
		$body->users = $this->users->allUsers();
		
		$this->view->main = $body;
		return $this->view;
	}


	/**
	 * Dodawanie nowego użytkownika
	 */
	public function add(){
		$this->setTitle("Dodaj użytkownika");
		$body = new View('user/add.phtml');

		// Domyślne komunikaty na stronie
		$body->success = false;
		$body->warning = false;
		$body->error = false;

		// Sprawdzenie czy formularz został wysłany
		if(isset($this->request->post['button'])){
			// Pola do sprawdzenia
			$post = $this->request->post;
			$fields = [
				$post['name'],
				$post['email'],
				$post['password1'],
				$post['password2'],
				$post['cash']
			];

			// Sprawdzenie czy pole istenieje
			if(Utility::exists([ $fields ]) && $post['password1'] === $post['password2'] && strlen($post['password1'])){
				$salt = Password::salt($post['password1']);
				
				$status = $this->users->addUser([
					':name' => (string) $post['name'],
					':email' => (string) $post['email'],
					':salt' => $salt,
					':password' => Password::create($post['password1'], $salt),
					':company' => (string) $post['company'],
					':cash' => (int) $post['cash']
				]);

				// Czy dodawanie powiodło się.
				if($status){
					// Komunikat o powodzeniu
					$body->success = true;
				} else {
					// Komunikat o nieoczekiwanym błędzie
					$body->error = true;
				}
			} else {
				// Komunikat o błędach w formularzu
				$body->warning = true;
			}
		}
		
		$this->view->main = $body;
		return $this->view;
	}


	/**
	 * Usuwanie użytkownika
	 */
	public function remove(){
		$this->setTitle("Usuwanie");
		$body = new View('user/remove.phtml');

		// Domyślne komunikaty na stronie
		$body->success = false;
		$body->warning = false;
		$body->error = false;

		// Identyfikator użytkownika, który ma zostać usunięty
		$id = (int) $this->request->parms['id'];
		$body->id = $id;

		// Parametr do przekazania do funkcji,
		// która sprawdza / usuwa użytkownika.
		$parm = [
			':id' => $id
		];

		// Sprawdzenie czy użytkownik istnieje
		if($this->users->isExists($parm)[0]->count){
			// Jeśli tak to usuwa
			if($this->users->removeUser($parm)){
				// Komunikat o powodzeniu
				$body->success = true;
			} else {
				// Komunikat o nieoczekiwanym błędzie
				$body->error = true;
			}
		} else {
			// Komunikat gdy nie ma takiego użytkownika
			$body->warning = true;
		}

		$this->view->main = $body;
		return $this->view;
	}
}
?>