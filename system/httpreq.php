<?php
namespace System;

class HttpReq {
	
	public $param; // the returned POST/GET values
	public $cookie; // the returned COOKIE values
	public $session; // the returned SESSION values
	private $strength; // the strength of sanitization
	
	/**
	 * Class constructor takes one argument to set the strength of sanitization
	 * @param string $strength values can be 'normal', 'strong', or 'strict'
	 */
	public function __construct($strength='normal'){
		$this->param = array();
		$this->cookie = array();
		$this->session = array();
		$this->strength = $strength;
	}
	
	
	/**
	 * Method to set, clean &/or sanitize a $_GET value if set
	 * @param string $name the name of the value sought
	 * @param boolean $urlDecode set to TRUE if the method should urldecode the value
	 * @param boolean $san set to TRUE if the method should sanitize the value against XSS vulnerabilities
	 * @return array
	 */
	public function get($name='', $urlDecode=FALSE, $san=FALSE) {
		
		if (isset($_GET[$name])) 
		{
			if ($urlDecode && $san) {
				$this->param[$name] = $this->clean_data(
				$this->san_data($_GET[$name]), 
				TRUE);
			}
			elseif ($urlDecode) {
				$this->param[$name] = $this->clean_data($_GET[$name], TRUE);
			}
			elseif ($san) {
				$this->param[$name] = $this->clean_data(
				$this->san_data($_GET[$name]), 
				FALSE);
			}
			else {
				$this->param[$name] = $this->clean_data($_GET[$name], FALSE);
			}
		} 
		else {
			$this->param[$name] = NULL;
		}
		return $this->param;
	}
	
	
	/**
	 * Method to set, clean &/or sanitize a $_POST value if set
	 * @param string $name the name of the value sought
	 * @param boolean $urlDecode set to TRUE if the method should urldecode the value
	 * @param boolean $san set to TRUE if the method should sanitize the value against XSS vulnerabilities
	 * @return array
	 */
	public function post($name='', $urlDecode=FALSE, $san=FALSE) {
		
		if (isset($_POST[$name])) 
		{
			if ($urlDecode && $san) {
				$this->param[$name] = $this->clean_data(
				$this->san_data($_POST[$name]), 
				TRUE);
			}
			elseif ($urlDecode) {
				$this->param[$name] = $this->clean_data($_POST[$name], TRUE);
			}
			elseif ($san) {
				$this->param[$name] = $this->clean_data(
				$this->san_data($_POST[$name]), 
				FALSE);
			}
			else {
				$this->param[$name] = $this->clean_data($_POST[$name], FALSE);
			}
		} 
		else {
			$this->param[$name] = NULL;
		}
		return $this->param;
	}
	
	
	/**
	 * Additional method to set a $_COOKIE value if set
	 * @param string $name the name of the value sought
	 */
	public function cookie($name='') {
		$this->cookie[$name] = (isset($_COOKIE[$name])) ?
			$_COOKIE[$name] : NULL;
			
		return $this->cookie;
	}
	
	
	/**
	 * Additional method to set a $_SESSION value if set
	 * @param string $name the name of the value sought
	 */
	public function session($name='') {
		$this->session[$name] = (isset($_SESSION[$name])) ?
			$_SESSION[$name] : NULL;
			
		return $this->session;
	}
	
	
	/**
	 * Private method to clean data
	 * @param mixed $data
	 * @param Boolean $isUrlEncoded
	 */
	private function clean_data($data, $isUrlEncoded=FALSE) {
		return ($isUrlEncoded) ?
		    strip_tags(trim(urldecode($data))) :
		    strip_tags(trim($data));
	}
	
	
	/**
	 * Private method to sanitize data
	 * @param mixed $data
	 */
	private function san_data($data) {
		switch($this->strength){
			default:
				return htmlspecialchars($data, ENT_QUOTES, "UTF-8");
				break;
			case 'strong':
				return htmlentities($data, ENT_QUOTES | ENT_IGNORE, "UTF-8");
				break;
			case 'strict':
				return urlencode($data);
				break;
		}
	}
	
}