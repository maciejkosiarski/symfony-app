<?php


namespace App\Entity;

use App\Exception\InvalidNotificationTypeException;
use Cron\CronExpression;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Notification
 *
 * @package App\Entity
 * @ORM\Table(name="app_notifications")
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\HasLifecycleCallbacks()
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class Notification extends BaseEntity
{
	const TYPE_EMAIL   = 1;
	const TYPE_BROWSER = 2;
	const TYPE_SMS 	   = 3;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="notifications")
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
	 * @Assert\Type("boolean")
	 */
	private $active;

	/**
	 * @var boolean
	 * @ORM\Column(name="recurrent", type="boolean", nullable=false)
	 * @Assert\Type("boolean")
	 */
	private $recurrent;

	/**
	 * @var string
	 * @ORM\Column(name="interval_expression", type="string", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type(type="string")
	 * @Assert\Regex(pattern="^(\*|([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])|\*\/([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])) (\*|([0-9]|1[0-9]|2[0-3])|\*\/([0-9]|1[0-9]|2[0-3])) (\*|([1-9]|1[0-9]|2[0-9]|3[0-1])|\*\/([1-9]|1[0-9]|2[0-9]|3[0-1])) (\*|([1-9]|1[0-2])|\*\/([1-9]|1[0-2])) (\*|([0-6])|\*\/([0-6]))$^", message="Interval expression has invalid format.")
	 */
	private $intervalExpression;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\NotificationQueuePosition", mappedBy="notification", cascade={"persist", "remove"})
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 */
	private $queuePositions;

	public function __construct()
	{
		$this->queuePositions = new ArrayCollection();
		$this->active         = true;
		$this->recurrent      = true;
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
	public function getType(): ?string
	{
		return $this->type;
	}

	/**
	 * @param int $type
	 * @throws InvalidNotificationTypeException
	 * @throws \ReflectionException
	 */
	public function setType(int $type): void
	{
		if (!$this->isTypeValid($type)) {
			throw new InvalidNotificationTypeException($type, $this->getTypeList());
		}

		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getMessage(): ?string
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
	 * @param bool $bool
	 */
	public function setRecurrent(bool $bool)
	{
		$this->recurrent = $bool;
	}

	/**
	 * @return bool
	 */
	public function isRecurrent(): bool
	{
		return $this->recurrent;
	}

	public function recurrentToggle(): void
	{
		$this->recurrent = !$this->recurrent;
	}

	/**
	 * @return string
	 */
	public function getIntervalExpression(): ?string
	{
		return $this->intervalExpression;
	}

	/**
	 * @param string $expression
	 */
	public function setIntervalExpression(string $expression): void
	{
		$expressions = $this->getDefaultExpressions();

		$this->intervalExpression = (key_exists($expression, $expressions)) ? $expressions[$expression] : $expression;
	}

	/**
	 * @return PersistentCollection
	 */
	public function getQueuePositions(): PersistentCollection
	{
		return $this->queuePositions;
	}

	public function addToQueue(\DateTime $dueDate)
	{
		$newQueuePosition = new NotificationQueuePosition();
		$newQueuePosition->setNotification($this);
		$newQueuePosition->setDueDate($dueDate);

		$this->queuePositions->add($newQueuePosition);
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
	public function getTypeList(): array
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

	/**
	 * @return array
	 */
	private function getDefaultExpressions(): array
	{
		return [
			'@yearly'   => '0 0 1 1 *',
			'@annually' => '0 0 1 1 *',
			'@monthly'  => '0 0 1 * *',
			'@weekly'   => '0 0 * * 0',
			'@daily'    => '0 0 * * *',
			'@hourly'   => '0 * * * *',
		];
	}

	/**
	 * @return string
	 */
	public function getPreviousRun(): string
	{
		$cronExpression = CronExpression::factory($this->intervalExpression);

		return $cronExpression->getNextRunDate()->format('Y-m-d H:i:s');
	}

	/**
	 * @return string
	 */
	public function getNextRun(): string
	{
		$cronExpression = CronExpression::factory($this->intervalExpression);

		return $cronExpression->getNextRunDate()->format('Y-m-d H:i:s');
	}

	/**
	 * @return \DateTime
	 */
	public function getDateTimeNextRun(): \DateTime
	{
		$cronExpression = CronExpression::factory($this->intervalExpression);

		$dudeDate = new \DateTime();
		$dudeDate->setTimestamp($cronExpression->getNextRunDate()->getTimestamp());
		$dudeDate->setTimezone($cronExpression->getNextRunDate()->getTimezone());

		return $dudeDate;
	}

	public function getDateTimeSpecificNextRun($run): \DateTime
	{
		$cronExpression = CronExpression::factory($this->intervalExpression);
		/** @var \DateTime[] $runDates */
		$runDates  = $cronExpression->getMultipleRunDates($run);

		$dudeDate = new \DateTime();
		$dudeDate->setTimestamp($runDates[$run - 1]->getTimestamp());
		$dudeDate->setTimezone($runDates[$run - 1]->getTimezone());

		return $dudeDate;
	}

	/**
	 * @return array
	 * @throws \ReflectionException
	 */
	public function getTypesLabels()
	{
		return array_map(function ($type) {
			return strtolower(str_replace('TYPE_', '', $type));
		}, array_flip($this->getTypeList()));
	}
}