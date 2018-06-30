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

use App\Entity\ExerciseType;
use App\Entity\Notification;
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
	 * @throws \App\Exception\InvalidNotificationTypeException
	 * @throws \App\Exception\InvalidUserRoleException
	 * @throws \ReflectionException
	 */
	public function load(ObjectManager $manager): void
	{
		$this->loadUsers($manager);
		$this->loadExerciseTypes($manager);
	}

	/**
	 * @param ObjectManager $manager
	 * @throws \App\Exception\InvalidNotificationTypeException
	 * @throws \App\Exception\InvalidUserRoleException
	 * @throws \ReflectionException
	 */
	private function loadUsers(ObjectManager $manager): void
	{
		foreach ($this->getUserData() as [$username, $password, $email, $roles]) {
			$user = new User();
			$user->setUsername($username);
			$user->setEmail($email);
			$user->setPassword($this->passwordEncoder->encodePassword($user, $password));

			foreach ($roles as $role) {
				$manager->persist(new Role($user, $role));
			}

			foreach ($this->getNotificationsData() as [$type, $message, $intervalExpression]) {
				$notification = new Notification();
				$notification->setUser($user);
				$notification->setType($type);
				$notification->setMessage($message);
				$notification->setIntervalExpression($intervalExpression);

				$manager->persist($notification);

			}

			$manager->persist($user);
			$manager->flush();
		}
	}

	private function loadExerciseTypes(ObjectManager $manager): void
	{
		foreach ($this->getExerciseTypeData() as $name) {
			$manager->persist(new ExerciseType($name));
		}

		$manager->flush();
	}

	private function getUserData(): array
	{
		return [
			['admin', 'admin', 'admin@example.com', ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']],
			['tom_doe', 'tom_doe', 'tom_admin@symfony.com', ['ROLE_USER']],
			['john_doe', 'john_doe', 'john_user@symfony.com', ['ROLE_USER']],
		];
	}

	private function getNotificationsData(): array
	{
		return [
			[2, 'test notification', '0 * * * *'],
		];
	}

	private function getExerciseTypeData(): array
	{
		return [
			'rowing machine',
			'run',
		];
	}
}
