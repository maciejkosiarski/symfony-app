<?php

namespace App\Exception;

/**
 * Class InvalidQueueStatusException
 * @package App\Exception
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class InvalidQueueStatusException extends \Exception
{
	public function __construct(string $status, array $statuses)
	{
		$message = sprintf('The passed queue position status (%s) is unacceptable, allowed types: %s', $status, implode(',', $statuses));

		parent::__construct($message);
	}
}