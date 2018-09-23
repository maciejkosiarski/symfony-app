<?php

namespace App\Service;

use App\Entity\Notification;
use App\Exception\PhoneNumberException;
use GuzzleHttp\Client;

/**
 * Class SmsNotifier
 * @package App\Service
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class SmsNotifier implements Notifier
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $device;

    /**
     * @var string
     */
    private $api;
    
    public function __construct()
    {
        $this->client = new Client();
        $this->token = getenv('SMS_AUTH_TOKEN');
        $this->device = getenv('SMS_DEVICE_ID');
        $this->api = getenv('SMS_URI');
    }

    /**
     * @param Notification $notification
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws PhoneNumberException
     */
    public function notify(Notification $notification): void
    {
        if (!is_numeric($notification->getUser()->getPhone())) {
            throw new PhoneNumberException($notification->getUser()->getPhone());
        }

        $this->client->request('POST', $this->api . '/message/send', [
            'headers' => [
                'Authorization' => $this->token
            ],
            'body' => $this->getBody($notification),
        ]);
    }

    public function getNotificationType(): int
    {
        return Notification::TYPE_SMS;
    }

    private function getBody(Notification $notification): string
    {
        $body = '[{';
        $body .= '"phone_number":"' . $notification->getUser()->getPhone() . '",';
        $body .= '"message":"' . $notification->getMessage() . '",';
        $body .= '"device_id":'. $this->device;
        $body .= '}]';

        return $body;
    }
}