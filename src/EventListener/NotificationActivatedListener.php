<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\NotificationQueuePosition;
use App\Event\NotificationActivatedEvent;
use App\Event\NotificationBlockedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class NotificationActivatedListener
{
	private $logger;

	private $em;

	public function __construct(LoggerInterface $logger, EntityManagerInterface $em)
	{
		$this->logger = $logger;
		$this->em     = $em;
	}

	public function onNotificationActivated(NotificationActivatedEvent $event): void
	{
		$notification = $event->getNotification();

		$notification->addToQueue($notification->getDateTimeNextRun());

		$this->em->flush();

		$this->logger->info(sprintf(
			'Notification id: %s successfully activated.',
			$notification->getId())
		);
	}
}