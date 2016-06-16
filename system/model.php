<?php
namespace System;

/**
 * Model.
 * Requirements:
 * - PDO library.
 */
abstract class Model
{
	/**
	 * Data Source Name.
	 * @static string
	 */
	static $dsn;
	
	/**
	 * Database username.
	 * @static string
	 */
	static $user;
	
	/**
	 * Database password.
	 * @static string
	 */
	static $password;
	
	/**
	 * Database connection.
	 * Singleton.
	 * @static PDO
	 */
	protected static $db;
	
	/**
	 * DVO class name.
	 * @var string
	 */
	protected $className;
	
	/**
	 * SQL array.
	 * To implement method i.e. $this->getItems() set "getItems" key and write prepared statement SQL as value.
	 * @var array
	 */
	protected $sql = array();
	
	private $sth = array();
	
	/**
	 * Constructor.
	 */
	function __construct()
	{
		if (empty(self::$db) && !empty(self::$dsn))
		{
			self::$db = new \PDO(self::$dsn, self::$user, self::$password);
			self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
	}
	
	/**
	 * Executes database prepared statement.
	 * Normally it is not required to call this method, because it is caled internally.
	 * @param string $name Key name from $this->sql array
	 * @param array $arguments Parameters to fill in prepered statement
	 * @return array Result
	 */
	protected function execute($name, $arguments=array())
	{
		if (!array_key_exists($name, $this->sql)) throw new \Exception('Execute of undefined sql ' . $name);
		if (!array_key_exists($name, $this->sth)) $this->sth[$name] = self::$db->prepare($this->sql[$name]);
		foreach ($arguments as $key => $value)
		{
			switch (gettype($value))
			{
				case 'boolean':
					$type = \PDO::PARAM_BOOL;
					break;
				case 'integer':
					$type = \PDO::PARAM_INT;
					break;
				case 'NULL':
					$type = \PDO::PARAM_NULL;
					break;
				default:
					$type = \PDO::PARAM_STR;
			}
			$this->sth[$name]->bindValue($key, $value, $type);
		}
		$this->sth[$name]->execute();
		$result = array();
		if (preg_match('/^[^A-Z_]*SELECT[^A-Z_]/i', $this->sql[$name]))
		{
			while (($object = $this->className ? $this->sth[$name]->fetchObject($this->className) : $this->sth[$name]->fetchObject())) $result[] = $object;
		}
		else
		{
			$object = (object)array('count' => $this->sth[$name]->rowCount());
			if (preg_match('/^[^A-Z_]*(INSERT|REPLACE)[^A-Z_]/i', $this->sql[$name])) $object->id = self::$db->lastInsertId();
			$result[] = $object;
		}
		return $result;
	}
	
	function __call($name, $arguments)
	{
		if (!array_key_exists($name, $this->sql)) throw new \Exception('Call to undefined method ' . get_class($this) . '::' . $name . '()');
		return $this->execute($name, array_key_exists(0, $arguments) ? $arguments[0] : array());
	}
}
?>