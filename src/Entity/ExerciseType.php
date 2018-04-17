<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExerciseType
 *
 * @package App\Entity
 * @ORM\Table(name="app_exercise_types")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class ExerciseType extends BaseEntity
{
	/**
	 * @var string
	 * @ORM\Column(name="name", type="string", unique=true, nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	private $name;

	/**
	 * ExerciseType constructor.
	 *
	 * @param string $name
	 */
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