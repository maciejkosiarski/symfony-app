<?php

namespace App\EventListener;

use App\Entity\NotificationQueuePosition;
use App\Event\NotificationActivatedEvent;
use App\Event\NotificationBlockedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class NotificationActivatedListener
 * @package App\EventListener
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationActivatedListener
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
	 * @param NotificationActivatedEvent $event
	 */
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