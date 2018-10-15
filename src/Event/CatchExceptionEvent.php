<?php

declare(strict_types=1);

namespace App\Event;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;

class CatchExceptionEvent extends Event
{
	const NAME = 'catch.exception';

	protected $exception;

	protected $logger;

	public function __construct(\Exception $exception, LoggerInterface $logger)
	{
		$this->exception = $exception;
		$this->logger    = $logger;
	}

	public function getException(): \Exception
	{
		return $this->exception;
	}

	public function getLogger(): LoggerInterface
	{
		return $this->logger;
	}
}