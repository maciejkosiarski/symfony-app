<?php


namespace App\Repository;

use App\Entity\Exercise;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ExerciseRepository
 * @package App\Repository
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class ExerciseRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, Exercise::class);
	}

	/**
	 * @param int  $page
	 * @param int  $limit
	 * @param User $user
	 * @return Paginator
	 */
	public function findPaginateByUser(int $page, int $limit, User $user): Paginator
	{
		return new Paginator(
			$this->createQueryBuilder('e')
				->where('e.user = :user')
				->setParameter('user', $user)
				->addOrderBy('e.createdAt', 'DESC')
				->setFirstResult($page * $limit - $limit)
				->setMaxResults($limit)
				->getQuery(),
			$fetchJoinCollection = true
		);
	}

	/**
	 * @param User $uer
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function countTotalHoursByUser(User $uer): float
	{
		$minutes = $this->createQueryBuilder('e')
			->Where('e.user = :user')
			->setParameter('user', $uer)
			->select('SUM(e.minutes)')
			->getQuery()
			->getSingleScalarResult();

		return round(($minutes / 60), 2);
	}
}
