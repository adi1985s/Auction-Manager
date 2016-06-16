<?php
spl_autoload_register(function($className)
{
	$className = ltrim(strtolower($className), '\\');
	$fileName  = '';
	$namespace = '';
	if ($lastNsPos = strrpos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	/**
	 * Rzucenie "stroną błędu" w przypadku braku pliku
	 */
	if(!file_exists($fileName)){
		header('HTTP/1.0 404 Not Found', true, 404);
		echo "<h1>404 Not Found</h1>";
		echo "The page that you have requested could not be found.<br>".PHP_EOL;
		echo "<small>".$fileName."</small><br>".PHP_EOL;
		exit();
	}

	require $fileName;
});