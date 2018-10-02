<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Notification;
use Symfony\Component\EventDispatcher\Event;

class NotificationActivatedEvent extends Event
{
	const NAME = 'notification.activated';

	protected $notification;

	public function __construct(Notification $notification)
	{
		$this->notification = $notification;
	}

	public function getNotification(): Notification
	{
		return $this->notification;
	}
}