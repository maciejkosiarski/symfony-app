<?php

declare(strict_types=1);

namespace App\Event\StockExchange;

use Symfony\Component\EventDispatcher\Event;

class ShareFoundExceptionEvent extends Event
{
	const NAME = 'share.found.exception';

	protected $exception;

	public function __construct(\Exception $exception)
	{
		$this->exception = $exception;
	}

	public function getException(): \Exception
	{
		return $this->exception;
	}
}