<?php
/**
 * Nazwa:	Index
 * Opis:	Domyślny kontroler
 */

namespace App\Controllers;
use \System\View as View;
use \App\Helpers\Debug as Debug;
use \App\Models\Allegro as Allegro;
use \App\Models\Goods as Goods;

class Goods extends BaseController
{
	private $title = 'Towary i usługi';
	private $request;


	/**
	 * Inicjalizacja
	 */
	public function __construct(){
		parent::__construct();
	}


	/**
	 * Domyślna akcja
	 */
	public function index(){
		$this->setTitle($this->title);
		$settings = $this->settings;
		$body = new View('dashboard/goods.phtml');

		// Dodanie produktów z bazy do widoku
		$goods = new Goods;
		$body->goods = $goods->getAll();

		// Załadowanie ustawienień do ciała szablonu
		$body->settings = $settings->getSettings();
		$body->title = $this->title;

		// Dodanie ciała do szablonu nadrzędnego
		$this->view->main = $body;
		return $this->view;
	}
}
?>