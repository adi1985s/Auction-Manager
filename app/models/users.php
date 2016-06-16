<?php
namespace App\Models;
use \System\Model as Model;

class Users extends Model {
	protected $sql = [
		'allUsers' => "SELECT * FROM users ORDER BY id",
		'getUser' => "SELECT * FROM users WHERE id = :id LIMIT 1",
		'getUserByName' => "SELECT * FROM users WHERE username = :name LIMIT 1",
		'addUser' => "INSERT INTO users VALUES (NULL, :name, :email, :salt, :password, :company, :cash, 0)",
		'isExists' => "SELECT COUNT(id) as count FROM users WHERE id = :id LIMIT 1",
		'removeUser' => "DELETE FROM users WHERE id = :id LIMIT 1;"
	];
}