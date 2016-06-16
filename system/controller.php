<?php
namespace System;

/**
 * Controller.
 */
abstract class Controller
{
	/**
	 * Confiruration data.
	 * @var static
	 */
	static $config;
	
	/**
	 * 404 Not Found action.
	 * Sends HTTP header and exits.
	 */
	static function http404()
	{
		header('HTTP/1.1 404 Not Found');
		exit;
	}
	
	/**
	 * HTTP redirect action.
	 * Sends HTTP header and exits.
	 * @param string $location Absolute or relative URL
	 * @param int $status HTTP response status code
	 */
	static function httpRedirect($location, $status=302)
	{
		$location = preg_replace(array('/^([^\r\n]+)/', '/(^|\/)\.(\/|$)/', '/[^\/]*\/\.\.(\/|$)/'), array('$1', '$1', ''), $location);
		header('Location: ' . (preg_match('/^[0-9a-z.+-]+:/i', $location) ? '' : 'http://' . $_SERVER['SERVER_NAME'] . (preg_match('/^\//', $location) ? '' : rTrim(dirName($_SERVER['SCRIPT_NAME']), '/') . '/')) . $location, true, $status);
		exit;
	}
	
	function __call($name, $arguments)
	{
		self::http404();
	}
}
?>