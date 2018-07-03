<?php


namespace App\Repository;

use App\Entity\NotificationQueuePosition;
use App\Service\Notifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class NotificationQueuePositionRepository
 * @package App\Repository
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationQueuePositionRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, NotificationQueuePosition::class);
	}

	/**
	 * @param Notifier $notifier
	 * @return ArrayCollection
	 */
	public function getActiveByNotifier(Notifier $notifier): ArrayCollection
	{
		return new ArrayCollection(
			$this->createQueryBuilder('nqp')
				->leftJoin('nqp.notification', 'n' )
				->where('nqp.status = :status')
				->setParameter('status', NotificationQueuePosition::STATUS_PENDING)
				->andwhere('n.active = :active')
				->setParameter('active', true)
				->andWhere('n.type = :type')
				->setParameter('type', $notifier->getNotificationType())
				->andWhere('nqp.dueDate < :dueDate')
				->setParameter('dueDate', new \DateTime('now'))
				->orderBy('nqp.createdAt', 'DESC')
				->getQuery()
				->getResult()
		);
	}
}
