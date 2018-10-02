<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\InvalidUserRoleException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="app_roles")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Role extends BaseEntity
{
	const ROLE_USER 	   = 'ROLE_USER';
	const ROLE_ADMIN 	   = 'ROLE_ADMIN';
	const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("App\Entity\User")
	 */
	private $user;

	/**
	 * @ORM\Column(name="role", type="string", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	private $role;

	/**
	 * @throws InvalidUserRoleException
	 * @throws \ReflectionException
	 */
	public function __construct(User $user, string $role)
	{	$this->user = $user;
		$this->setRole($role);
	}

	public function getUser(): User
	{
		return $this->user;
	}

	public function setUser(User $user): void
	{
		$this->user = $user;
	}

	public function getRole(): string
	{
		return $this->role;
	}

	/**
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
	 * @throws \InvalidArgumentException
	 * @throws \ReflectionException
	 */
	private function isRoleValid(string $role): bool
	{
		return in_array($role, $this->getRoleList());
	}

	private $roleList;

	/**
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