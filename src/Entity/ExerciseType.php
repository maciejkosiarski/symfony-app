<?php


namespace App\Entity;

use App\Exception\InvalidExerciseTypeException;
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
	const TYPE_OARSMAN = 1;
	const TYPE_RUN     = 2;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("App\Entity\User")
	 */
	private $user;

	/**
	 * @var integer
	 * @ORM\Column(name="type", type="integer", length=30,  nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("integer")
	 */
	private $type;

	/**
	 * @var int
	 * @ORM\Column(name="minutes", type="integer", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("integer")
	 * @Assert\GreaterThan(0)
	 */
	private $minutes;

	/**
	 * Exercise constructor.
	 *
	 * @param User $user
	 * @param int  $type
	 * @param int  $minutes
	 * @throws InvalidExerciseTypeException
	 * @throws \ReflectionException
	 */
	public function __construct(User $user, int $type, $minutes)
	{	$this->user   = $user;
		$this->minutes = $minutes;
		$this->setType($type);
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
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param int $type
	 * @throws InvalidExerciseTypeException
	 * @throws \ReflectionException
	 */
	public function setType(int $type): void
	{
		if (!$this->isTypeValid($type)) {
			throw new InvalidExerciseTypeException($type, $this->getTypeList());
		}

		$this->type = $type;
	}

	/**
	 * @return int
	 */
	public function getMinutes(): int {
		return $this->minutes;
	}

	/**
	 * @param int $minutes
	 */
	public function setMinutes(int $minutes): void {
		$this->minutes = $minutes;
	}

	/**
	 * @param int $type
	 * @return bool
	 * @throws \InvalidArgumentException
	 * @throws \ReflectionException
	 */
	private function isTypeValid(int $type): bool
	{
		return in_array($type, $this->getTypeList());
	}


	/**
	 * @var int[]
	 */
	private $typeList;

	/**
	 * @return int[]
	 * @throws \ReflectionException
	 */
	private function getTypeList(): array
	{
		if (empty($this->typeList)) {
			$reflection = new \ReflectionClass(Exercise::class);

			foreach ($reflection->getConstants() as $constantName => $constantValue) {
				if (strpos($constantName, 'TYPE_') !== false) {
					$this->typeList[$constantName] = $constantValue;
				}
			}
		}

		return $this->typeList;
	}

}