<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\NotificationQueuePosition;
use App\Event\NotificationSentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class NotificationSentListener
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
	public function onNotificationSent(NotificationSentEvent $event): void
	{
		$queuePosition = $event->getNotificationQueuePosition();

		$this->logger->info(sprintf(
			'Notification id: %s successfully sent.',
			$queuePosition->getNotification()->getId())
		);

		$queuePosition->setStatus(NotificationQueuePosition::STATUS_SENT);

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