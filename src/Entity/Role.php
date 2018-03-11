<?php


namespace App\Entity;

use App\Exception\InvalidUserRoleException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Role
 *
 * @package App\Entity
 * @ORM\Table(name="app_roles")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class Role extends BaseEntity
{
	const ROLE_USER 	   = 'ROLE_USER';
	const ROLE_ADMIN 	   = 'ROLE_ADMIN';
	const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("App\Entity\User")
	 */
	private $user;

	/**
	 * @var string
	 * @ORM\Column(name="role", type="string", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	private $role;

	/**
	 * Role constructor.
	 *
	 * @param User   $user
	 * @param string $role
	 * @throws InvalidUserRoleException
	 * @throws \ReflectionException
	 */
	public function __construct( $user, string $role)
	{	$this->user = $user;
		$this->setRole($role);
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
	 * @throws InvalidUserRoleException
	 * @throws \ReflectionException
	 */
	public function setRole(string $role): void
	{
		if (!$this->isRoleValid($role)) {
			throw new InvalidUserRoleException($role, $this->getRoleList());
		}

		$this->role = $role;
	}

	/**
	 * @param string $role
	 * @return bool
	 * @throws \InvalidArgumentException
	 * @throws \ReflectionException
	 */
	private function isRoleValid(string $role): bool
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