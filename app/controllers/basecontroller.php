<?php
/**
 * Nazwa:	BaseController
 * Opis:	Nadrzędny kontroler. Po nim dziedziczą wszystkie inne kontrolery.
 *			Stworzony aby nie powtarzać tych samych czynności (zasada DRY).
 */

namespace App\Controllers;
use \System\Controller as Controller;
use \System\View as View;
use \System\Utility as Utility;
use \App\Models\Settings as Settings;


abstract class BaseController extends Controller
{
	public $layout = 'layout.phtml';
	protected $view;
	protected $settings;

	/**
	 * Konstruktor w którzym pobierane są ustawienia z bazy,
	 * domyślny szablon, czy nawigacja ma być wyeświetlana,
	 * oraz dodanie tytułu aplikacji do listy z tytułami.
	 *
	 * @param $opts		Ustawienia użytkownika aplikacji. 
	 * @return BaseController Object
	 */
	public function __construct($opts = []){
		$this->settings = new Settings;

		// Zmiana ustawień jeśli potrzeba
		$opts = Utility::extend([
			'layout' => 'layout.phtml',
			'basetemplate' => true,
			'auth' => true,
			'navigation' => true
		], $opts);

		// Jeśli jest to domyśliny szablon i włączona wyświetlana nawigacja
		if($opts['navigation'] && $opts['basetemplate']){
			$this->view = new View($opts['layout']);
			$this->view->navigation = new View('navigation.phtml');
			$this->view->navigation->site_title = $this->settings->getSiteTitle();
		}

		array_push(Controller::$config['title'], $this->settings->getSiteTitle());
	}


	/**
	 * Wylogowanie z systemu
	 *
	 * @return null
	 */
	public function logout(){
		Auth::logout();
		Controller::httpRedirect('index.php');
	}


	/**
	 * Zmiana tytułu strony poprzez dodanie kolejnego członu
	 *
	 * @param $title nowy człon tytułu
	 * @return null
	 */
	public function setTitle($title){
		array_push(Controller::$config['title'], $title);
	}
}