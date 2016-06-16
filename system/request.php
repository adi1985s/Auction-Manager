<?php
namespace System;

/**
 * Klasa która przetwarza parametry i zapisuje je do
 * zmiennych "post", "get", "cookie" oraz "parms"
 */
class Request {
	public $post = []; // Dane przesłane metodą POST
	public $get = []; // Dane przesłane metodą GET
	public $cookie = []; // Zapisane ciasteczka
	public $parms = []; // Parametry przesłane metodą GET (bez "controller" oraz "action")

	/**
	 * Inicjalizja - zapisanie do zmiennych wszystkich danych
	 */
	public function __construct(){
		$this->get = $this->multi_clean_data($this->get, $_GET);
		$this->post = $this->multi_clean_data($this->post, $_POST);
		$this->cookie = $this->multi_clean_data($this->cookie, $_COOKIE);

		if(isset($this->get['controller'])) unset($this->get['controller']);
		if(isset($this->get['action'])) unset($this->get['action']);
		$this->parms = $this->get;
	}


	/**
	 * Oczyszczenie ze zbędnych znaków całej tablicy
	 */
	private function multi_clean_data($store, $arr){
		return Utility::extend($store, array_map(function($item){
			return $this->clean_data($item);
		}, $arr));
	}

	/**
	 * Oczyszczenie ze zbędnych znaków danych
	 */
	private function clean_data($data, $isUrlEncoded=FALSE) {
		if(gettype($data) == 'string'){
			return ($isUrlEncoded) ? strip_tags(trim(urldecode($data))) : strip_tags(trim($data));
		}
	}
}