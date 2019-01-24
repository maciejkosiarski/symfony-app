<?php

declare(strict_types=1);

namespace App\Exception;

use App\Entity\Notification;

class NotifyException extends \Exception
{
    public function __construct(Notification $notification)
    {
        $message = 'The user %s has not received his notification(id: %s, type: %s).';
        parent::__construct(sprintf(
            $message, $notification->getUser()->getUsername(), $notification->getId(), $notification->getType()
        ));
    }
}