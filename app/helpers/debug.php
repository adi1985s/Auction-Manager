<?php
namespace App\Helpers;

class Debug {
	private function __construct(){
	}

	public static function show($mixed){
		echo '<pre>'.PHP_EOL;
		print_r($mixed);
		echo '</pre>'.PHP_EOL;
	}
}