<?php

namespace App\Service\Notifier\Factory;

use App\Exception\CreateNotifierException;
use App\Service\Notifier\MailNotifier;
use App\Service\Notifier\Notifier;
use App\Service\Notifier\SmsNotifier;
use App\Service\Sms;

class NotifierFactory
{
	private $mailer;

	private $sms;

	/**
	 * @throws CreateNotifierException
	 */
	public function getNotifierByName(string $name): Notifier
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
        return new SmsNotifier($this->sms);
	}

	/**
	 * @required
	 */
	public function getMailer(\Swift_Mailer $mailer): void
	{
		$this->mailer = $mailer;
	}

    /**
     * @required
     */
    public function getSmsClient(Sms $sms): void
    {
        $this->sms = $sms;
	}
}