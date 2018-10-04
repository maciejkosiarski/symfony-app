<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NotifyException;

class Mail
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws NotifyException
     */
    public function send($subject, $to, $body): void
    {
        if (0 === $this->mailer->send($this->prepareMessage($subject, $to, $body))){
            throw new NotifyException($to);
        };
    }

    private function prepareMessage(string $subject, string $to, string $body): \Swift_Message
    {
        return (new \Swift_Message($subject))
            ->setFrom(getenv('MAILER_FROM'))
            ->setTo($to)
            ->setBody($body);
    }
}