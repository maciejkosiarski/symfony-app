<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Exercise
 *
 * @package App\Entity
 * @ORM\Table(name="app_exercises")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class Exercise extends BaseEntity
{
	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("App\Entity\User")
	 */
	private $user;

	/**
	 * @var ExerciseType
	 * @ORM\ManyToOne(targetEntity="App\Entity\ExerciseType")
	 * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("ExerciseType")
	 */
	private $type;

	/**
	 * @var int
	 * @ORM\Column(name="minutes", type="integer", nullable=false)
	 * @Assert\NotBlank(groups={"form"})
	 * @Assert\Type("integer", groups={"form"})
	 * @Assert\GreaterThan(0, groups={"form"})
	 */
	private $minutes;

	/**
	 * @var string
	 * @ORM\Column(name="note", type="string", nullable=true)
	 * @Assert\Type("string", groups={"form"})
	 * @Assert\Length(max = 255, maxMessage = "Your note cannot be longer than 255 characters", groups={"form"})
	 */
	private $note;

	/**
	 * Exercise constructor.
	 *
	 * @param User         $user
	 * @param int          $minutes
	 */
	public function __construct(User $user, $minutes)
	{
		$this->user    = $user;
		$this->minutes = $minutes;
	}

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user): void
	{
		$this->user = $user;
	}

	/**
	 * @return ExerciseType
	 */
	public function getType(): ?ExerciseType
	{
		return $this->type;
	}

	/**
	 * @param ExerciseType $type
	 */
	public function setType(ExerciseType $type): void
	{
		$this->type = $type;
	}

	/**
	 * @return int
	 */
	public function getMinutes(): int
	{
		return $this->minutes;
	}

	/**
	 * @param int $minutes
	 */
	public function setMinutes(int $minutes): void
	{
		$this->minutes = $minutes;
	}

	/**
	 * @return string
	 */
	public function getNote(): ?string
	{
		return $this->note;
	}

	/**
	 * @param string $note
	 */
	public function setNote(string $note): void
	{
		$this->note = $note;
	}
}