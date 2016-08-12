<?php
namespace Security\Daos\Doctrine;

use Mouf\Security\Password\Api\ForgotYourPasswordDao as ForgotYourPasswordInterface;
use Mouf\Security\Password\Exception\EmailNotFoundException;
use Mouf\Security\UserService\UserDaoInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Mouf\Security\UserService\UserInterface;

class UserDao extends EntityRepository implements UserDaoInterface, ForgotYourPasswordInterface {
	/**
	 * 
	 * @var EntityManager
	 */
	protected $entityManager = null;
	/**
	 * 
	 * @var EntityRepository;
	 */
	protected $userRepository = null;
	
	public function __construct(EntityManager $entityManager, $fullenameClassEntity) {
		$this->entityManager = $entityManager;
		parent::__construct($entityManager, $entityManager->getClassMetadata($fullenameClassEntity));
	}

	/**
	 * Returns a user from its login and its password, or null if the login or credentials are false.
	 * @param string $login
	 * @param string $password
	 * @return null
	 * @throws UserDaoException
	 */
	public function getUserByCredentials($login, $password) {
		//$this->entityManager->find();
		$user = $this->findByLogin(
					$login
				);
		if ($user === null || count($user) < 1) {
			return null;
		}
		else if (count($user) > 1)
			throw new UserDaoException("More than one user with this loggin: [".$login."] has been found"); // Or woops excep??
		$pwdHash = $user[0]->getPassword();
		if (password_verify($password, $pwdHash))
			return $user[0];
		return null;
	}

	/**
	 * Returns a user from its token.
	 * @param string $token
	 * @return null
	 * @throws UserDaoException
	 */
	public function getUserByToken($token) {
		return $this->findOneBy(['token' => $token]);
	}

	/**
	 * Discards a token.
	 * @param string $token
	 * @return null
	 * @throws UserDaoException
	 */
	public function discardToken($token){
		$user =  $this->findOneBy(['token' => $token]);

		if ($user === null)
			return null;
		$user->setToken(null);
		$this->entityManager->fetch();
	}
	
	/**
	 * Returns a user from its ID
	 *
	 * @param string $id
	 * @return UserInterface
	*/
	public function getUserById($id) {
		return $this->find($id);
	}

	/**
	 * Returns a user from its login
	 * @param string $login
	 * @return null
	 * @throws UserDaoException
	 */
	public function getUserByLogin($login) {
		return $this->findOneBy([
			'login'=>$login
		]);
	}

	/**
	 * Sets $token for user whose mail is $email, stores the token in database.
	 * Throws an EmailNotFoundException if the email is not part of the database.
	 *
	 * @param string $email
	 *
	 * @throws \Mouf\Security\Password\Api\EmailNotFoundException
	 */
	public function setToken(string $email, string $token)
	{
		$user = $this->findOneBy([
			'email' => $email
		]);

		if ($user === null) {
			throw EmailNotFoundException::notFound($email);
		}
		$user->setToken($token);
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}

	/**
	 * Sets the password matching to $token and discards $token.
	 * Throws an TokenNotFoundException if the token is not part of the database.
	 *
	 * @param string $token
	 * @param string $password
	 *
	 * @throws \Mouf\Security\Password\Api\TokenNotFoundException
	 */
	public function setPasswordAndDiscardToken(string $token, string $password)
	{
		$user = $this->getUserByToken($token);

		$user->setPassword($password);
		$user->setToken(null);
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}
}