<?php

declare(strict_types=1);

namespace App\Exception;

class NotifyException extends \Exception
{
    public function __construct(string $user)
    {
        parent::__construct(sprintf('The user %s has not received his notification', $user));
    }
}