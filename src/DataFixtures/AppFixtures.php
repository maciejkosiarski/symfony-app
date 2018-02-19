<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}

	/**
	 * @param ObjectManager $manager
	 * @throws \App\Exception\InvalidRoleException
	 * @throws \ReflectionException
	 */
	public function load(ObjectManager $manager)
	{
		$this->loadUsers($manager);
	}

	/**
	 * @param ObjectManager $manager
	 * @throws \App\Exception\InvalidRoleException
	 * @throws \ReflectionException
	 */
	private function loadUsers(ObjectManager $manager)
	{
		foreach ($this->getUserData() as [$username, $password, $email, $apiKey, $roles]) {
			$user = new User();
			$user->setUsername($username);
			$user->setEmail($email);
			$user->setPassword($this->passwordEncoder->encodePassword($user, $password));
			$user->setApiKey($apiKey);

			foreach ($roles as $role) {
				$manager->persist(new Role($user, $role));
			}

			$manager->persist($user);

		}

		$manager->flush();
	}

	private function getUserData(): array
	{
		return [
			['admin', 'admin', 'admin@example.com', 'admin_api_key', ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']],
			['tom_doe', 'tom_doe', 'tom_admin@symfony.com', 'tom_api_key', ['ROLE_USER']],
			['john_doe', 'john_doe', 'john_user@symfony.com', 'john_api_key', ['ROLE_USER']],
		];
	}
}
