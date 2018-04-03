<?php


namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use App\Service\Notifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class NotificationRepository
 * @package App\Repository
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, Notification::class);
	}

	/**
	 * @param Notifier $notifier
	 * @return ArrayCollection
	 */
	public function getActiveByNotifier(Notifier $notifier): ArrayCollection
	{
		return new ArrayCollection(
			$this->createQueryBuilder('n')
				->where('n.active = :active')
				->setParameter('active', true)
				->andWhere('n.type = :type')
				->setParameter('type', $notifier->getNotificationType())
				->andWhere('n.dueDate < :dueDate')
				->setParameter('dueDate', new \DateTime('now'))
				->getQuery()
				->getResult()
		);
	}

	/**
	 * @param User $user
	 * @return ArrayCollection
	 */
	public function findByUser(User $user)
	{
		return new ArrayCollection(
			$this->createQueryBuilder('n')
				->where('n.user = :user')
				->setParameter('user', $user)
				->addOrderBy('n.active', 'DESC')
				->addOrderBy('n.dueDate', 'ASC')
				->getQuery()
				->getResult()
		);
	}
}
