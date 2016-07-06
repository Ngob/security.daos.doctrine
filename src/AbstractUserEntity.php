<?php
namespace Security\Daos\Doctrine;

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
	 * @Column(type="string", unique=true)
	 */
	protected $login;
	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $password;
	/**
	 * @var string
	 * @Column(type="string", nullable= true)
	 */
	protected $token = null;
	
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
		return $this->token;
	}
	
	public function setToken($token) {
		$this->token = $token;
	}
	
	public function setLogin($login) {
		if (empty($login)) {
			throw new \InvalidArgumentException("Must provide a login");
		}
		$this->login = $login;
	}
	
	public function setPassword($password) {
		if (empty($password)) {
			throw new \InvalidArgumentException("Must provide a password");
		}
		$this->password = password_hash($password, PASSWORD_DEFAULT);
	}
}
