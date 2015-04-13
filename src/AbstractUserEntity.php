<?php
namespace Mouf\Security\Userdao;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Mouf\Security\UserService\UserInterface;
abstract class AbstractUserEntity implements UserInterface {
	/**
	 * @var int
	 * @Id 
	 * @Column(type="integer") 
	 * @GeneratedValue
	 */
	protected $id;
	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $login;
	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $password;
	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $token;
	
	public function getId() {
		return $this->id;
	}
	
	public function getLogin() {
		return $this->login;
	}
	
	public function getPassword() {
		return $this->password;
	}
	
	public function getToken() {
		return $token;
	}
	
	public function setToken($token) {
		$this->token = $token;
	}
	
	public function setLogin($login) {
		$this->login = $login;
	}
	
	public function setPassword($password) {
		$this->password = password_hash($password, PASSWORD_DEFAULT);
	}
}