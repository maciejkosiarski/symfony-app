<?php

namespace App\Entity;

use Doctrine\Common\Inflector\Inflector;

/**
 * Trait DataLoader
 * @package App\Entity
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
trait DataLoader
{
	/**
	 * @param array $data
	 *
	 * @return $this
	 */
	public function loadData(array $data)
	{
		foreach ($data as $key => $value) {
			$method = Inflector::camelize('set_' . $key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}

		return $this;
	}
}