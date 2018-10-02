<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\NotificationQueuePosition;
use App\Event\NotificationBlockedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class NotificationBlockedListener
{
	private $logger;

	private $em;

	public function __construct(LoggerInterface $logger, EntityManagerInterface $em)
	{
		$this->logger = $logger;
		$this->em     = $em;
	}

	/**
	 * @throws \App\Exception\InvalidQueueStatusException
	 * @throws \ReflectionException
	 */
	public function onNotificationBlocked(NotificationBlockedEvent $event): void
	{
		$notification = $event->getNotification();

		$positions = $this->em->getRepository(NotificationQueuePosition::class)->findBy([
			'notification' => $notification,
			'status' => NotificationQueuePosition::STATUS_PENDING
		]);

		foreach ($positions as $position) {
			$position->setStatus(NotificationQueuePosition::STATUS_CANCELED);
		}

		$this->em->flush();

		$this->logger->info(sprintf(
			'Notification id: %s successfully blocked.',
			$notification->getId())
		);
	}
}