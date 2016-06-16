<?php
namespace System;

/**
 * Przydatne funkcje
 */
class Utility {
	/**
	 * Rozszerza istniejącą już tablicę o dodatkowe elementy
	 * @param oryginalna tablica
	 * @param tablica o którą ma być rozszerzona oryginalna
	 */
	public static function extend(array $oryginal, array $additional){
		foreach($additional as $key => $value){
			$oryginal[$key] = $value;
		}

		return $oryginal;
	}


	/**
	 * Wykonuje daną funkcję dla każdego elementu tablicy.
	 * Odpowiednij JS'owej funkcji forEach.
	 *
	 * @param tablica
	 * @param funkcja lambda
	 */
	public static function each(array $array, Callable $lambda){
		return array_map($lambda, $array);
	}


	/**
	 * Sprawdza czy wszystkie elementy istnieją
	 * @param tablica do sprawdzenia
	 */
	public static function exists(array $array){
		foreach($array as $item){
			if(!isset($item) or empty($item)){
				return false;
			}
		}

		return true;
	}


	/**
	 * Wyświetla sformatowany już obiekt za pomocą print_r
	 * @param tablica do sformatowania i wyświetlenia
	 */
	public static function showArray($item){
		echo '<pre>';
		print_r($item);
		echo '</pre>';
	}


	/**
	 * Zwraca nazwę klasy.
	 * @param obiekt odnoszący się do klasy ($this)
	 */
	public static function className($object){
		return strtolower(array_reverse(explode('\\', get_class($object)))[0]);
	}
}