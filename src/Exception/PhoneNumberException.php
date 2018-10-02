<?php

declare(strict_types=1);

namespace App\Exception;

class PhoneNumberException extends \Exception
{
    public function __construct(int $phone)
    {
        parent::__construct(sprintf('User provide invalid phone number: %s', $phone));
    }
}