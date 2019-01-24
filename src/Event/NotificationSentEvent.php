<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\NotificationQueuePosition;
use Symfony\Component\EventDispatcher\Event;

class NotificationSentEvent extends Event
{
	const NAME = 'notification.sent';

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