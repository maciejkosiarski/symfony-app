<?php

namespace App\Exception;

/**
 * Class CommandAlreadyRunningException
 * @package App\Exception
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
	class CommandAlreadyRunningException extends \Exception
{
	public function __construct(string $command)
	{
		parent::__construct(sprintf('The command %s is already running in another process.', $command));
	}
}