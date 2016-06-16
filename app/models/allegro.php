<?php
/**
 * Autor:	Piotr Filipek
 * E-mail:	piotrek290@gmail.com
 *
 * Opis:	Klasa dzięki której możliwe jest zalogowanie się do Allegro
 * 			oraz wykonywanie zdalnie metod podanych w dokumentacji na stronie Allegro.
 *
 * http://allegro.pl/webapi/documentation.php
 */

namespace App\Models;
use \App\Models\Settings as Settings;

class Allegro extends AllegroWebAPIService {

	/**
	 * Typy obsługi p
	 */
	const ALLEGRO_PRODUCTION = 'production';
	const ALLEGRO_DEVELOPMENT = 'development';

	/**
	 * Uchwyt do obiektu Allegro
	 */
	protected $client;

	/**
	 * Czas po jakim pliki cache stają się przedawnione (sekundy)
	 */
	static protected $cacheOutdatedTime = 3600; // na godzinę

	/**
	 * Wstępna konfiguracja, czyli nazwa użytkownika, hasło oraz klucz API.
	 */
	protected $config;


	/**
	 * Logowanie do Allegro za pomocą podanych w konfuguracji danych.
	 * Wszytkie dane zapisywane są do zmiennej $client
	 */
	public function __construct($type = ALLEGRO::ALLEGRO_DEVELOPMENT){
		$settings = new Settings;
		$wsdl = $settings->getField('wsdl');
		$apikey = $settings->getField('apikey');
		$user = $settings->getField('user');
		$password = $settings->getField('password');
		$countryId = 1;

		// Pobranie nazwę klasy rodzica
		$parentClass = get_parent_class($this);

		// Utworzenie obiektu rodzica i próba zalogowania się
		$this->client = new $parentClass($apikey, 1, $wsdl);
		
		// Proces logowania
		$this->client->login($user, $password);
	}


	/**
	 * Pobiera informacje o zalogowanym użytkowniku
	 * @return stdClass
	 */
	public function getUserInfo(){
		return $this->client->doGetMyData($this->session);
	}


	/**
	 * Pobiera auktualnie przez nas sprzedawane aukcje
	 *
	 * @return StdClass
	 */
	public function getMySellItems(){
		$cache = new Cache(__METHOD__);
		$latestAccess = $cache->latestAccess();

		if(!$cache->isCached() || $cache->isOutdated(self::$cacheOutdatedTime)){
			$items = $this->client->doGetMySellItems();
			$items->latestAccess = $latestAccess;
			$cache->save($items);
			return $items;
		}

		return $cache->restore();
	}


	/**
	 * Pobiera wszystkie kategorie i zapisuje w cache.
	 * Jeśli dane są już w pamięci i nie są przedawnione to je pobiera.
	 */
	public function getCatsData(){
		$cache = new Cache(__METHOD__);

		$categories = false;

		if(!$cache->isCached() || $cache->isOutdated(self::$cacheOutdatedTime)){
			$categories = $this->client->doGetCatsDataLimit([
				'offset' => 1
			])->catsList->item;

			$cache->save($categories);
			return $categories;
		}

		return $cache->restore();
	}


	/**
	 * Zmiana miniaturki na wybranych aukcjach
	 *
	 */
	public function changeItemThumbnail($list, $arguments){
		foreach($list as $id){
			$title = $arguments['title'];
			$base64 = $arguments['image_base64'];
			
			$this->client->doChangeItemFields([
				'itemId' => (int) $id,
				'fvalueImage' => $base64,
				'previewOnly' => 0,
				'fieldsToModify' => array(
					array(
						'fid' => 1,
						'fvalueString' => $title,
						'fvalueInt' => 0,
						'fvalueFloat' => 0,
						'fvalueImage' => 0,
						'fvalueDatetime' => 0,
						'fvalueDate' => '',
						'fvalueRangeInt' => array( 
								 'fvalueRangeIntMin ' => 0,
								 'fvalueRangeIntMax ' => 0),
						'fvalueRangeFloat' => array( 
								 'fvalueRangeFloatMin ' => 0,
								 'fvalueRangeFloatMax ' => 0),
						'fvalueRangeDate' => array( 
								 'fvalueRangeDateMin ' => '',
								 'fvalueRangeDateMax ' => ''
						)
					)
				)
			]);

			//echo '<pre>';
			//print_r($base64);
			//echo '</pre>';
		}
	}


	/**
	 * Tworzy nową aukcję na podstawie przekazanych parametrów przez zmienną tablicową
	 *
	 * @param array $args
	 * @return mixed
	 */
	public function createAuction($args){
		echo "Tworzenie aukcji...<br>";
		/*$this->client->doNewAuctionExt([
			'fields' => [
				'fid' => 1,
				'fvalueString' => 'Testowa aukcja'
			]
		]);*/

		return $this->client->doGetSellFormFieldsExt();

		return true;
	}


	/**
	 * Sprawdza czy tytuł przechodzi test poprawności.
	 * Domyślnie na Allegro ustawiny jest limit na 50 znaków.
	 *
	 * @param string $title - tytuł aukcji
	 * @param integer $limit - limit znaków w tytule
	 *
	 * @return boolean
	 */
	public function isTitleValid($title, $limit = 50){
		$len = strlen($title);
		$chars = [
			'"' => 6,
			'<' => 4,
			'>' => 4,
			'&' => 5
		];

		foreach (count_chars($title, 1) as $i => $val) {
			if(array_key_exists(chr($i), $chars)){
				$len = ($len-1) + ($val*$chars[chr($i)]);
			}
		}

		return $len > $limit ? false : true;
	}


	/**
	 * Kończy aukcje po ich numerach
	 *
	 * @param mixed [$arg1, $arg2, ...]
	 * @return null
	 */
	public function fishishItems(){
		$args = func_get_args();
		$num = func_num_args();

		print_r($this->client->getUserInfo());
		return;

		// Jeśli nie podano żadnego parametru
		if(!$num){
			echo "Nie wybrano żadnej aukcji do zakończenia.";
			return;
		}

		// Jeśli do zakończenia jest tylko jedna aukcja
		if($num == 1){
			$this->client->doFinishItem($this->session, [
				'finishItemId' => $args[0]
			]);

			echo "Aukcja została usunięta.<br>";
			return;
		}

		// Jeśli do zakończenia jest lista aukcji
		for($i=0; $i<$num; ++$i){
			$this->client->doFinishItem($this->session, $args[$i]);

			echo "Aukcja {$args[$i]} została zakończona.<br>";
		}
	}


	/**
	 * Ustawienie domyślnego czasu odświeżania pamięci cache
	 *
	 * @param integer $time
	 * @return null
	 */
	public static function setCacheOutdatedTime($time){
		self::$cacheOutdatedTime = $time;
	}


	/**
	 * Siłowie odświeżenie pamięci cache zmieniając czas do kolejnego zapisu na "0",
	 * co powoduje natychmiastowe zapisanie do pliku.
	 *
	 * @return null
	 */
	public static function forceRefreshCache(){
		self::setCacheOutdatedTime(0);
	}
}
?>