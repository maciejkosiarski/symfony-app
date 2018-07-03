<?php

namespace App\EventListener;

use App\Entity\NotificationQueuePosition;
use App\Event\NotificationSendedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class NotificationSendedListener
 * @package App\EventListener
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationSendedListener
{
	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	public function __construct(LoggerInterface $logger, EntityManagerInterface $em)
	{
		$this->logger = $logger;
		$this->em     = $em;
	}

	/**
	 * @param NotificationSendedEvent $event
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
			$queuePosition = new NotificationQueuePosition();
			$queuePosition->setNotification($queuePosition->getNotification());
			$queuePosition->setDueDate($queuePosition->getNotification()->getDateTimeNextRun());

			$this->em->persist($queuePosition);
		} else {
			$queuePosition->getNotification()->activeToggle();
		}

		$this->em->flush();
	}
}