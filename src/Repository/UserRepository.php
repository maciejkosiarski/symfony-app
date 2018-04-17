<?php


namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserRepository
 * @package App\Repository
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
	/**
	 * @param string $username
	 * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function loadUserByUsername($username): ?UserInterface
	{
		return $this->createQueryBuilder('u')
			->where('u.username = :username OR u.email = :email')
			->setParameter('username', $username)
			->setParameter('email', $username)
			->getQuery()
			->getOneOrNullResult();
	}
}
