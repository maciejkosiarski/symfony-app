<?php


namespace App\Entity;

use App\Exception\InvalidRoleException;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Role
 *
 * @package App\Entity
 * @ORM\Table(name="app_roles")
 * @ORM\Entity()
 *
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class Role
{
	const ROLE_USER 	   = 'ROLE_USER';
	const ROLE_ADMIN 	   = 'ROLE_ADMIN';
	const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

	/**
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 */
	private $user;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="role", type="string", nullable=false)
	 */
	private $role;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created_at", type="datetime", nullable=false)
	 */
	protected $createdAt;

	/**
	 * Role constructor.
	 * @param User   $user
	 * @param string $role
	 * @throws InvalidRoleException
	 * @throws \ReflectionException
	 */
	public function __construct(User $user, string $role)
	{	$this->user = $user;
		$this->setRole($role);
		$this->createdAt = new \DateTime();
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user): void
	{
		$this->user = $user;
	}

	/**
	 * @return string
	 */
	public function getRole(): string
	{
		return $this->role;
	}

	/**
	 * @param string $role
	 * @throws InvalidRoleException
	 * @throws \ReflectionException
	 */
	public function setRole(string $role): void
	{
		if (!$this->isRoleValid($role)) {
			throw new InvalidRoleException($role, $this->getRoleList());
		}

		$this->role = $role;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt(): \DateTime
	{
		return $this->createdAt;
	}

	/**
	 * @param string $role
	 * @return bool
	 * @throws \InvalidArgumentException
	 * @throws \ReflectionException
	 */
	public function isRoleValid(string $role): bool
	{
		return in_array($role, $this->getRoleList());
	}


	/**
	 * @var string[]
	 */
	private $roleList;

	/**
	 * @return string[]
	 * @throws \ReflectionException
	 */
	private function getRoleList(): array
	{
		if (empty($this->roleList)) {
			$reflection = new \ReflectionClass(Role::class);

			foreach ($reflection->getConstants() as $constantName => $constantValue) {
				if (strpos($constantName, 'ROLE_') !== false) {
					$this->roleList[$constantName] = $constantValue;
				}
			}
		}

		return $this->roleList;
	}

}