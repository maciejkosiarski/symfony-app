<?php

namespace App\Exception;

/**
 * Class InvalidRoleException
 * @package App\Exception
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class InvalidRoleException extends \Exception
{
	public function __construct(string $role, array $roles)
	{
		$message = sprintf('The %s is unacceptable, allowed roles: %s', $role, implode(',', $roles));

		parent::__construct($message);
	}
}