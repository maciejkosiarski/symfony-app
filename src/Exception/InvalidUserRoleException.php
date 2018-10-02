<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidUserRoleException extends \Exception
{
	public function __construct(string $role, array $roles)
	{
		$message = sprintf('The %s is unacceptable, allowed roles: %s', $role, implode(',', $roles));

		parent::__construct($message);
	}
}