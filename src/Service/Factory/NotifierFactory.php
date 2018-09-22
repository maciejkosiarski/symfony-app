<?php

namespace App\Service\Factory;

use App\Exception\CreateNotifierException;
use App\Service\MailNotifier;
use App\Service\Notifier;
use App\Service\SmsNotifier;

/**
 * Class ReminderFactory
 * @package App\Service\Factory
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotifierFactory
{
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

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

	private function createMailNotifier(): MailNotifier
	{
		return new MailNotifier($this->mailer);
	}

    private function createSmsNotifier(): SmsNotifier
    {
        return new SmsNotifier();
	}

	/**
	 * @param \Swift_Mailer $mailer
	 * @required
	 */
	public function getMailer(\Swift_Mailer $mailer): void
	{
		$this->mailer = $mailer;
	}
}