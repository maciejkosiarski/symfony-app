<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Inflector\Inflector;

trait DataLoader
{
	public function loadData(array $data): self
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