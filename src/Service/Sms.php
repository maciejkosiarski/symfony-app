<?php

namespace App\Service;

use App\Exception\PhoneNumberException;
use GuzzleHttp\Client;

class Sms
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
     * @throws PhoneNumberException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(int $receiver, string $message): void
    {
        if (!is_numeric($receiver)) {
            throw new PhoneNumberException($receiver);
        }

        $this->sendRequest($this->getSendBody($receiver, $message), $this->uri . self::SEND);
    }

    private function getSendBody(int $number, string $message): string
    {
        $body = '[{';
        $body .= '"phone_number":"' . $number . '",';
        $body .= '"message":"' . $message . '",';
        $body .= '"device_id":' . $this->device;
        $body .= '}]';

        return $body;
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

    private function getHeaders(): array
    {
        return ['Authorization' => $this->token];
    }
}