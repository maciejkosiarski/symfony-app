<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\NotificationQueuePosition;
use Symfony\Component\EventDispatcher\Event;

class NotificationSendedEvent extends Event
{
	const NAME = 'notification.sended';

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