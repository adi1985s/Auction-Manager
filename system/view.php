<?php
namespace System;

use \System\Controller as Controller;

class View
{
	/**
	 * Base directory with template files.
	 * @static string
	 */
	static $dir = '';
	
	/**
	 * Global variables passed to every template file.
	 * @static array
	 */
	static $var = array();
	
	/**
	 * Template file name.
	 * @var string
	 */
	protected $file;
	
	/**
	 * Template assigned variables.
	 * @var array
	 */
	protected $data = array();
	
	/**
	 * Constructor.
	 * @param string $file Template file name.
	 */
	function __construct($file)
	{
		$this->file = $file;
	}

	function part($file){
		if (!file_exists(self::$dir . $this->file)) return '';
		ob_start();
		require self::$dir . $file;
		return ob_get_clean();
	}
	
	function __get($name)
	{
		return array_key_exists($name, $this->data) ? $this->data[$name] : null;
	}
	
	function __set($name, $value)
	{
		$this->data[$name] = $value;
	}
	
	function __toString()
	{
		if (!file_exists(self::$dir . $this->file)) return '';
		foreach (array_merge(self::$var, $this->data) as $name => $value)
		{
			if ($name != 'this') $$name = $value;
		}
		unset($name, $value);
		$_config = Controller::$config;
		$_dir = self::$dir;
		ob_start();
		require $_dir . $this->file;
		Controller::$config = $_config;
		return ob_get_clean();
	}
}
?>