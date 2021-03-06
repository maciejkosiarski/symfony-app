<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\NotificationQueuePosition;
use App\Entity\User;
use App\Service\Notifier\Notifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NotificationQueuePositionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NotificationQueuePosition::class);
    }

    public function getQueueToSendByNotifier(Notifier $notifier): ArrayCollection
    {
        return new ArrayCollection(
            $this->createQueryBuilder('nqp')
                ->leftJoin('nqp.notification', 'n')
                ->where('nqp.status = :status')
                ->setParameter('status', NotificationQueuePosition::STATUS_PENDING)
                ->andwhere('n.active = :active')
                ->setParameter('active', true)
                ->andWhere('n.type = :type')
                ->setParameter('type', $notifier->getNotificationType())
                ->andWhere('nqp.dueDate < :dueDate')
                ->setParameter('dueDate', new \DateTime('now'))
                ->orderBy('nqp.createdAt', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
        );
    }

    public function findPaginateByUser(int $page, int $limit, User $user): Paginator
    {
        return new Paginator(
            $this->createQueryBuilder('nqp')
                ->leftJoin('nqp.notification', 'n')
                ->where('n.user = :user')
                ->setParameter('user', $user)
                ->andWhere('n.active = true')
                ->andWhere('nqp.status = :status')
                ->setParameter('status', NotificationQueuePosition::STATUS_PENDING)
                ->addOrderBy('nqp.dueDate', 'ASC')
                ->setFirstResult($page * $limit - $limit)
                ->setMaxResults($limit)
                ->getQuery(),
            $fetchJoinCollection = true
        );
    }
}
