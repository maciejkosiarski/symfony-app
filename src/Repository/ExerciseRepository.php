<?php


namespace App\Repository;

use App\Entity\Exercise;
use App\Entity\User;
use App\Entity\ExerciseType;
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
	 * @param ExerciseType|null $type
	 * @return Paginator
	 */
	public function findPaginateByUserAndType(int $page, int $limit, User $user, ?ExerciseType $type): Paginator
	{
		$query = $this->createQueryBuilder('e')
			->where('e.user = :user')
			->setParameter('user', $user);

		if ($type) {
			$query->andWhere('e.type = :type')
				->setParameter('type', $type);
		}

		$query->addOrderBy('e.createdAt', 'DESC')
			->setFirstResult($page * $limit - $limit)
			->setMaxResults($limit)
			->getQuery();

		return new Paginator($query, $fetchJoinCollection = true);
	}

	/**
	 * @param User $user
	 * @param ExerciseType|null $type
	 * @return float
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function countTotalHoursByUserAndType(User $user, ?ExerciseType $type): float
	{
		$query = $this->createQueryBuilder('e')
			->Where('e.user = :user')
			->setParameter('user', $user);

		if ($type) {
			$query->andWhere('e.type = :type')
				->setParameter('type', $type);
		}

		$minutes = $query->select('SUM(e.minutes)')
			->getQuery()
			->getSingleScalarResult();

		return round(($minutes / 60), 2);
	}
}
