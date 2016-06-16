<?php
namespace App\Models;

class AllegroWebAPIService {
	protected $_client = NULL;
	protected $session = NULL;
	protected $versionKeys = array();
	protected $key;
	protected $countryId;
	protected $default_request;

	function __construct($key, $countryId = 1, $wsdl){
		$this->key = $key;
		$this->countryId = $countryId;
		
		$options = array();
		$options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
		$options['trace'] = true;
		$options['encoding'] = 'utf-8';

		$this->_client = new \SoapClient($wsdl, $options);
		$request = array(
			'countryId' => $countryId,
			'webapiKey' => $key
		);

		$status = $this->_client->doQueryAllSysStatus($request);
		foreach ($status->sysCountryStatus->item as $row) {
			$this->versionKeys[$row->countryId] = $row;
		}

		$this->default_request = $request;
	}

	public function login($login, $password){
			$request = array(
				'userLogin' => $login,
				'userPassword' => $password,
				'countryCode' => $this->countryId,
				'webapiKey' => $this->key,
				'localVersion' => $this->versionKeys[$this->countryId]->verKey,
			);

			$this->session = $this->_client->doLogin($request);
	}

	public function __call($name, $arguments){
			if(isset($arguments[0])) $arguments = (array)$arguments[0];
			else $arguments = array();
			
			$arguments['sessionId'] = $this->session->sessionHandlePart;
			$arguments['sessionHandle'] = $this->session->sessionHandlePart;
			$arguments['webapiKey'] = $this->key;
			$arguments['countryId'] = $this->countryId;
			$arguments['countryCode'] = $this->countryId;

			return $this->_client->$name($arguments);
	}
}