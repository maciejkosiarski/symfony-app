<?php

namespace App\Event;

use App\Entity\NotificationQueuePosition;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NotificationSendedEvent
 * @package App\Event
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationSendedEvent extends Event
{
	const NAME = 'notification.sended';

	/**
	 * @var NotificationQueuePosition
	 */
	protected $queuePosition;

	public function __construct(NotificationQueuePosition $queuePosition)
	{
		$this->queuePosition = $queuePosition;
	}

	public function getNotificationQueuePosition(): NotificationQueuePosition
	{
		return $this->queuePosition;
	}
}