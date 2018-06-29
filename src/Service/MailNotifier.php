<?php

namespace App\Service;

use App\Entity\Notification;
use App\Exception\NotifyException;

/**
 * Class MailNotifier
 * @package App\Service
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class MailNotifier implements Notifier
{
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * MailNotifier constructor.
	 * @param \Swift_Mailer $mailer
	 */
	public function __construct(\Swift_Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	/**
	 * @param Notification $notification
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

	/**
	 * @return int
	 */
	public function getNotificationType(): int
	{
		return Notification::TYPE_EMAIL;
	}

	/**
	 * @param \Swift_Message $message
	 * @throws NotifyException
	 */
	private function send(\Swift_Message $message): void
	{
		if(0 === $this->mailer->send($message)){
			throw new NotifyException(current($message->getTo()));
		};
	}
}