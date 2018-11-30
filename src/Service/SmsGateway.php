<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;

class SmsGateway
{
    const SEND   = '/message/send';
    const CANCEL = '/message/cancel';
    const INFO   = '/message';
    const SEARCH = '/message/search';

    private $client;
    private $token;
    private $device;
    private $uri;

    public function __construct()
    {
        $this->client = new Client();
        $this->token = getenv('SMS_AUTH_TOKEN');
        $this->device = getenv('SMS_DEVICE_ID');
        $this->uri = getenv('SMS_URI');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(Sms $sms): void
    {
        $this->sendRequest($this->getSendBody($sms), $this->uri . self::SEND);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendRequest(string $body, string $endpoint, string $method = 'POST'): void
    {
        $this->client->request($method, $endpoint, [
            'headers' => $this->getHeaders(),
            'body' => $body,
        ]);
    }

    private function getSendBody(Sms $sms): string
    {
        return sprintf('[{"phone_number":"%s", "message":"%s", "device_id": "%s"}]',
            $sms->getReceiver(),
            $sms->getMessage(),
            $this->device
        );
    }

    private function getHeaders(): array
    {
        return ['Authorization' => $this->token];
    }
}