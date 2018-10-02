<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\NotificationSendedExceptionEvent;
use Psr\Log\LoggerInterface;

class NotificationSendedExceptionListener
{
	private $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function onNotificationSendedException(NotificationSendedExceptionEvent $event): void
	{
		$exception = $event->getException();

		$message = sprintf(
			'Sending notifications caused a problem: %s',
			$exception->getMessage()
		);

		$this->logger->error($message, [
				'file'  => $exception->getFile(),
				'line'  => $exception->getLine(),
				'trace' => $exception->getTraceAsString(),
			]
		);

		$event->getLogger()->error($message);
	}
}