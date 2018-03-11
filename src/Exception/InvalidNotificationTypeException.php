<?php

namespace App\Exception;

/**
 * Class InvalidNotificationTypeException
 * @package App\Exception
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class InvalidNotificationTypeException extends \Exception
{
	public function __construct(string $type, array $types)
	{
		$message = sprintf('The passed type (%s) is unacceptable, allowed types: %s', $type, implode(',', $types));

		parent::__construct($message);
	}
}