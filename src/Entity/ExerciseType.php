<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExerciseType
 *
 * @package App\Entity
 * @ORM\Table(name="app_exercise_type")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class ExerciseType extends BaseEntity
{
	/**
	 * @var string
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

	public function __toString()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}
}