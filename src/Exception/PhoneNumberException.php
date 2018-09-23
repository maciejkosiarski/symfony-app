<?php

namespace App\Exception;

/**
 * Class PhoneNumberException
 * @package App\Exception
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class PhoneNumberException extends \Exception
{
    public function __construct(string $phone)
    {
        parent::__construct(sprintf('User provide invalid phone number: %s', $phone));
    }
}