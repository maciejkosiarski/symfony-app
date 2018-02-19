<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @package App\Entity
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class User implements UserInterface, \Serializable
{
	/**
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(name="username", type="string", length=40, unique=true, nullable=true)
	 * @Assert\Type("string")
	 * @Assert\Length(
	 *     min = 2,
	 * )
	 */
	private $username;

	/**
	 * @ORM\Column(name="password", type="string", length=64)
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	private $password;

	/**
	 * @ORM\Column(name="email", type="string", length=60, unique=true, nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Email()
	 */
	private $email;

	/**
	 * @ORM\Column(name="phone", type="integer", unique=true, nullable=true)
	 * @Assert\Type("integer")
	 */
	private $phone;

	/**
	 * @ORM\Column(name="api_key", type="string", unique=true)
	 * @Assert\Type("string")
	 */
	private $apiKey;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 * @Assert\Type("boolean")
	 */
	private $isActive;

	/**
	 * @var Role[]|ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="Role", mappedBy="user", cascade={"persist"})
	 * @ORM\OrderBy({"id" = "DESC"})
	 */
	public $roles;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created_at", type="datetime", nullable=false)
	 */
	protected $createdAt;


	public function __construct()
	{
		$this->isActive  = true;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername(string $username)
	{
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password)
	{
		$this->password = $password;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email)
	{
		$this->email = $email;
	}

	/**
	 * @return mixed
	 */
	public function getPhone(): int
	{
		return $this->phone;
	}

	/**
	 * @param int $phone
	 */
	public function setPhone(int $phone)
	{
		$this->phone = $phone;
	}

	/**
	 * @return mixed
	 */
	public function getApiKey(): string
	{
		return $this->apiKey;
	}

	/**
	 * @param string $apiKey
	 */
	public function setApiKey(string $apiKey)
	{
		$this->apiKey = $apiKey;
	}

	/**
	 * @return mixed
	 */
	public function isActive(): bool
	{
		return $this->isActive;
	}

	/**
	 * @param bool $isActive
	 */
	public function setIsActive(bool $isActive)
	{
		$this->isActive = $isActive;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt(): \DateTime {
		return $this->createdAt;
	}

	/**
	 * @return array
	 */
	public function getRoles() : array
	{
		return $this->roles->map(function ($role){
			return $role->getRole();
		})->toArray();
	}

	public function getSalt()
	{
		return null;
	}

	public function eraseCredentials()
	{

	}

	/**
	 * @return string
	 */
	public function serialize(): string
	{
		return serialize([
			$this->id,
			$this->username,
			$this->password,
		]);
	}

	/**
	 * @param string $serialized
	 */
	public function unserialize($serialized)
	{
		list (
			$this->id,
			$this->username,
			$this->password,
		) = unserialize($serialized);
	}

	/**
	 * @ORM\PrePersist()
	 */
	public function prePersist() {
		$this->createdAt = new \DateTime();

		if (!$this->username) {
			$this->username = $this->email;
		}
	}
}