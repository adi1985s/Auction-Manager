<?php
/**
 * Nazwa:	Index
 * Opis:	Domyślny kontroler
 */

namespace App\Controllers;
use \System\View as View;
use \App\Helpers\Debug as Debug;
use \App\Models\Allegro as Allegro;

class Index extends BaseController
{
	private $title = 'Ustawienia';
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
		$body = new View('dashboard/settings.phtml');

		// Jeśli wysłano formularz z ustawieniami
		if(isset($_POST['settings_btn'])){
			unset($_POST['settings_btn']);

			foreach($_POST['settings_fields'] as $id => $value){
				$settings->editSettingsField([
					':id' => (int) $id,
					':field_value' => $value
				]);
			}
		}

		$allegro = new Allegro();

		// Załadowanie ustawienień do ciała szablonu
		$body->settings = $settings->getSettings();
		$body->title = $this->title;

		// Dodanie ciała do szablonu nadrzędnego
		$this->view->main = $body;
		return $this->view;
	}
}
?>