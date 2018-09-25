<?php

namespace App\Service\Notifier;

use App\Entity\Notification;
use App\Exception\PhoneNumberException;
use App\Service\Sms;
use GuzzleHttp\Client;

class SmsNotifier implements Notifier
{
    private $sms;

    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws PhoneNumberException
     */
    public function notify(Notification $notification): void
    {
        $this->sms->send($notification->getUser()->getPhone(), $notification->getMessage());
    }

    public function getNotificationType(): int
    {
        return Notification::TYPE_SMS;
    }
}