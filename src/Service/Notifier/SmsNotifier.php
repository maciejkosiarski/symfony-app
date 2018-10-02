<?php

declare(strict_types=1);

namespace App\Service\Notifier;

use App\Entity\Notification;
use App\Exception\PhoneNumberException;
use App\Service\SmsGateway;
use App\Service\Sms;

class SmsNotifier implements Notifier
{
    private $gateway;

    public function __construct(SmsGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws PhoneNumberException
     */
    public function notify(Notification $notification): void
    {
        $this->gateway->send($this->createSms($notification));
    }

    public function getNotificationType(): int
    {
        return Notification::TYPE_SMS;
    }

    /**
     * @throws PhoneNumberException
     */
    private function createSms(Notification $notification): Sms
    {
        $sms = new Sms();
        $sms->setReceiver($notification->getUser()->getPhone());
        $sms->setMessage($notification->getMessage());

        return $sms;
    }
}