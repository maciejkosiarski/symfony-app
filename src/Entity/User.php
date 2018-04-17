<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class User
 *
 * @package App\Entity
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class User extends BaseEntity implements UserInterface, \Serializable
{
	/**
	 * @ORM\Column(name="username", type="string", length=255, unique=true, nullable=true)
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
	 * @ORM\Column(name="email", type="string", length=255, unique=true, nullable=false)
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
	 * @ORM\GeneratedValue(strategy="UUID")
	 * @Assert\Type("string")
	 */
	private $apiKey;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 * @Assert\Type("boolean")
	 */
	private $isActive;

	/**
	 * @var PersistentCollection
	 * @ORM\OneToMany(targetEntity="Role", mappedBy="user", cascade={"persist"})
	 * @ORM\OrderBy({"id" = "DESC"})
	 */
	private $roles;

	/**
	 * @var PersistentCollection
	 * @ORM\OneToMany(targetEntity="Notification", mappedBy="user", cascade={"persist"})
	 * @ORM\OrderBy({"id" = "DESC"})
	 */
	private $notifications;

	public function __construct()
	{
		$this->isActive  = true;
		$this->apiKey 	 = sha1(uniqid());
	}

	/**
	 * @return string|null
	 */
	public function getUsername(): ?string
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	/**
	 * @return string|null
	 */
	public function getPassword(): ?string
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	/**
	 * @return string|null
	 */
	public function getEmail(): ?string
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	/**
	 * @return integer
	 */
	public function getPhone(): ?int
	{
		return $this->phone;
	}

	/**
	 * @param int $phone
	 */
	public function setPhone(int $phone): void
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
	public function setApiKey(string $apiKey): void
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
	public function setIsActive(bool $isActive): void
	{
		$this->isActive = $isActive;
	}

	/**
	 * @return array
	 */
	public function getRoles(): array
	{
		return $this->roles->map(function ($role){
			/** @var Role $role */
			return $role->getRole();
		})->toArray();
	}

	/**
	 * @return PersistentCollection
	 */
	public function getNotifications(): PersistentCollection
	{
		return $this->notifications;
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
}