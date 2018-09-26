<?php

namespace App\Service;

use App\Exception\PhoneNumberException;

class Sms
{
    private $receiver;

    private $message;

    public function getReceiver(): int
    {
        return $this->receiver;
    }

    /**
     * @throws PhoneNumberException
     */
    public function setReceiver(int $receiver): void
    {
        if ($receiver < 500000000 || $receiver > 899999999) {
            throw new PhoneNumberException($receiver);
        }

        $this->receiver = $receiver;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = substr($message, 0, 200);
    }
}