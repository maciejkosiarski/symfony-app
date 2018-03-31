<?php

namespace App\Service;

use App\Entity\Notification;

/**
 * Interface Notifier
 *
 * @package App\Service
 * @author  Maciej Kosiarski <mks@moleo.pl>
 */
interface Notifier
{
	/**
	 * Send specific types of notifications to users
	 * @param Notification $notification
	 */
	public function notify(Notification $notification): void;

	/**
	 * Get type of notifications to send
	 * @return int
	 */
	public function getNotificationType(): int;
}