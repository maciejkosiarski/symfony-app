<?php

namespace App\Service;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class MailNotifier
 * @package App\Service
 * @author  Maciej Kosiarski <mks@moleo.pl>
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

	public function notify(Notification $notification): void
	{
		$message = (new \Swift_Message(mb_substr($notification->getMessage(), 0, 15) . '...'))
			->setFrom(getenv('MAILER_FROM'))
			->setTo($notification->getUser()->getEmail())
			->setBody($notification->getMessage());

		$this->mailer->send($message);

		if (!$notification->isLoop()) {
			$notification->activeToggle();

			$this->em->flush();
		}
	}

	/**
	 * @return int
	 */
	public function getNotificationType(): int
	{
		return Notification::TYPE_EMAIL;
	}
}