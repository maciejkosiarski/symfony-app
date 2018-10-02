<?php

declare(strict_types=1);

namespace App\Exception;

class CreateNotifierException extends \Exception
{
	public function __construct(string $createMethod)
	{
		parent::__construct(sprintf('Notifier factory has not defined %s method', $createMethod));
	}
}