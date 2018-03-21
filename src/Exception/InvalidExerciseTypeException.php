<?php

namespace App\Exception;

/**
 * Class InvalidExerciseTypeException
 * @package App\Exception
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class InvalidExerciseTypeException extends \Exception
{
	public function __construct(string $type, array $types)
	{
		$message = sprintf('The passed exercise type (%s) is unacceptable, allowed types: %s', $type, implode(',', $types));

		parent::__construct($message);
	}
}