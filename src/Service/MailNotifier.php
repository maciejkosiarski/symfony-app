<?php

namespace App\Service;

use App\Entity\Notification;
use App\Exception\NotifyException;
use Doctrine\ORM\EntityManagerInterface;

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
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * MailNotifier constructor.
	 * @param \Swift_Mailer $mailer
	 * @param EntityManagerInterface $em
	 */
	public function __construct(\Swift_Mailer $mailer, EntityManagerInterface $em)
	{
		$this->mailer = $mailer;
		$this->em = $em;
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

		$this->disableNotification($notification);
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

	/**
	 * @param Notification $notification
	 */
	private function disableNotification(Notification $notification): void
	{
		$notification->activeToggle();

		$this->em->flush();
	}
}