<?php
namespace App\Models;
use \System\Model as Model;

class Settings extends Model
{
	protected $sql = [
		'getSettings' => "SELECT m.id, d.meta_id, m.name, d.value, m.description FROM settings_data as d LEFT JOIN settings_meta as m ON d.meta_id = m.id ORDER BY m.id",
		'editSettingsField' => "UPDATE settings_data SET value = :field_value WHERE meta_id = :id",
		'getMeta' => "SELECT * FROM settings_meta",
		'getField' => "SELECT d.value FROM settings_data as d LEFT JOIN settings_meta as m ON d.meta_id = m.id WHERE m.name = :field LIMIT 1",
		'getSiteTitle' => "SELECT d.value FROM settings_data as d LEFT JOIN settings_meta as m ON d.meta_id = m.id WHERE m.name = 'site_title' ORDER BY m.id LIMIT 1"
	];

	public function __construct(){
		parent::__construct();
	}

	public function getSiteTitle(){
		return $this->execute('getSiteTitle')[0]->value;
	}

	public function getField($field_name){
		return $this->execute('getField', [
			':field' => $field_name
		])[0]->value;
	}
}