<?php

declare(strict_types=1);

namespace App\Exception;

class CommandAlreadyRunningException extends \Exception
{
    public function __construct(string $command)
    {
        parent::__construct(sprintf('The command %s is already running in another process.', $command));
    }
}