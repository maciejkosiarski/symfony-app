<?php

declare(strict_types=1);

namespace App\Service\Notifier\Factory;

use App\Exception\CreateNotifierException;
use App\Service\SmsGateway;
use App\Service\Notifier\MailNotifier;
use App\Service\Notifier\Notifier;
use App\Service\Notifier\SmsNotifier;

class NotifierFactory
{
	private $mailer;

	private $smsGateway;

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
        return new SmsNotifier($this->smsGateway);
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
    public function getSmsGateway(SmsGateway $smsGateway): void
    {
        $this->smsGateway = $smsGateway;
	}
}