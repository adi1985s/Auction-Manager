<?php
namespace App\Helpers;

/**
 * Klasa Password:
 * - Tworzy i weryfikuje hasła,
 * - Generuje unikalną sól
 */
class Password {
	/**
	 * Tworzenie hasha
	 * @pass (string) hasło
	 * @salt (string) wygenerowana sól
	 *
	 * @return (string) hash
	 */
	public static function create($pass, $salt){
		return hash("sha256", $pass.$salt, false);
	}


	/**
	 * Weryfikacja hasła z solą z hashem
	 * @pass (string) hasło
	 * @salt (string) zapisana w bazie sól
	 * @hash (string) zapisany w bazie hash
	 *
	 * @return (boolean)
	 */
	public static function verify($pass, $salt, $hash){
		return (self::create($pass, $salt) === $hash);
	}


	/**
	 * Tworzy nową sól
	 * @pass (string) hasło
	 *
	 * @return (string) hash
	 */
	public static function salt($sPass){
		$b64 = base64_encode(md5(microtime() . substr($sPass, 0, 3)));
		return substr($b64, -5).substr($b64, 1, 5)."$";
	}
}