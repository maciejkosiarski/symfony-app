<?php


namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class NotificationRepository
 * @package App\Repository
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationRepository extends EntityRepository
{
	/**
	 * @param int $type
	 * @return array
	 */
	public function getActiveByType(int $type): array
	{
		return $this->createQueryBuilder('n')
			->where('n.active = :active')
			->setParameter('active', true)
			->andWhere('n.type = :type')
			->setParameter('type', $type)
			->andWhere('n.dueDate < :dueDate')
			->setParameter('dueDate', new \DateTime('now'))
			->getQuery()
			->getResult();
	}
}
