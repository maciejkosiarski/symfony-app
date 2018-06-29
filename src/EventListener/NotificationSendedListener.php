<?php

namespace App\EventListener;

use App\Event\NotificationSendedEvent;
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

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function onNotificationSended(NotificationSendedEvent $event): void
	{
		$notification = $event->getNotification();

		$this->logger->info(sprintf(
			'Notification id: %s successfully sended.',
			$notification->getId())
		);
	}
}