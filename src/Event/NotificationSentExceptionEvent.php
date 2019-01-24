<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\EventDispatcher\Event;

class NotificationSentExceptionEvent extends Event
{
	const NAME = 'notification.sent.exception';

	protected $exception;

	protected $logger;

	public function __construct(\Exception $exception, ConsoleLogger $logger)
	{
		$this->exception = $exception;
		$this->logger    = $logger;
	}

	public function getException(): \Exception
	{
		return $this->exception;
	}

	public function getLogger(): ConsoleLogger
	{
		return $this->logger;
	}
}