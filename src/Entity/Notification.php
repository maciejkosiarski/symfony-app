<?php


namespace App\Entity;

use App\Exception\InvalidNotificationTypeException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Notification
 *
 * @package App\Entity
 * @ORM\Table(name="app_notifications")
 * @ORM\Entity()
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class Notification extends BaseEntity
{
	const TYPE_BROWSER = 1;
	const TYPE_EMAIL   = 2;
	const TYPE_SMS 	   = 3;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
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
	 * @var string
	 * @ORM\Column(name="message", type="text", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	private $message;

	/**
	 * @var boolean
	 * @ORM\Column(name="active", type="boolean", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("boolean")
	 */
	private $active;

	/**
	 * @var boolean
	 * @ORM\Column(name="loop", type="boolean", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("boolean")
	 */
	private $loop;

	/**
	 * @var \DateTime
	 * @ORM\Column(name="due_date", type="datetime", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\DateTime()
	 */
	private $dueDate;

	/**
	 * Notification constructor.
	 *
	 * @param User $user
	 * @param int  $type
	 * @throws InvalidNotificationTypeException
	 * @throws \ReflectionException
	 */
	public function __construct(User $user, int $type)
	{	$this->user   = $user;
		$this->active = true;
		$this->loop   = false;
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
	 * @param string $type
	 * @throws InvalidNotificationTypeException
	 * @throws \ReflectionException
	 */
	public function setType(string $type): void
	{
		if (!$this->isTypeValid($type)) {
			throw new InvalidNotificationTypeException($type, $this->getTypeList());
		}

		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	public function activeToggle(): void
	{
		$this->active = !$this->active;
	}

	/**
	 * @return bool
	 */
	public function isLoop(): bool
	{
		return $this->loop;
	}

	public function loopToggle(): void
	{
		$this->loop = !$this->loop;
	}

	/**
	 * @return \DateTime
	 */
	public function getDueDate(): \DateTime
	{
		return $this->dueDate;
	}

	/**
	 * @param \DateTime $dueDate
	 */
	public function setDueDate(\DateTime $dueDate): void
	{
		$this->dueDate = $dueDate;
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
			$reflection = new \ReflectionClass(Notification::class);

			foreach ($reflection->getConstants() as $constantName => $constantValue) {
				if (strpos($constantName, 'TYPE_') !== false) {
					$this->typeList[$constantName] = $constantValue;
				}
			}
		}

		return $this->typeList;
	}

}