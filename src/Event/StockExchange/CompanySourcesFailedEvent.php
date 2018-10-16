<?php

declare(strict_types=1);

namespace App\Event\StockExchange;

use App\Exception\StockExchange\AllSourceFailedException;
use Symfony\Component\EventDispatcher\Event;

class CompanySourcesFailedEvent extends Event
{
	const NAME = 'company.sources.failed';

	protected $exception;

	public function __construct(AllSourceFailedException $exception)
	{
		$this->exception = $exception;
	}

	public function getException(): AllSourceFailedException
	{
		return $this->exception;
	}
}