<?php

declare(strict_types=1);

namespace App\EventListener\StockExchange;

use App\Event\StockExchange\ShareFoundExceptionEvent;
use Psr\Log\LoggerInterface;

class ShareFoundExceptionListener
{
	private $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function onShareFoundException(ShareFoundExceptionEvent $event): void
	{
		$exception = $event->getException();

		$this->logger->error($exception->getMessage(), [
				'file'  => $exception->getFile(),
				'line'  => $exception->getLine(),
				'trace' => $exception->getTraceAsString(),
			]
		);

		$event->getLogger()->error($exception->getMessage());
	}
}