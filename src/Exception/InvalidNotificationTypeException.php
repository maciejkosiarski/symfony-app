<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidNotificationTypeException extends \Exception
{
	public function __construct(int $type, array $types)
	{
		$message = sprintf('The passed notification type (%s) is unacceptable, allowed types: %s', $type, implode(',', $types));

		parent::__construct($message);
	}
}