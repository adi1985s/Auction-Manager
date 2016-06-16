<?php
namespace App\Models;
use \System\Model as Model;

class Goods extends Model
{
	protected $sql = [
		'getAll' => "SELECT * FROM goods ORDER BY id DESC"
	];

	public function __construct(){
		parent::__construct();
	}

	public function getAll(){
		return $this->execute('getAll');
	}
}