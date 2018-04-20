<?php

namespace App\Exception;

/**
 * Class NotifyException
 * @package App\Exception
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
	class NotifyException extends \Exception
{
	public function __construct(string $user)
	{
		parent::__construct(sprintf('The user %s has not received his notification', $user));
	}
}