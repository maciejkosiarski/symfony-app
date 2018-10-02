<?php

declare(strict_types=1);

namespace App\Service\Notifier;

use App\Entity\Notification;

interface Notifier
{
	/**
	 * Send specific types of notifications to users
	 */
	public function notify(Notification $notification): void;

	public function getNotificationType(): int;
}