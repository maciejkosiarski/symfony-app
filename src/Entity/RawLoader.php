<?php

namespace App\Entity;

/**
 * Trait RawLoader
 * @package App\Entity
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
trait RawLoader
{
	/**
	 * @return array
	 */
	public function toArray()
	{
		return get_object_vars($this);
	}
}