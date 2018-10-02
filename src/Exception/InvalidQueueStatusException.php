<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidQueueStatusException extends \Exception
{
	public function __construct(int $status, array $statuses)
	{
		$message = sprintf('The passed queue position status (%s) is unacceptable, allowed types: %s', $status, implode(',', $statuses));

		parent::__construct($message);
	}
}