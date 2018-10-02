<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="app_exercise_types")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class ExerciseType extends BaseEntity
{
	/**
	 * @ORM\Column(name="name", type="string", unique=true, nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	private $name;

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	public function __toString(): string
	{
		return $this->name;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}
}