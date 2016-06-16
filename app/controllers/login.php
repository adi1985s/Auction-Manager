<?php
namespace App\Controllers;
use \System\Controller as Controller;
use \System\View as View;
use \System\Request as Request;
use \App\Controllers\Auth as Auth;

class Login extends BaseController
{
	// Zablokowanie wczytywania szablonu
	// nadrzędnego "layout.phtml"
	protected $options = [
		'basetemplate' => false,
		'auth' => false
	];

	public function __construct(){
		parent::__construct();

		if(Auth::isLogged()){
			Controller::httpRedirect('index.php');
		}
	}

	public function index(){
		$view = new View('login.phtml');
		$request = new Request;

		if(isset($request->post['button'])){
			$auth = new Auth($request);
			if($auth->verify()){
				Controller::httpRedirect('index.php');
			}
		}

		return $view;
	}
}