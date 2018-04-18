<?php

namespace App\Service\Factory;

use App\Exception\CreateNotifierException;
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
	 * @param $name
	 * @return Notifier
	 * @throws CreateNotifierException
	 */
	public function getNotifierByName($name): Notifier
	{
		$createMethod = 'create' . ucfirst($name). 'Notifier';

		if (method_exists($this, $createMethod)) {
			return $this->{$createMethod}();
		};

		throw new CreateNotifierException($createMethod);
	}

	/**
	 * @return MailNotifier
	 */
	private function createMailNotifier(): MailNotifier
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