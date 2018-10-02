<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="app_exercises")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Exercise extends BaseEntity
{
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("App\Entity\User")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\ExerciseType")
	 * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("ExerciseType")
	 */
	private $type;

	/**
	 * @ORM\Column(name="minutes", type="integer", nullable=false)
	 * @Assert\NotBlank(groups={"form"})
	 * @Assert\Type("integer", groups={"form"})
	 * @Assert\GreaterThan(0, groups={"form"})
	 */
	private $minutes;

	/**
	 * @ORM\Column(name="note", type="string", nullable=true)
	 * @Assert\Type("string", groups={"form"})
	 * @Assert\Length(max = 255, maxMessage = "Your note cannot be longer than 255 characters", groups={"form"})
	 */
	private $note;

	public function __construct(User $user, $minutes)
	{
		$this->user    = $user;
		$this->minutes = $minutes;
	}

	public function getUser(): User
	{
		return $this->user;
	}

	public function setUser(User $user): void
	{
		$this->user = $user;
	}

	public function getType(): ?ExerciseType
	{
		return $this->type;
	}

	public function setType(ExerciseType $type): void
	{
		$this->type = $type;
	}

	public function getMinutes(): int
	{
		return $this->minutes;
	}

	public function setMinutes(int $minutes): void
	{
		$this->minutes = $minutes;
	}

	public function getNote(): ?string
	{
		return $this->note;
	}

	public function setNote(string $note): void
	{
		$this->note = $note;
	}
}