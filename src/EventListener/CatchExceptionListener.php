<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\CatchExceptionEvent;
use Psr\Log\LoggerInterface;

class CatchExceptionListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onCatchException(CatchExceptionEvent $event): void
	{
		$exception = $event->getException();

        $this->logger->error($exception->getMessage(), [
				'file'  => $exception->getFile(),
				'line'  => $exception->getLine(),
				'trace' => $exception->getTraceAsString(),
			]
		);
	}
}