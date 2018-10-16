<?php

declare(strict_types=1);

namespace App\EventListener\StockExchange;

use App\Entity\Notification;
use App\Entity\Role;
use App\Event\StockExchange\CompanySourcesFailedEvent;
use Doctrine\ORM\EntityManagerInterface;

class CompanySourcesFailedListener
{
	private $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

    /**
     * @throws \App\Exception\InvalidNotificationTypeException
     * @throws \ReflectionException
     */
	public function onCompanySourcesFailed(CompanySourcesFailedEvent $event): void
	{
	    $roles = $this->em->getRepository(Role::class);
	    /** @var Role $superAdmin */
        foreach ($roles->findByRole(Role::ROLE_SUPER_ADMIN) as $superAdmin) {
            $notification = new Notification();
            $notification->setUser($superAdmin->getUser());
            $notification->setMessage($event->getException()->getMessage());
            $notification->setRecurrent(false);
            $notification->setType(Notification::TYPE_EMAIL);
            $notification->setIntervalExpression('15 9-17 * * 1-5');

            $this->em->persist($notification);
        }

        $this->em->flush();
	}
}