<?php

namespace App\Service\Notifier;

use App\Entity\Notification;
use App\Exception\NotifyException;

class MailNotifier implements Notifier
{
	private $mailer;

	public function __construct(\Swift_Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	/**
	 * @throws NotifyException
	 */
	public function notify(Notification $notification): void
	{
		$message = (new \Swift_Message(mb_substr($notification->getMessage(), 0, 15) . '...'))
			->setFrom(getenv('MAILER_FROM'))
			->setTo($notification->getUser()->getEmail())
			->setBody($notification->getMessage());

		$this->send($message);
	}

	public function getNotificationType(): int
	{
		return Notification::TYPE_EMAIL;
	}

	/**
	 * @throws NotifyException
	 */
	private function send(\Swift_Message $message): void
	{
		if(0 === $this->mailer->send($message)){
			throw new NotifyException(current($message->getTo()));
		};
	}
}