<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use App\Service\Notifier\Notifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NotificationRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, Notification::class);
	}

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

	public function findByUser(User $user):ArrayCollection
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

	public function findPaginateByUser(int $page, int $limit, User $user): Paginator
	{
		return new Paginator(
			$this->createQueryBuilder('n')
				->where('n.user = :user')
				->setParameter('user', $user)
				->addOrderBy('n.active', 'DESC')
				->setFirstResult($page * $limit - $limit)
				->orderBy('n.createdAt', 'DESC')
				->setMaxResults($limit)
				->getQuery(),
			$fetchJoinCollection = true
		);
	}
}
