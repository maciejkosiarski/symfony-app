<?php

namespace App\Exception;

/**
 * Class CreateNotifierException
 * @package App\Exception
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class CreateNotifierException extends \Exception
{
	public function __construct(string $createMethod)
	{
		parent::__construct(sprintf('Notifier factory has not defined %s method', $createMethod));
	}
}