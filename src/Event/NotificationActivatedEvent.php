<?php

namespace App\Event;

use App\Entity\Notification;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NotificationActivatedEvent
 * @package App\Event
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationActivatedEvent extends Event
{
	const NAME = 'notification.activated';

	/**
	 * @var Notification
	 */
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