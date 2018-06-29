<?php

namespace App\Event;

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NotificationSendedExceptionEvent
 * @package App\Event
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationSendedExceptionEvent extends Event
{
	const NAME = 'notification.sended.exception';

	/**
	 * @var \Exception
	 */
	protected $exception;

	/**
	 * @var ConsoleLogger
	 */
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