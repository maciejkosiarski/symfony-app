<?php

declare(strict_types=1);

namespace App\Service\Notifier;

use App\Entity\Notification;
use App\Exception\NotifyException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class MessengerNotifier implements Notifier
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @throws NotifyException
     */
    public function notify(Notification $notification): void
    {
        $this->send($notification);
    }

    public function getNotificationType(): int
    {
        return Notification::TYPE_MESSENGER;
    }

    /**
     * @throws NotifyException
     */
    private function send(Notification $notification): void
    {
        /** @var Response $response */
        $response = $this->client->post(getenv('MESSENGER_URL'), $this->getOptions($notification));

        if ($response->getStatusCode() !== 200) {
            throw new NotifyException($notification->getUser()->getUsername());
        }
    }

    private function getOptions(Notification $notification): array
    {
        return [
            'json' => [
                'recipient' => [
                    'id' => $notification->getUser()->getFacebookId(),
                ],
                'message' => [
                    'text' => $notification->getMessage(),
                ],
            ],
            'query' => ['access_token' => getenv('PAGE_ACCESS_TOKEN')],
        ];
    }
}