<?php

namespace App\Service\Factory;

use App\Service\MailNotifier;
use App\Service\Notifier;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ReminderFactory
 * @package App\Service\Factory
 * @author  Maciej Kosiarski <mks@moleo.pl>
 */
class NotifierFactory
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
	 * @return Notifier
	 */
	public function createMailNotifier(): Notifier
	{
		return new MailNotifier($this->mailer, $this->em);
	}

	/**
	 * @param \Swift_Mailer $mailer
	 * @required
	 */
	public function getMailer(\Swift_Mailer $mailer): void
	{
		$this->mailer = $mailer;
	}

	/**
	 * @param EntityManagerInterface $em
	 * @required
	 */
	public function getEntityManager(EntityManagerInterface $em): void
	{
		$this->em = $em;
	}
}