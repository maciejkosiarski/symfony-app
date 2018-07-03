<?php


namespace App\Entity;

use App\Exception\InvalidQueueStatusException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class NotificationQueuePosition
 *
 * @package App\Entity
 * @ORM\Table(name="app_notification_queue_positions")
 * @ORM\Entity(repositoryClass="App\Repository\NotificationQueuePositionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationQueuePosition extends BaseEntity
{
	const STATUS_PENDING  = 1;
	const STATUS_SENDED   = 2;
	const STATUS_CANCELED = 3;

	/**
	 * @var Notification
	 * @ORM\ManyToOne(targetEntity="App\Entity\Notification", inversedBy="queuePositions")
	 * @ORM\JoinColumn(name="notification_id", referencedColumnName="id", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("App\Entity\Notification")
	 */
	private $notification;

	/**
	 * @var integer
	 * @ORM\Column(name="status", type="integer", length=30,  nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("integer")
	 */
	private $status;

	/**
	 * @var \DateTime
	 * @ORM\Column(name="due_date", type="datetime", nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	private $dueDate;


	public function __construct()
	{
		$this->status = self::STATUS_PENDING;
	}

	/**
	 * @return Notification
	 */
	public function getNotification(): Notification
	{
		return $this->notification;
	}

	/**
	 * @param Notification $notification
	 */
	public function setNotification(Notification $notification)
	{
		$this->notification = $notification;
	}

	/**
	 * @return int
	 */
	public function getStatus(): int
	{
		return $this->status;
	}

	/**
	 * @param int $status
	 * @throws InvalidQueueStatusException
	 * @throws \ReflectionException
	 */
	public function setStatus(int $status): void
	{
		if (!$this->isStatusValid($status)) {
			throw new InvalidQueueStatusException($status, $this->getStatusList());
		}

		$this->status = $status;
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
	public function setDueDate(\DateTime $dueDate)
	{
		$this->dueDate = $dueDate;
	}

	/**
	 * @param int $status
	 * @return bool
	 * @throws \InvalidArgumentException
	 * @throws \ReflectionException
	 */
	private function isStatusValid(int $status): bool
	{
		return in_array($status, $this->getStatusList());
	}

	/**
	 * @var int[]
	 */
	private $statusList;

	/**
	 * @return int[]
	 * @throws \ReflectionException
	 */
	private function getStatusList(): array
	{
		if (empty($this->statusList)) {
			$reflection = new \ReflectionClass(NotificationQueuePosition::class);

			foreach ($reflection->getConstants() as $constantName => $constantValue) {
				if (strpos($constantName, 'STATUS_') !== false) {
					$this->statusList[$constantName] = $constantValue;
				}
			}
		}

		return $this->statusList;
	}

	/**
	 * @return array
	 * @throws \ReflectionException
	 */
	public function getStatusLabels()
	{
		return array_map(function ($status) {
			return strtolower(str_replace('STATUS_', '', $status));
		}, array_flip($this->getStatusList()));
	}
}