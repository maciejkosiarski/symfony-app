<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\NotificationQueuePosition;
use App\Event\NotificationSendedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class NotificationSendedListener
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
	public function onNotificationSended(NotificationSendedEvent $event): void
	{
		$queuePosition = $event->getNotificationQueuePosition();

		$this->logger->info(sprintf(
			'Notification id: %s successfully sended.',
			$queuePosition->getNotification()->getId())
		);

		$queuePosition->setStatus(NotificationQueuePosition::STATUS_SENDED);

		if ($queuePosition->getNotification()->isRecurrent()) {
			$dueDate = $queuePosition->getNotification()->getDateTimeNextRun();

			if ($dueDate->getTimestamp() <= $queuePosition->getDueDate()->getTimestamp()) {
				$dueDate = $queuePosition->getNotification()->getDateTimeSpecificNextRun(2);
			}

			$queuePosition->getNotification()->addToQueue($dueDate);

			$this->logger->info(sprintf(
				'Notification id: %s added to queue.',
				$queuePosition->getNotification()->getId())
			);
		} else {
			$queuePosition->getNotification()->activeToggle();
		}

		$this->em->flush();
	}
}